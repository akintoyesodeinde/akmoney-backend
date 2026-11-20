<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;

class KycMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $module = null)
    {
        if ($request->routeIs(
            'user.kyc.form',
            'user.kyc.submit',
            'user.kyc.data',
            'user.download.attachment',
            'user.view.attachment'
        )) {
            return $next($request);
        }

        if (!gs('kv')) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user->kv == Status::KYC_VERIFIED) {
            return $next($request);
        }

        // User is not KYC-approved — block the action
        if ($request->is('api/*')) {
            $kycStatus = $user->getKycStatus();

            $message = match ($user->kv) {
                Status::KYC_PENDING  => 'Your identity verification is under review. You will be notified once approved.',
                Status::KYC_REJECTED => 'Your identity verification was rejected. Please re-submit your documents before proceeding.',
                default              => 'Identity verification (KYC) is required before you can perform this action.',
            };

            return response()->json([
                'remark'  => 'kyc_required',
                'status'  => 'error',
                'message' => ['error' => [$message]],
                'data'    => [
                    'kyc_status'  => $kycStatus,
                    'redirect_to' => 'kyc_screen',
                ],
            ]);
        }

        // Web fallback
        if ($user->kv == Status::KYC_UNVERIFIED) {
            $notify[] = ['error', 'Please complete your KYC verification'];
            return to_route('user.kyc.form')->withNotify($notify);
        }

        if ($user->kv == Status::KYC_PENDING) {
            $notify[] = ['warning', 'Your KYC documents are under review. Please wait for admin approval.'];
            return to_route('user.kyc.data')->withNotify($notify);
        }

        if ($user->kv == Status::KYC_REJECTED) {
            $notify[] = ['error', 'Your KYC was rejected. Please re-submit your documents.'];
            return to_route('user.kyc.form')->withNotify($notify);
        }

        return $next($request);
    }
}
