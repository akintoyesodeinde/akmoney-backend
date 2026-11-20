<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::get('login', 'showLoginForm');
            Route::post('/', 'login')->name('login')->middleware(['throttle:10,1', 'progressive.delay', 'recaptcha']);
            Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });

        // Admin Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('reset');
            Route::post('reset', 'sendResetCodeEmail')->middleware('recaptcha');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        Route::controller('ResetPasswordController')->group(function () {
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
            Route::post('password/reset/change', 'reset')->name('password.change')->middleware('recaptcha');
        });
    });
});

Route::middleware('admin')->group(function () {

    // ── Admin Management (Super Admin only) ───────────────────────────────────
    Route::middleware('super.admin')->group(function () {
        Route::controller('AdminManagementController')->prefix('admins')->name('admins.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('toggle-status/{id}', 'toggleStatus')->name('toggle.status');
            Route::post('delete/{id}', 'delete')->name('delete');
        });

        Route::controller('AdminRoleController')->prefix('roles')->name('roles.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{role}', 'edit')->name('edit');
            Route::post('update/{role}', 'update')->name('update');
            Route::post('reset/{role}', 'reset')->name('reset');
        });
    });

    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('chart/deposit-withdraw', 'depositAndWithdrawReport')->name('chart.deposit.withdraw');
        Route::get('chart/transaction', 'transactionReport')->name('chart.transaction');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all');
        Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all');
        Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single');

        //Report Bugs
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit');

        Route::get('send-money/statistics', 'sendMoneyStatistics')->name('send_money.statistics');
        
        Route::get('view-attachments/{path}', 'viewAttachment')->name('view.attachment');
        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    //country
    Route::middleware('admin.permission:manage_countries')->prefix('country')->group(function () {
        Route::controller('CountryController')->name('country.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('save', 'store')->name('store');
            Route::post('save/{id}', 'update')->name('update');
            Route::post('status/{id}', 'updateStatus')->name('update.status');

            //currency conversation
            Route::get('currency/conversion-rate/{id}', 'currencyConversionRate')->name('currency.conversion.rate');
            Route::post('currency/conversion-rate/{id}', 'saveCurrencyConversionRates');
            Route::get('set-charges/{id}', 'setCharges')->name('charges.set');
            Route::post('charges/save/{id}', 'saveCharges')->name('charges.save');

            Route::post('currency/conversion-synchronize/{id}', 'synchronizeViaApi')->name('currency.conversion.synchronize');
        });
        Route::controller('ServiceController')->name('service.')->prefix('delivery-method')->group(function () {
            Route::get('services/{id?}', 'index')->name('index');
            Route::get('service/add', 'add')->name('add');
            Route::get('service/edit/{serviceId}', 'edit')->name('edit');
            Route::post('{id}/service/add/{serviceId?}', 'addService')->name('add.update');
            Route::post('service/update/status/{id}', 'updateServiceStatus')->name('status');
        });
        Route::controller('ConversionRateController')->name('conversion.rate.api.')->prefix('conversion-rate-api')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'save')->name('save');
        });
    });

    //Delivery Methods
    Route::middleware('admin.permission:remittance_options')->controller('DeliveryMethodController')->name('delivery.method.')->prefix('delivery')->group(function () {
        Route::get('', 'all')->name('all');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'updateStatus')->name('status');
    });

    //SOF
    Route::middleware('admin.permission:remittance_options')->controller('SourceOfFundController')->name('sof.')->prefix('source-of-fund')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('save/{id?}', 'saveSourceOfFund')->name('save');
        Route::post('status/{id}', 'updateStatus')->name('status');
    });

    //Sending Purpose
    Route::middleware('admin.permission:remittance_options')->controller('SendingPurposeController')->name('sending.purpose.')->prefix('sending-purpose')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('save/{id?}', 'saveSendingPurpose')->name('save');
        Route::post('status/{id}', 'updateStatus')->name('status');
    });

    // Payout API (multi-provider: PawaPay, Paga, Manual)
    Route::middleware('admin.permission:remittance_options')->controller('PayoutApiController')->name('payout.api.')->prefix('payout-api')->group(function () {
        Route::get('settings',                                    'settings')->name('settings');
        Route::post('settings',                                   'saveSettings')->name('settings.save');
        Route::get('countries',                                   'countries')->name('countries');
        Route::post('country/{payoutCountry}/provider',            'saveCountryProvider')->name('country.provider');
        Route::post('country/{country}/toggle',                   'toggleCountry')->name('country.toggle');
        Route::post('method/{method}/toggle',                     'toggleMethod')->name('method.toggle');
        Route::post('method/{method}/provider',                   'saveMethodProvider')->name('method.provider');
        Route::get('logs',                                        'logs')->name('logs');
        Route::post('sync-active-conf',                           'syncActiveConf')->name('sync.active.conf');
        Route::post('retry/{sendMoney}',                          'retry')->name('retry');
        Route::post('resend-callback/{sendMoney}',                'resendCallback')->name('resend.callback');
        Route::post('fail-enqueued/{sendMoney}',                  'failEnqueued')->name('fail.enqueued');
    });

    // Send Money
    Route::middleware('admin.permission:send_money')->controller('SendMoneyController')->name('send.money.')->prefix('send-money')->group(function () {
        Route::get('all', 'index')->name('all');
        Route::get('pending', 'index')->name('pending');
        Route::get('completed', 'index')->name('completed');
        Route::get('refunded', 'index')->name('refunded');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('refund/{id}', 'refundMoney')->name('refund.now');
        Route::post('pay-receiver/{id}', 'payTheReceiver')->name('pay.receiver');
    });

    // Users Manager
    Route::middleware('admin.permission:manage_users')->controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('personal/account', 'personalAccount')->name('personal.account');
        Route::get('business/account', 'businessAccount')->name('business.account');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('with-balance', 'usersWithBalance')->name('with.balance');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('force-tfa/{id}', 'toggleForceTfa')->name('force.tfa');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');

        Route::get('list', 'list')->name('list');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');

        Route::get('setting/business-account', 'businessForm')->name('business.profile');
        Route::post('setting/business-account', 'businessFormUpdate');
    });

    // Agent Manager
    Route::middleware('admin.permission:manage_agents')->controller('ManageAgentController')->name('agents.')->prefix('agents')->group(function () {
        Route::get('add', 'add')->name('add');
        Route::post('add', 'store')->name('store');

        Route::get('/', 'allAgents')->name('all');
        Route::get('active', 'activeAgents')->name('active');
        Route::get('banned', 'bannedAgents')->name('banned');
        Route::get('kyc-unverified', 'kycUnverifiedAgents')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingAgents')->name('kyc.pending');
        Route::get('with-balance', 'agentsWithBalance')->name('with.balance');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    });

    // Deposit Gateway
    Route::middleware('admin.permission:system_settings')->name('gateway.')->prefix('gateway')->group(function () {
        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
        });

        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // DEPOSIT SYSTEM
    Route::middleware('admin.permission:agent_deposits')->controller('DepositController')->prefix('deposit')->name('deposit.')->group(function () {
        Route::get('all/{user_id?}', 'deposit')->name('list');
        Route::get('pending/{user_id?}', 'pending')->name('pending');
        Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
        Route::get('approved/{user_id?}', 'approved')->name('approved');
        Route::get('successful/{user_id?}', 'successful')->name('successful');
        Route::get('initiated/{user_id?}', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
    });

    // payment SYSTEM
    Route::middleware('admin.permission:payments')->controller('DepositController')->prefix('payment')->name('payment.')->group(function () {
        Route::get('/', 'deposit')->name('list');
        Route::get('pending', 'pending')->name('pending');
        Route::get('rejected', 'rejected')->name('rejected');
        Route::get('approved', 'approved')->name('approved');
        Route::get('successful', 'successful')->name('successful');
        Route::get('initiated', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('{id}/receipt/verify', 'verifyReceipt')->name('receipt.verify')->middleware('admin.permission:payments,edit');
    });

    // WITHDRAW SYSTEM
    Route::middleware('admin.permission:withdrawals')->name('withdraw.')->prefix('withdraw')->group(function () {
        Route::controller('WithdrawalController')->name('data.')->group(function () {
            Route::get('pending/{user_id?}', 'pending')->name('pending');
            Route::get('approved/{user_id?}', 'approved')->name('approved');
            Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
            Route::get('all/{user_id?}', 'all')->name('all');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('approve', 'approve')->name('approve');
            Route::post('reject', 'reject')->name('reject');
        });

        // Withdraw Method
        Route::controller('WithdrawMethodController')->prefix('method')->name('method.')->group(function () {
            Route::get('/', 'methods')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('edit/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // Report
    Route::middleware('admin.permission:payments')->controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('transaction/{user_id?}', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });

    // Admin Support
    Route::middleware('admin.permission:support_ticket')->controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('tickets/agent', 'agentTicket')->name('agent');
        Route::get('tickets/user', 'userTicket')->name('user');
        Route::get('tickets/guest', 'guestTicket')->name('guest');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });

    // Language Manager
    Route::middleware('admin.permission:system_settings')->controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    Route::middleware('admin.permission:system_settings')->controller('GeneralSettingController')->group(function () {
        Route::get('system-setting', 'systemSetting')->name('setting.system');

        // General Setting
        Route::get('general-setting', 'general')->name('setting.general');
        Route::post('general-setting', 'generalUpdate');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Security settings (IP blacklist/whitelist)
        Route::get('setting/security', 'securitySetting')->name('setting.security');
        Route::post('setting/security', 'securitySettingUpdate');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');

        // Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');

        // sitemap
        Route::get('sitemap', 'sitemap')->name('setting.sitemap');
        Route::post('sitemap', 'sitemapSubmit');

        // robots
        Route::get('robot', 'robot')->name('setting.robot');
        Route::post('robot', 'robotSubmit');

        // Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        // maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit');
    });


    Route::middleware('admin.permission:system_settings')->controller('CronConfigurationController')->name('cron.')->prefix('cron')->group(function () {
        Route::get('index', 'cronJobs')->name('index');
        Route::post('store', 'cronJobStore')->name('store');
        Route::post('update', 'cronJobUpdate')->name('update');
        Route::post('delete/{id}', 'cronJobDelete')->name('delete');
        Route::get('schedule', 'schedule')->name('schedule');
        Route::post('schedule/store', 'scheduleStore')->name('schedule.store');
        Route::post('schedule/status/{id}', 'scheduleStatus')->name('schedule.status');
        Route::get('schedule/pause/{id}', 'schedulePause')->name('schedule.pause');
        Route::get('schedule/logs/{id}', 'scheduleLogs')->name('schedule.logs');
        Route::post('schedule/log/resolved/{id}', 'scheduleLogResolved')->name('schedule.log.resolved');
        Route::post('schedule/log/flush/{id}', 'logFlush')->name('log.flush');
    });


    //KYC setting
    Route::middleware('admin.permission:system_settings')->controller('KycController')->group(function () {
        Route::get('kyc/setting/personal-user', 'setting')->name('kyc.setting.user');
        Route::post('kyc/setting/store', 'store')->name('kyc.providers.save');
        Route::post('kyc/setting/personal-user', 'settingUpdate');

        Route::get('kyc/setting/business-user', 'setting')->name('kyc.setting.user.business');
        Route::post('kyc/setting/business-user', 'settingUpdate');

        Route::get('kyc/setting/agent', 'setting')->name('kyc.setting.agent');
        Route::post('kyc/setting/agent', 'settingUpdate');

        Route::get('kyc/setting/module', 'module')->name('kyc.setting.module');
        Route::post('kyc/setting/module', 'moduleUpdate');
    });

    //Notification Setting
    Route::middleware('admin.permission:system_settings')->name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global/email', 'globalEmail')->name('global.email');
        Route::post('global/email/update', 'globalEmailUpdate')->name('global.email.update');
        Route::get('global/sms', 'globalSms')->name('global.sms');
        Route::post('global/sms/update', 'globalSmsUpdate')->name('global.sms.update');
        Route::get('global/push', 'globalPush')->name('global.push');
        Route::post('global/push/update', 'globalPushUpdate')->name('global.push.update');

        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{type}/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    // Security Logs
    Route::middleware('admin.permission:security_logs')->controller('SecurityLogController')->prefix('security')->name('security.')->group(function () {
        Route::get('login-history', 'loginHistory')->name('login.history');
        Route::get('profile-audit', 'profileAudit')->name('profile.audit');
    });

    // reCAPTCHA Logs
    Route::middleware('admin.permission:security_logs')->controller('RecaptchaLogController')->prefix('recaptcha-logs')->name('recaptcha.log.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::delete('{id}', 'destroy')->name('destroy');
        Route::post('flush', 'flush')->name('flush');
    });

    // Plugin
    Route::middleware('admin.permission:system_settings')->controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });

    //System Information
    Route::middleware('admin.permission:cache_management')->controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
        Route::get('system-update', 'systemUpdate')->name('update');
        Route::post('system-update', 'systemUpdateProcess')->name('update.process');
        Route::get('system-update/log', 'systemUpdateLog')->name('update.log');
    });

    // SEO
    Route::middleware('admin.permission:system_settings')->get('seo', 'FrontendController@seoEdit')->name('seo');

    // Frontend
    Route::middleware('admin.permission:system_settings')->name('frontend.')->prefix('frontend')->group(function () {
        Route::controller('FrontendController')->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key?}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::get('frontend-slug-check/{key}/{id?}', 'frontendElementSlugCheck')->name('sections.element.slug.check');
            Route::get('frontend-element-seo/{key}/{id}', 'frontendSeo')->name('sections.element.seo');
            Route::post('frontend-element-seo/{key}/{id}', 'frontendSeoUpdate');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::get('manage-pages/check-slug/{id?}', 'checkSlug')->name('manage.pages.check.slug');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');
            Route::get('manage-seo/{id}', 'manageSeo')->name('manage.pages.seo');
            Route::post('manage-seo/{id}', 'manageSeoStore');
        });
    });

    // WALLET SYSTEM
    Route::controller('WalletController')->prefix('wallets')->name('wallet.')->group(function () {
        Route::get('/', 'wallets')->name('index');
        Route::get('exchanges', 'exchanges')->name('exchanges');
        Route::get('transfers', 'transfers')->name('transfers');
        Route::get('setting', 'setting')->name('setting');
        Route::post('setting', 'settingUpdate')->name('setting.update');
        Route::post('{wallet}/credit', 'credit')->name('credit');
        Route::post('{wallet}/debit', 'debit')->name('debit');
    });

    // Compliance Dashboard
    Route::middleware('admin.permission:compliance')->controller('ComplianceController')->prefix('compliance')->name('compliance.')->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');
        Route::get('flagged', 'flaggedTransactions')->name('flagged');
        Route::get('flagged/{id}', 'reviewTransaction')->name('flagged.review');
        Route::post('flagged/{id}/mark', 'markReviewed')->name('flagged.mark');
        Route::get('sanctions', 'sanctionsHits')->name('sanctions');
        Route::get('audit-export', 'exportAuditReport')->name('audit.export');
    });
});
