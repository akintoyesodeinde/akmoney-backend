<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('health', function () {
    $dbStatus = 'disconnected';
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Throwable $e) {
        $dbStatus = 'error';
    }
    return response()->json([
        'status' => 'ok',
        'service' => 'AkMoney API',
        'timestamp' => now()->toIso8601String(),
        'database' => $dbStatus,
        'environment' => config('app.env'),
        'version' => '1.0.0',
    ]);
});

Route::namespace('Api')->group(function(){
    Route::controller('AppController')->group(function () {
        Route::get('general-setting','generalSetting');
        Route::get('get-countries','getCountries');
        Route::get('language/{key?}','getLanguage');
        Route::get('language/{key}','getLanguage');
        Route::get('policies', 'policies');
        Route::get('policy/{slug}', 'policyContent');
        Route::get('faq', 'faq');
        Route::get('seo', 'seo');
        Route::get('get-extension/{act}','getExtension');
        Route::post('contact', 'submitContact');
        Route::get('cookie', 'cookie');
        Route::post('cookie/accept', 'cookieAccept');
        Route::get('custom-pages', 'customPages');
        Route::get('custom-page/{slug}', 'customPageData');
        Route::get('sections/{key?}', 'allSections');
    });

    // Public transfer tracking — no auth required, minimal info returned
    Route::get('track/{trx}', 'SendMoneyController@trackTransferPublic');

	Route::namespace('Auth')->group(function(){
        Route::controller('LoginController')->group(function(){
            Route::post('login', 'login')->middleware('throttle:10,1');
            Route::post('check-token', 'checkToken');
        });
		Route::post('register', 'RegisterController@register')->middleware('throttle:5,1');

        Route::controller('ForgotPasswordController')->group(function(){
            Route::post('password/email', 'sendResetCodeEmail')->middleware('throttle:5,1');
            Route::post('password/verify-code', 'verifyCode')->middleware('throttle:10,1');
            Route::post('password/reset', 'reset')->middleware('throttle:5,1');
        });
	});

    Route::middleware('auth:sanctum')->group(function () {
        Route::controller('UserController')->group(function(){
            Route::post('user-data-submit', 'userDataSubmit');
            Route::get('business-user-form', 'businessUserForm');
        });

        //authorization
        Route::middleware('registration.complete')->controller('AuthorizationController')->group(function(){
            Route::get('authorization', 'authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode')->middleware('throttle:3,5');
            Route::post('resend-2fa-sms', 'resend2faSms')->middleware('throttle:3,5');
            Route::post('verify-email', 'emailVerification');
            Route::post('verify-mobile', 'mobileVerification');
            Route::post('verify-g2fa', 'g2faVerification');
        });

        // 2FA setup — accessible before check.status so force_tfa users can set up
        Route::middleware('registration.complete')->controller('UserController')->group(function () {
            Route::get('twofactor', 'show2faForm');
            Route::post('twofactor/sms/send-setup-code', 'setupSmsTwoFa')->middleware('throttle:3,5');
            Route::post('twofactor/enable', 'create2fa');
            Route::post('twofactor/disable', 'disable2fa');
            Route::get('twofactor/backup-codes', 'backupCodes');
            Route::post('twofactor/backup-codes/regenerate', 'regenerateBackupCodes');
        });

        Route::middleware(['check.status'])->group(function () {
            Route::middleware('registration.complete')->group(function(){
                Route::controller('UserController')->group(function(){
                    Route::get('dashboard', 'dashboard');

                    Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                    // profile setting
                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');

                    // email change
                    Route::post('email-change/request', 'requestEmailChange');
                    Route::post('email-change/verify', 'verifyEmailChange');

                    // phone change
                    Route::post('mobile-change/request', 'requestMobileChange');
                    Route::post('mobile-change/verify', 'verifyMobileChange');

                    // user info
                    Route::get('user-info','userInfo');

                    //KYC
                    Route::get('kyc-form','kycForm');
                    Route::post('kyc-submit','kycSubmit');

                    // app
                    Route::get('home', 'home');
                    Route::post('calculate', 'calculate');

                    //Report
                    Route::get('transactions','transactions');
                    Route::get('transaction-detail/{id}', 'transactionDetail');
                    Route::get('deposit/history', 'depositHistory');

                    // push notification
                    Route::post('add-device-token', 'addDeviceToken');
                    Route::get('push-notifications', 'pushNotifications');
                    Route::post('push-notifications/read/{id}', 'pushNotificationsRead');

                    // in-app notifications
                    Route::get('notifications', 'notifications');
                    Route::post('notifications/read', 'notificationsRead');
                    Route::post('notifications/read/{id}', 'notificationsRead');


                    Route::post('delete-account', 'deleteAccount');
                });

                // Recipients CRUD
                Route::controller('RecipientController')->prefix('recipients')->group(function () {
                    Route::get('/', 'index');
                    Route::post('/', 'store');
                    Route::get('{id}', 'show');
                    Route::put('{id}', 'update');
                    Route::delete('{id}', 'destroy');
                });

                Route::controller('SendMoneyController')->name('send.money.')->group(function () {
                    Route::get('send-money/send-now', 'sendMoney')->middleware('kyc:send_money');
                    Route::post('send-money/save', 'saveMoney')->middleware('kyc:send_money');
                    Route::post('send-money/service', 'getServices');
                    Route::get('send-money/transfers/history', 'history');
                    Route::get('send-money/detail/{id}', 'sendMoneyDetail');
                    // Transfer tracking — authenticated, full detail
                    Route::get('send-money/track/{trx}', 'trackTransfer');
                });

                // Transaction PIN
                Route::middleware('registration.complete')->controller('TransactionPinController')->prefix('pin')->group(function () {
                    Route::get('status', 'status');
                    Route::post('set', 'setPin');
                    Route::post('verify', 'verifyPin');
                    Route::post('update', 'updatePin');
                    Route::post('reset/send-code', 'sendResetCode');
                    Route::post('reset/verify-code', 'verifyResetCode');
                    Route::post('reset/confirm', 'resetPin');
                });

                // Payment
                Route::middleware(['registration.complete', 'kyc:direct_payment'])->controller('PaymentController')->group(function(){
                    Route::get('payment/methods', 'methods');
                    Route::post('payment/insert', 'depositInsert');
                    Route::get('payment/status/{trx}', 'depositStatus');
                    Route::post('manual/confirm', 'manualDepositConfirm');
                });

                // Multi-wallet system
                Route::controller('WalletController')->prefix('wallets')->group(function () {
                    Route::get('/', 'index');
                    Route::post('/', 'store');
                    Route::get('/currencies', 'currencies');
                    Route::get('/exchange-history', 'exchangeHistory');
                    Route::get('/deposit/methods', 'depositMethods');
                    Route::post('/exchange', 'exchange');
                    Route::get('/transfer/lookup', 'transferLookup');
                    Route::post('/transfer', 'transfer');
                    Route::get('/transfer/history', 'transferHistory');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}/deposit', 'depositInit');
                    Route::post('/{id}/deposit/confirm', 'depositConfirm');
                    Route::get('/{id}/transactions', 'walletTransactions');
                    Route::get('/{id}/deposit-history', 'depositHistory');
                    Route::post('/{id}/set-default', 'setDefault');
                });

                Route::controller('TicketController')->prefix('ticket')->group(function () {
                    Route::get('/', 'supportTicket');
                    Route::post('create', 'storeSupportTicket');
                    Route::get('view/{ticket}', 'viewTicket');
                    Route::post('reply/{id}', 'replyTicket');
                    Route::post('close/{id}', 'closeTicket');
                    Route::get('download/{attachment_id}', 'ticketDownload');
                });
            });
        });

        Route::get('logout', 'Auth\LoginController@logout');
        Route::post('logout-all', 'Auth\LoginController@logoutAll');
    });
});
