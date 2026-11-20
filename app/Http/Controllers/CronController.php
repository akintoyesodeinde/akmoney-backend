<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\CronJob;
use App\Lib\CurlRequest;
use App\Constants\Status;
use App\Models\CronJobLog;
use App\Models\Deposit;
use App\Lib\ConversionRate;
use App\Http\Controllers\Gateway\Apaylo\ApayloClient;
use App\Http\Controllers\Gateway\PaymentController;

class CronController extends Controller
{
    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $interval = isset($cron->schedule->interval) ? (int) $cron->schedule->interval : 60;
            $cron->next_run = now()->addSeconds($interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    /**
     * Poll Apaylo for pending Interac e-Transfer deposits and complete them.
     * Registered as a cron job — runs every 5 minutes.
     */
    public function syncApayloTransactions(): void
    {
        // Find all pending Apaylo deposits that have a TransactionNumber stored
        $pendingDeposits = Deposit::where('method_code', 511)
            ->where('status', Status::PAYMENT_PENDING)
            ->whereNotNull('btc_wallet')
            ->get();

        if ($pendingDeposits->isEmpty()) {
            return;
        }

        // Group by gateway_parameter (all share the same credentials per currency)
        $gatewayCurrency = \App\Models\GatewayCurrency::where('method_code', 511)->first();

        if (!$gatewayCurrency) {
            return;
        }

        $config = json_decode($gatewayCurrency->gateway_parameter);

        if (empty($config->api_key) || empty($config->secret_key)) {
            return;
        }

        $client    = new ApayloClient($config->api_key, $config->secret_key, $config->environment ?? 'sandbox');
        $startDate = now()->subDays(7)->format('Y-m-d');
        $endDate   = now()->format('Y-m-d');

        $response  = $client->searchRequestTransfers($startDate, $endDate);

        if (!empty($response->IsError)) {
            logger()->error('Apaylo sync error: ' . ($response->Message ?? 'Unknown error'));
            return;
        }

        // Apaylo wraps results in {"StatusCode":...,"IsError":...,"Result":[...]}
        $results = $response->Result ?? [];
        if (!is_array($results)) {
            $results = (array) $results;
        }

        if (empty($results)) {
            return;
        }

        // Build a lookup of TransactionNumber → status from Apaylo response
        $apayloTxMap = [];

        foreach ($results as $item) {
            if (is_object($item) && !empty($item->TransactionNumber)) {
                $apayloTxMap[$item->TransactionNumber] = $item;
            }
        }

        foreach ($pendingDeposits as $deposit) {
            $txNumber = $deposit->btc_wallet;

            if (!isset($apayloTxMap[$txNumber])) {
                continue;
            }

            $apayloTx = $apayloTxMap[$txNumber];
            $txStatus = strtolower($apayloTx->TransactionStatus ?? '');

            if (in_array($txStatus, ['completed', 'settled', 'sent', 'successful', 'success'])) {
                // Verify amount matches (Apaylo returns Amount as numeric)
                $paidAmount = (float) ($apayloTx->Amount ?? 0);
                $expected   = (float) $deposit->final_amount;

                if (abs($paidAmount - $expected) < 0.02) {
                    $deposit->detail = array_merge(
                        (array) $deposit->detail,
                        ['apaylo_status' => $txStatus, 'synced_at' => now()->toIso8601String()]
                    );
                    $deposit->save();

                    PaymentController::userDataUpdate($deposit);

                    $deposit->receipt_required = 1;
                    $deposit->save();

                    logger()->info("Apaylo deposit #{$deposit->id} confirmed via cron (TxNo: {$txNumber})");
                } else {
                    logger()->warning("Apaylo amount mismatch on deposit #{$deposit->id}: paid {$paidAmount}, expected {$expected}");
                }
            } elseif (in_array($txStatus, ['expired', 'cancelled', 'declined'])) {
                $deposit->status = Status::PAYMENT_REJECT;
                $deposit->detail = array_merge(
                    (array) $deposit->detail,
                    ['apaylo_status' => $txStatus, 'synced_at' => now()->toIso8601String()]
                );
                $deposit->save();
                logger()->info("Apaylo deposit #{$deposit->id} rejected (status: {$txStatus})");
            }
        }
    }

    public function currencyConversionRate()
    {
        if (@gs('conversion_rate_api')?->auto_synchronization) {
            $conversionRate = new ConversionRate();
            $conversionRate->synchronizeAll();
        }
    }
}
