<?php

namespace App\Services\Aml\Contracts;

use App\Models\User;

interface AmlProviderInterface
{
    /**
     * Screen a user against sanctions/PEP lists.
     *
     * @return array{status: string, matches: array, provider: string}
     *               status: 'clear' | 'hit' | 'pending'
     */
    public function screenUser(User $user): array;

    /**
     * Screen a transaction payload for suspicious patterns.
     *
     * @param  array  $txData  Keys: amount, currency, destination_country, type
     * @return array{status: string, matches: array, provider: string}
     */
    public function screenTransaction(array $txData): array;

    /**
     * Provider identifier stored in sanctions_screening_results.provider.
     */
    public function getProviderName(): string;
}
