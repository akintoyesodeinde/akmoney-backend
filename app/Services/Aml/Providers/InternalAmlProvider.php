<?php

namespace App\Services\Aml\Providers;

use App\Models\User;
use App\Services\Aml\Contracts\AmlProviderInterface;

class InternalAmlProvider implements AmlProviderInterface
{
    /**
     * Simple name-based screening against a local keyword list.
     * In production, replace with an OFAC SDN API or Refinitiv call.
     */
    public function screenUser(User $user): array
    {
        $name = strtolower(trim($user->firstname . ' ' . $user->lastname));
        $hits = $this->matchAgainstList($name);

        return [
            'status'   => count($hits) > 0 ? 'hit' : 'clear',
            'matches'  => $hits,
            'provider' => $this->getProviderName(),
        ];
    }

    public function screenTransaction(array $txData): array
    {
        return [
            'status'   => 'clear',
            'matches'  => [],
            'provider' => $this->getProviderName(),
        ];
    }

    public function getProviderName(): string
    {
        return 'internal';
    }

    /**
     * Match a full name against a minimal internal watchlist.
     * Extend this list or replace with a JSON file for production use.
     */
    private function matchAgainstList(string $name): array
    {
        // Placeholder watchlist — replace with OFAC SDN JSON import or API
        $watchlist = [];

        $hits = [];
        foreach ($watchlist as $entry) {
            if (str_contains($name, strtolower($entry))) {
                $hits[] = ['list' => 'internal_watchlist', 'name' => $entry];
            }
        }

        return $hits;
    }
}
