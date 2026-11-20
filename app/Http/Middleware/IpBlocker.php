<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\GeneralSetting;

class IpBlocker
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        [$blacklist, $whitelist] = Cache::remember('ip_filter_lists', 300, function () {
            $gs = GeneralSetting::first();
            $blacklist = $this->parseIpList($gs->ip_blacklist ?? '');
            $whitelist = $this->parseIpList($gs->ip_whitelist ?? '');
            return [$blacklist, $whitelist];
        });

        // If a whitelist is configured, only allow those IPs
        if (!empty($whitelist) && !$this->ipMatches($ip, $whitelist)) {
            abort(403, 'Access denied.');
        }

        // Block blacklisted IPs
        if ($this->ipMatches($ip, $blacklist)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }

    private function parseIpList(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }
        return array_filter(array_map('trim', preg_split('/[\r\n,]+/', $raw)));
    }

    private function ipMatches(?string $ip, array $list): bool
    {
        if (empty($ip)) {
            return false;
        }
        foreach ($list as $entry) {
            // Exact match
            if ($ip === $entry) {
                return true;
            }
            // CIDR match (e.g. 192.168.1.0/24)
            if (str_contains($entry, '/') && $this->ipInCidr($ip, $entry)) {
                return true;
            }
            // Wildcard match (e.g. 192.168.1.*)
            if (str_contains($entry, '*')) {
                $pattern = '/^' . str_replace('\\*', '.*', preg_quote($entry, '/')) . '$/';
                if (preg_match($pattern, $ip)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return false;
        }
        $ip     = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask   = -1 << (32 - (int) $bits);
        return ($ip & $mask) === ($subnet & $mask);
    }
}
