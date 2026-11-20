<?php

namespace App\Models;

use App\Constants\AdminRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AdminNotification extends Model
{
    protected $fillable = ['title', 'click_url', 'module', 'triggered_by', 'user_id', 'agent_id', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function triggeredBy()
    {
        return $this->belongsTo(Admin::class, 'triggered_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Scope notifications visible to the currently authenticated admin.
     * Super Admin sees everything; others see only their accessible modules.
     */
    public function scopeForCurrentAdmin($query)
    {
        /** @var Admin|null $admin */
        $admin = Auth::guard('admin')->user();

        if (!$admin instanceof Admin || $admin->isSuperAdmin()) {
            return $query;
        }

        // Get modules this admin can view
        $accessibleModules = AdminRolePermission::where('role', $admin->role)
            ->where('can_view', true)
            ->pluck('module')
            ->toArray();

        // Also infer module from click_url for legacy notifications that have no module set
        return $query->where(function ($q) use ($accessibleModules) {
            $q->whereIn('module', $accessibleModules)
              ->orWhere(function ($q2) use ($accessibleModules) {
                  $q2->whereNull('module')
                     ->where(function ($q3) use ($accessibleModules) {
                         foreach ($accessibleModules as $module) {
                             $patterns = static::urlPatternsForModule($module);
                             foreach ($patterns as $pattern) {
                                 $q3->orWhere('click_url', 'like', '%' . $pattern . '%');
                             }
                         }
                     });
              });
        });
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Infer the module key from a click_url path (e.g. "/admin/users/detail/3").
     */
    public static function moduleFromUrl(?string $url): ?string
    {
        if (!$url || $url === '#') return null;

        foreach (static::URL_MODULE_MAP as $segment => $module) {
            if (str_contains($url, $segment)) {
                return $module;
            }
        }

        return null;
    }

    /**
     * Return URL path segments that map to a given module (for LIKE query filtering).
     */
    public static function urlPatternsForModule(string $module): array
    {
        $map = array_flip(static::URL_MODULE_MAP);
        // One module can match multiple URL segments
        return array_keys(array_filter($map, fn($m) => $m === $module));
    }

    // URL segment → module mapping (order matters — more specific first)
    const URL_MODULE_MAP = [
        '/admin/ticket'      => 'support_ticket',
        '/admin/compliance'  => 'compliance',
        '/admin/send-money'  => 'send_money',
        '/admin/deposit'     => 'agent_deposits',
        '/admin/withdraw'    => 'withdrawals',
        '/admin/payment'     => 'payments',
        '/admin/agents'      => 'manage_agents',
        '/admin/users'       => 'manage_users',
        '/admin/security'    => 'security_logs',
        '/admin/country'     => 'manage_countries',
        '/admin/admins'      => 'manage_admins',
    ];
}
