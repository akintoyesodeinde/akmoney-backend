<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestSizeLimit
{
    // 10 MB default limit for regular requests
    const MAX_BYTES = 10 * 1024 * 1024;

    // 50 MB for file upload endpoints
    const MAX_UPLOAD_BYTES = 50 * 1024 * 1024;

    private array $uploadPaths = [
        'user/kyc-submit',
        'user/profile',
        'ticket',
    ];

    public function handle(Request $request, Closure $next)
    {
        $contentLength = (int) $request->header('Content-Length', 0);

        $limit = $this->isUploadPath($request) ? self::MAX_UPLOAD_BYTES : self::MAX_BYTES;

        if ($contentLength > $limit) {
            abort(413, 'Request entity too large.');
        }

        return $next($request);
    }

    private function isUploadPath(Request $request): bool
    {
        foreach ($this->uploadPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return true;
            }
        }
        return false;
    }
}
