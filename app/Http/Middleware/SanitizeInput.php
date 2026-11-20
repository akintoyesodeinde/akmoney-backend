<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    // Fields that should never be stripped (passwords, tokens, etc.)
    private array $skip = [
        'password',
        'password_confirmation',
        'current_password',
        'g-recaptcha-response',
        '_token',
        'captcha_secret',
    ];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->except($this->skip);

        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Strip null bytes
                $value = str_replace("\0", '', $value);
                // Remove HTML tags from non-rich-text inputs
                $value = strip_tags($value);
                // Trim whitespace
                $value = trim($value);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
