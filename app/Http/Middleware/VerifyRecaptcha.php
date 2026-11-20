<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Extension;
use App\Models\RecaptchaLog;
use App\Constants\Status;

class VerifyRecaptcha
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') === 'local' || in_array($request->getHost(), ['localhost', '127.0.0.1'])) {
            return $next($request);
        }
        $extension = Extension::where('act', 'google-recaptcha2')->where('status', Status::ENABLE)->first();

        if (!$extension) {
            return $next($request);
        }

        $captcha = $request->input('g-recaptcha-response');

        if (!$captcha) {
            $this->writeLog($request, false, ['missing-input-response']);
            Log::warning('reCAPTCHA v2: missing token', [
                'ip'  => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
            return back()->withErrors(['captcha' => 'Captcha is required']);
        }

        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret'   => $extension->shortcode->secret_key->value,
                'response' => $captcha,
                'remoteip' => $request->ip(),
            ]
        );

        $result = $response->json();

        $errorCodes = $result['error-codes'] ?? [];

        $score = isset($result['score']) ? (float) $result['score'] : null;

        if (empty($result['success'])) {
            $this->writeLog($request, false, $errorCodes, $score);
            Log::warning('reCAPTCHA v2: verification failed', [
                'ip'          => $request->ip(),
                'url'         => $request->fullUrl(),
                'error-codes' => $errorCodes,
            ]);
            return back()->withErrors(['captcha' => 'Captcha verification failed']);
        }

        $this->writeLog($request, true, $errorCodes, $score);

        return $next($request);
    }

    private function writeLog(Request $request, bool $success, array $errorCodes, ?float $score = null): void
    {
        try {
            RecaptchaLog::create([
                'ip'          => $request->ip(),
                'url'         => substr($request->fullUrl(), 0, 500),
                'success'     => $success,
                'score'       => $score,
                'error_codes' => $errorCodes ? implode(',', $errorCodes) : null,
                'user_agent'  => substr($request->userAgent() ?? '', 0, 500),
            ]);
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA v2: failed to write log — ' . $e->getMessage());
        }
    }
}
