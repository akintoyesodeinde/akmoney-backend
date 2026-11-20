<?php

namespace App\Services\Aml;

use App\Models\AmlRiskScore;
use App\Models\AdminNotification;
use App\Models\SanctionsScreeningResult;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Aml\Contracts\AmlProviderInterface;
use App\Services\Aml\Providers\InternalAmlProvider;
use Carbon\Carbon;

class AmlRiskService
{
    private AmlProviderInterface $provider;

    public function __construct()
    {
        $this->provider = $this->resolveProvider();
    }

    /**
     * Score a transaction model (SendMoney, Deposit, Withdrawal) for AML risk.
     * Saves an AmlRiskScore record and notifies admin if high/critical.
     */
    public function scoreTransaction(object $model, string $type): AmlRiskScore
    {
        $user    = User::find($model->user_id);
        $score   = 0;
        $factors = [];

        // Resolve amount and destination country
        $amount  = $this->resolveAmount($model, $type);
        $country = $this->resolveCountry($model, $type);

        // --- Rule 1: FINTRAC large-cash threshold ($10,000 CAD) ---
        $highThreshold = config('aml.thresholds.high_amount', 10000);
        if ($amount >= $highThreshold) {
            $score   += 40;
            $factors[] = "Amount ≥ \${$highThreshold} CAD (FINTRAC reporting threshold)";
        } elseif ($amount >= config('aml.thresholds.medium_amount', 3000)) {
            // --- Rule 2: Medium-risk amount ($3,000–$9,999) ---
            $score   += 15;
            $factors[] = "Amount ≥ \$3,000 CAD";
        }

        // --- Rule 3: Round amount structuring indicator ---
        if ($amount > 0 && fmod($amount, 100) === 0.0 && $amount >= 1000) {
            $score   += 10;
            $factors[] = "Round amount (structuring indicator)";
        }

        // --- Rule 4: Velocity — > 5 transactions in 24 hours ---
        $windowHours = config('aml.velocity_window_hours', 24);
        $limit       = config('aml.velocity_limit', 5);
        $recentCount = Transaction::where('user_id', $model->user_id)
            ->where('created_at', '>=', Carbon::now()->subHours($windowHours))
            ->count();
        if ($recentCount >= $limit) {
            $score   += 20;
            $factors[] = "High velocity: {$recentCount} transactions in {$windowHours}h";
        }

        // --- Rule 5: New account (< 30 days) ---
        $newAccountDays = config('aml.new_account_days', 30);
        if ($user && $user->created_at->diffInDays(now()) < $newAccountDays) {
            $score   += 15;
            $factors[] = "New account (< {$newAccountDays} days old)";
        }

        // --- Rule 6: High-risk destination country ---
        if ($country && in_array(strtoupper($country), config('aml.high_risk_countries', []))) {
            $score   += 20;
            $factors[] = "High-risk destination country: {$country}";
        }

        // --- Rule 7: Failed KYC (rejected status) ---
        if ($user && $user->kv == \App\Constants\Status::KYC_REJECTED) {
            $score   += 10;
            $factors[] = "KYC previously rejected";
        }

        $score     = min($score, 100);
        $riskLevel = $this->resolveLevel($score);

        $record = AmlRiskScore::create([
            'user_id'          => $model->user_id,
            'transaction_id'   => $model->id,
            'transaction_type' => $type,
            'risk_score'       => $score,
            'risk_level'       => $riskLevel,
            'risk_factors'     => $factors,
        ]);

        if (in_array($riskLevel, ['high', 'critical'])) {
            $this->notifyAdmin($record, $model, $type);
        }

        return $record;
    }

    /**
     * Screen a user against sanctions/PEP lists on registration or profile change.
     * Saves a SanctionsScreeningResult record.
     */
    public function screenUser(User $user): void
    {
        $result = $this->provider->screenUser($user);

        SanctionsScreeningResult::create([
            'user_id'        => $user->id,
            'screening_type' => 'ofac',
            'status'         => $result['status'],
            'provider'       => $result['provider'],
            'match_data'     => $result['matches'] ?: null,
            'screened_at'    => now(),
        ]);

        if ($result['status'] === 'hit') {
            AdminNotification::create([
                'user_id'  => $user->id,
                'title'    => 'Sanctions Screening Hit',
                'click_url' => urlPath('admin.users.detail', $user->id),
            ]);
        }
    }

    // -------------------------------------------------------------------------

    private function resolveAmount(object $model, string $type): float
    {
        // SendMoney uses base_currency_amount; Deposit/Withdrawal use amount
        if ($type === 'send_money' && isset($model->base_currency_amount)) {
            return (float) $model->base_currency_amount;
        }
        return (float) ($model->amount ?? 0);
    }

    private function resolveCountry(object $model, string $type): ?string
    {
        if ($type === 'send_money' && isset($model->recipientCountry)) {
            return optional($model->recipientCountry)->code;
        }
        return null;
    }

    private function resolveLevel(int $score): string
    {
        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 30) return 'medium';
        return 'low';
    }

    private function notifyAdmin(AmlRiskScore $record, object $model, string $type): void
    {
        $label = ucfirst(str_replace('_', ' ', $type));
        AdminNotification::create([
            'user_id'   => $record->user_id,
            'title'     => strtoupper($record->risk_level) . " Risk {$label} Flagged",
            'click_url' => urlPath('admin.compliance.flagged.review', $record->id),
        ]);
    }

    private function resolveProvider(): AmlProviderInterface
    {
        $configured = config('aml.provider', 'internal');

        // Future external providers can be registered here
        return match ($configured) {
            default => new InternalAmlProvider(),
        };
    }
}
