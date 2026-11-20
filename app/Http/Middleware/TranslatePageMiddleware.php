<?php

namespace App\Http\Middleware;

use App\Services\PageTranslator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Post-processes HTML responses to translate their visible text.
 *
 * Performance strategy
 * ─────────────────────
 * On the FIRST request for a given URL + locale we run the full translation
 * pipeline (DOM parse → API call → string cache), then store the entire
 * translated HTML body in a page-level cache entry.
 *
 * Every SUBSEQUENT request for the same URL + locale is served entirely from
 * the page cache — no DOM parsing, no API calls, no per-string lookups.
 *
 * CSRF safety: before caching we replace every occurrence of the live CSRF
 * token with a placeholder (__XCSRF__). On cache read we swap the placeholder
 * back to the current session's token, so every user always gets a valid token.
 *
 * Page caching is only applied to anonymous GET requests. Authenticated users'
 * pages are translated but not cached (they may contain user-specific content).
 *
 * Skipped entirely when:
 *  - Locale is English
 *  - Response is not text/html
 *  - Request is AJAX / JSON
 *  - Request targets admin/*
 */
class TranslatePageMiddleware
{
    private const PAGE_CACHE_TTL  = 3600 * 6; // 6 hours
    private const CSRF_PLACEHOLDER = '__XCSRF__';

    public function __construct(private PageTranslator $translator) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process full HTML responses
        if (!str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        // Skip admin, AJAX, JSON
        if ($request->is('admin/*') || $request->ajax() || $request->wantsJson()) {
            return $response;
        }

        $locale = app()->getLocale();
        if ($locale === 'en') {
            return $response;
        }

        // ── Page-level cache (GET, unauthenticated only) ──────────────────
        $usePageCache = $request->isMethod('GET') && !auth()->check();

        if ($usePageCache) {
            $pageKey = $this->pageKey($request, $locale);
            $cached  = Cache::get($pageKey);

            if ($cached !== null) {
                // Restore the live CSRF token and serve instantly
                $body = str_replace(self::CSRF_PLACEHOLDER, csrf_token(), $cached);
                $response->setContent($body);
                return $response;
            }
        }

        // ── Translate ─────────────────────────────────────────────────────
        $html       = $response->getContent();
        $translated = $this->translator->translateHtml($html, $locale);

        // ── Store in page cache ───────────────────────────────────────────
        if ($usePageCache) {
            $token     = csrf_token();
            $cacheable = $token ? str_replace($token, self::CSRF_PLACEHOLDER, $translated) : $translated;
            Cache::put($this->pageKey($request, $locale), $cacheable, self::PAGE_CACHE_TTL);
        }

        $response->setContent($translated);
        return $response;
    }

    private function pageKey(Request $request, string $locale): string
    {
        return 'xlate.page.' . $locale . '.' . md5($request->fullUrl());
    }
}
