<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Agent;
use App\Lib\Searchable;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Constants\Status;
use App\Models\SendMoney;
use App\Models\Withdrawal;
use App\Models\SupportTicket;
use App\Models\AdminNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (!cache()->get('SystemInstalled')) {
                $envFilePath = base_path('.env');
                if (!config('app.key') && (!file_exists($envFilePath) || empty(file_get_contents($envFilePath)))) {
                    if (!$this->app->runningInConsole()) {
                        header('Location: install');
                        exit;
                    }
                } else {
                    cache()->put('SystemInstalled', true);
                }
            }
        } catch (\Throwable $e) {
            // Allow artisan migrate / console bootstrap before cache table is created
        }

        $viewShare['emptyMessage'] = 'Data not found';
        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount'  => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount' => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'    => User::kycUnverified()->count(),
                'kycPendingUsersCount'       => User::kycPending()->count(),
                'pendingTicketCount'         => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                'pendingDepositsCount'       => Deposit::agentDeposit()->pending()->count(),
                'pendingWithdrawCount'       => Withdrawal::pending()->count(),
                'updateAvailable'            => version_compare(gs('available_version'),systemDetails()['version'],'>') ? 'v'.gs('available_version') : false,
                'shouldPayoutCount'          => SendMoney::where('status', Status::SEND_MONEY_PENDING)->count(),
                'pendingPaymentsCount'       => Deposit::pending()->where('user_id', '!=', 0)->count(),
                'bannedAgentsCount'          => Agent::banned()->count(),
                'kycUnverifiedAgentsCount'   => Agent::kycUnverified()->count(),
                'kycPendingAgentsCount'      => Agent::kycPending()->count(),
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications'      => AdminNotification::forCurrentAdmin()->where('is_read', Status::NO)->with('user', 'triggeredBy')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount'  => AdminNotification::forCurrentAdmin()->where('is_read', Status::NO)->count(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        try {
            if (gs('force_ssl') && config('app.env') !== 'local') {
                \URL::forceScheme('https');
            }
        } catch (\Throwable $e) {
            // Allow console commands before general_settings table is migrated
        }

        Paginator::useBootstrapFive();

        try {
            View::addNamespace('Template', resource_path('views/templates/' . activeTemplateName()));
        } catch (\Throwable $e) {
            View::addNamespace('Template', resource_path('views/templates/basic'));
        }
        View::composer('*', function ($view) {
            $mode = Auth::check() ? Auth::user()->current_mode : session('theme_mode', 'light');
            $view->with('themeMode', $mode);
            $view->with('theme', themeColors($mode));
        });
    }
}
