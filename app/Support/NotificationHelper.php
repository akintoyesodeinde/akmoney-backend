<?php

namespace App\Support;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationHelper
{
    public static function get(int $limit = 10): array
    {
        if (!Auth::check()) {
            return [
                'items' => collect(),
                'unread_count' => 0,
            ];
        }

        $baseQuery = Notification::where(function ($q) {
            $q->where('user_id', Auth::id())
              ->orWhere(function ($q2) {
                  $q2->whereNull('user_id')
                     ->where('model_type', 'App\Models\User')
                     ->where('model_id', Auth::id());
              });
        });

        $items = $baseQuery->latest()->limit($limit)->get()->map(function ($note) {
            $note->display = self::resolveDisplay($note);
            return $note;
        });

        return [
            'items'        => $items,
            'unread_count' => (clone $baseQuery)->where('is_read', 0)->count(),
        ];
    }

    /**
     * Map model changes to a human-readable title, message, icon, and colour.
     * Returns an array: [title, message, icon, color].
     */
    public static function resolveDisplay(Notification $note): array
    {
        $changes = $note->meta['changes'] ?? [];

        // Skip purely internal fields
        $internal = ['tv', 'updated_at', 'ver_code', 'ver_code_send_at'];
        $meaningful = array_diff_key($changes, array_flip($internal));

        // Try each meaningful change key in priority order
        foreach ($meaningful as $key => $value) {
            $resolved = self::mapChange($key, $value);
            if ($resolved) {
                return $resolved;
            }
        }

        // Fallback: use stored title/message if present
        if ($note->title && $note->title !== 'User updated') {
            return [
                'title'   => $note->title,
                'message' => $note->message ?? '',
                'icon'    => 'ri-notification-3-line',
                'color'   => '#6366f1',
            ];
        }

        return [
            'title'   => 'Account activity',
            'message' => 'Your account was updated.',
            'icon'    => 'ri-user-settings-line',
            'color'   => '#94a3b8',
        ];
    }

    private static function mapChange(string $key, $value): ?array
    {
        return match ($key) {
            'kv' => match ((int)$value) {
                1 => ['title' => 'KYC Approved', 'message' => 'Your identity verification has been approved. You now have full access.', 'icon' => 'ri-shield-check-line', 'color' => '#10b981'],
                3 => ['title' => 'KYC Rejected', 'message' => 'Your identity verification was rejected. Please re-submit your documents.', 'icon' => 'ri-shield-cross-line', 'color' => '#ef4444'],
                default => ['title' => 'KYC Required', 'message' => 'Please complete identity verification to access all features.', 'icon' => 'ri-shield-line', 'color' => '#f59e0b'],
            },
            'ts' => (int)$value === 1
                ? ['title' => '2FA Enabled', 'message' => 'Two-factor authentication has been enabled on your account.', 'icon' => 'ri-shield-keyhole-line', 'color' => '#3b82f6']
                : ['title' => '2FA Disabled', 'message' => 'Two-factor authentication has been turned off. Re-enable it to keep your account secure.', 'icon' => 'ri-shield-line', 'color' => '#f59e0b'],
            'two_fa_method' => ['title' => '2FA Method Changed', 'message' => '2FA method updated to: ' . ucfirst((string)$value) . '.', 'icon' => 'ri-settings-3-line', 'color' => '#3b82f6'],
            'force_tfa' => (int)$value === 1
                ? ['title' => 'Security Requirement Added', 'message' => 'Your account now requires two-factor authentication to be set up.', 'icon' => 'ri-alarm-warning-line', 'color' => '#ef4444']
                : null,
            'status' => (int)$value === 1
                ? ['title' => 'Account Reactivated', 'message' => 'Your account has been reactivated. Welcome back!', 'icon' => 'ri-checkbox-circle-line', 'color' => '#10b981']
                : ['title' => 'Account Suspended', 'message' => 'Your account has been suspended. Please contact support.', 'icon' => 'ri-error-warning-line', 'color' => '#ef4444'],
            'ev' => (int)$value === 1
                ? ['title' => 'Email Verified', 'message' => 'Your email address has been successfully verified.', 'icon' => 'ri-mail-check-line', 'color' => '#10b981']
                : null,
            'sv' => (int)$value === 1
                ? ['title' => 'Phone Verified', 'message' => 'Your phone number has been successfully verified.', 'icon' => 'ri-phone-line', 'color' => '#10b981']
                : null,
            'image' => ['title' => 'Profile Photo Updated', 'message' => 'Your profile photo has been changed successfully.', 'icon' => 'ri-user-3-line', 'color' => '#6366f1'],
            'balance' => ['title' => 'Wallet Balance Updated', 'message' => 'Your wallet balance has changed.', 'icon' => 'ri-wallet-3-line', 'color' => '#6366f1'],
            'email' => ['title' => 'Email Address Changed', 'message' => 'Your account email address has been updated.', 'icon' => 'ri-mail-line', 'color' => '#f59e0b'],
            'mobile' => ['title' => 'Phone Number Changed', 'message' => 'Your account phone number has been updated.', 'icon' => 'ri-phone-line', 'color' => '#f59e0b'],
            'firstname', 'lastname' => ['title' => 'Name Updated', 'message' => 'Your profile name has been changed.', 'icon' => 'ri-user-line', 'color' => '#6366f1'],
            'kyc_approve_reason' => null, // skip, covered by kv
            default => null,
        };
    }

    public static function markAsRead(?int $id = null): void
    {
        if (!Auth::check()) {
            return;
        }

        // Mark notifications that belong to this user — either by user_id
        // or by model_id (older records where user_id was not set on creation).
        $query = Notification::where(function ($q) {
            $q->where('user_id', Auth::id())
              ->orWhere(function ($q2) {
                  $q2->whereNull('user_id')
                     ->where('model_type', 'App\Models\User')
                     ->where('model_id', Auth::id());
              });
        })->where('is_read', 0);

        if ($id) {
            $query->where('id', $id);
        }

        $query->update(['is_read' => 1]);
    }
}
