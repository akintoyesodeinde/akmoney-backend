<?php

namespace App\Services;

use App\Models\Extension;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Translates rendered HTML pages via Google Translate (unofficial API).
 *
 * Strategy
 * --------
 * 1. Extract only the <body> text so the <head>, scripts and styles are never touched.
 * 2. Walk the DOM with DOMXPath, skipping script / style / svg / code / pre nodes
 *    and any element marked translate="no" or data-notranslate.
 * 3. Collect unique text strings, check the cache, then send uncached strings to
 *    Google Translate in batches of up to BATCH_SIZE strings joined by newlines.
 * 4. Cache every translated string individually for CACHE_TTL seconds.
 * 5. Apply translations back to the DOM and re-serialise only the body fragment.
 */
class PageTranslator
{
    private const BATCH_SIZE      = 100;  // Cloud Translation API limit is 128 segments per request
    private const FREE_BATCH_SIZE = 20;   // free tier: smaller batches reduce 429 rate-limit hits
    private const CACHE_TTL       = 86400 * 1;
    private const API_TIMEOUT     = 12;
    private const SKIP_TAGS       = ['script', 'style', 'noscript', 'textarea', 'svg', 'code', 'pre'];

    // ──────────────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Translate a full HTML document and return the modified HTML.
     * Returns the original HTML untouched on any failure.
     */
    public function translateHtml(string $html, string $locale): string
    {
        if ($locale === 'en') {
            return $html;
        }

        // Capture everything between (and including) <body …> … </body>
        if (!preg_match('/(<body[^>]*>)(.*?)(<\/body>)/si', $html, $m)) {
            return $html;
        }

        [$full, $openTag, $body, $closeTag] = $m;

        try {
            $translatedBody = $this->processFragment($body, $locale);
        } catch (\Throwable $e) {
            Log::warning('[PageTranslator] ' . $e->getMessage());
            return $html;
        }

        // Replace exactly the matched body block in the full HTML
        return substr_replace(
            $html,
            $openTag . $translatedBody . $closeTag,
            strpos($html, $full),
            strlen($full)
        );
    }

    // ──────────────────────────────────────────────────────────────────────
    // HTML fragment processing
    // ──────────────────────────────────────────────────────────────────────

    private function processFragment(string $fragment, string $locale): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Wrap in a sentinel div so we can easily extract the children later
        $dom->loadHTML(
            '<?xml encoding="UTF-8">' .
            '<html><body><div id="__pt__">' . $fragment . '</div></body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        // Build XPath: skip all undesirable ancestors
        $skipParts = array_map(
            fn ($tag) => "not(ancestor::{$tag})",
            self::SKIP_TAGS
        );
        $skipParts[] = "not(ancestor::*[@translate='no'])";
        $skipParts[] = "not(ancestor::*[@data-notranslate])";
        $xpathExpr   = '//text()[' . implode(' and ', $skipParts) . ']';

        $xpath     = new DOMXPath($dom);
        $nodeList  = $xpath->query($xpathExpr);

        // Collect translatable items (text nodes that contain at least one letter)
        $items = [];
        foreach ($nodeList as $node) {
            $raw     = $node->nodeValue;
            $trimmed = trim($raw);
            if ($trimmed === '' || !preg_match('/\p{L}/u', $trimmed)) {
                continue;
            }
            $items[] = ['node' => $node, 'text' => $trimmed, 'raw' => $raw];
        }

        if (empty($items)) {
            return $fragment;
        }

        // Translate unique strings (with caching + batching)
        $unique       = array_values(array_unique(array_column($items, 'text')));
        $translations = $this->batchTranslate($unique, $locale);

        // Apply back to DOM nodes, preserving any surrounding whitespace
        foreach ($items as ['node' => $node, 'text' => $text, 'raw' => $raw]) {
            if (!isset($translations[$text])) {
                continue;
            }
            $leadLen  = strlen($raw) - strlen(ltrim($raw));
            $trailLen = strlen($raw) - strlen(rtrim($raw));
            $node->nodeValue =
                ($leadLen  > 0 ? substr($raw, 0, $leadLen)  : '') .
                $translations[$text] .
                ($trailLen > 0 ? substr($raw, -$trailLen)   : '');
        }

        // Re-serialise only the children of our sentinel wrapper.
        // Use XPath instead of getElementById — more reliable with LIBXML_HTML_NOIMPLIED.
        $roots = $xpath->query('//div[@id="__pt__"]');
        $root  = $roots->item(0);
        if (!$root) {
            return $fragment;
        }

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return $out;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Translation + caching
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Translate an array of unique strings, using the cache and batching
     * uncached strings into groups of BATCH_SIZE to minimise API calls.
     *
     * @param  string[] $texts
     * @return array<string,string>  original → translated
     */
    private function batchTranslate(array $texts, string $locale): array
    {
        $results = [];
        $toFetch = [];

        foreach ($texts as $text) {
            $cached = Cache::get($this->cacheKey($text, $locale));
            if ($cached !== null) {
                $results[$text] = $cached;
            } else {
                $toFetch[] = $text;
            }
        }

        // Use a smaller chunk size for the free tier to avoid payload limits.
        $hasKey    = $this->resolveApiKey() !== null;
        $chunkSize = $hasKey ? self::BATCH_SIZE : self::FREE_BATCH_SIZE;

        foreach (array_chunk($toFetch, $chunkSize) as $batch) {
            $fetched = $this->callApi($batch, $locale);
            foreach ($fetched as $orig => $trans) {
                $results[$orig] = $trans;
                Cache::put($this->cacheKey($orig, $locale), $trans, self::CACHE_TTL);
            }
        }

        return $results;
    }

    /**
     * Route the batch to the official Cloud Translation API (when an API key
     * is configured in the Google Translate extension) or fall back to the
     * free unofficial tier.
     *
     * @param  string[] $texts
     * @return array<string,string>
     */
    private function callApi(array $texts, string $locale): array
    {
        $apiKey = $this->resolveApiKey();

        return $apiKey
            ? $this->callOfficialApi($texts, $locale, $apiKey)
            : $this->callFreeApi($texts, $locale);
    }

    /**
     * Google Cloud Translation API v2 (official).
     * Supports true array queries — each string maps 1-to-1 in the response,
     * so no newline-splitting hacks are needed.
     *
     * @param  string[] $texts
     * @return array<string,string>
     */
    private function callOfficialApi(array $texts, string $locale, string $apiKey): array
    {
        try {
            $response = Http::timeout(self::API_TIMEOUT)
                ->post('https://translation.googleapis.com/language/translate/v2?key=' . $apiKey, [
                    'q'      => $texts,
                    'source' => 'en',
                    'target' => $locale,
                    'format' => 'text',
                ]);

            if (!$response->ok()) {
                $reason = $response->json('error.message') ?? $response->body();
                Log::warning('[PageTranslator] Official API HTTP ' . $response->status() . ': ' . $reason);
                return $this->callFreeApi($texts, $locale); // graceful fallback
            }

            $translations = $response->json('data.translations', []);

            $result = [];
            foreach ($texts as $i => $original) {
                $t = trim($translations[$i]['translatedText'] ?? '');
                if ($t !== '') {
                    $result[$original] = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
            return $result;

        } catch (\Throwable $e) {
            Log::warning('[PageTranslator] Official API error: ' . $e->getMessage());
            return $this->callFreeApi($texts, $locale);
        }
    }

    /**
     * Google Translate free (unofficial) tier.
     * Uses POST to avoid URL length limits (GET with a large `q` fails silently).
     * Strings are joined with "\n"; if Google merges lines we fall back to per-string calls.
     *
     * @param  string[] $texts
     * @return array<string,string>
     */
    private function callFreeApi(array $texts, string $locale): array
    {
        $joined = implode("\n", $texts);

        try {
            $response = Http::timeout(self::API_TIMEOUT)
                ->asForm()
                ->post('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl'     => 'en',
                    'tl'     => $locale,
                    'dt'     => 't',
                    'q'      => $joined,
                ]);

            // 429 = rate limited — wait 1 s and retry once
            if ($response->status() === 429) {
                sleep(1);
                $response = Http::timeout(self::API_TIMEOUT)
                    ->asForm()
                    ->post('https://translate.googleapis.com/translate_a/single', [
                        'client' => 'gtx',
                        'sl'     => 'en',
                        'tl'     => $locale,
                        'dt'     => 't',
                        'q'      => $joined,
                    ]);
            }

            if (!$response->ok()) {
                Log::warning('[PageTranslator] Free API HTTP ' . $response->status() . ' for locale=' . $locale);
                return [];
            }

            $data = $response->json();
            if (empty($data[0])) {
                return [];
            }

            $translatedJoined = collect($data[0])->pluck(0)->join('');
            $parts            = explode("\n", $translatedJoined);

        } catch (\Throwable $e) {
            Log::warning('[PageTranslator] Free API error: ' . $e->getMessage());
            return [];
        }

        if (count($parts) === count($texts)) {
            $result = [];
            foreach ($texts as $i => $original) {
                $t = trim($parts[$i]);
                if ($t !== '') {
                    $result[$original] = $t;
                }
            }
            return $result;
        }

        // Count mismatch — translate individually
        $result = [];
        foreach ($texts as $original) {
            $t = $this->callFreeApiSingle($original, $locale);
            if ($t !== null) {
                $result[$original] = $t;
            }
        }
        return $result;
    }

    /** Translate a single string via the free tier; returns null on failure. */
    private function callFreeApiSingle(string $text, string $locale): ?string
    {
        try {
            $response = Http::timeout(self::API_TIMEOUT)
                ->asForm()
                ->post('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl'     => 'en',
                    'tl'     => $locale,
                    'dt'     => 't',
                    'q'      => $text,
                ]);

            if (!$response->ok()) {
                return null;
            }

            $data       = $response->json();
            $translated = collect($data[0] ?? [])->pluck(0)->join('');
            return $translated !== '' ? $translated : null;

        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Resolve the Google Translate API key from the extensions table.
     * Cached for 1 hour to avoid hitting the DB on every request.
     */
    private function resolveApiKey(): ?string
    {
        return Cache::remember('xlate.api_key', 3600, function () {
            $ext = Extension::where('act', 'google-translate')
                ->where('status', 1)
                ->first();

            $key = $ext?->shortcode?->api_key?->value ?? '';
            return $key !== '' ? $key : null;
        });
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    private function cacheKey(string $text, string $locale): string
    {
        return 'xlate.' . $locale . '.' . md5($text);
    }
}
