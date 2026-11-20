<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm');
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->middleware(['recaptcha', 'throttle:10,1', 'progressive.delay']);
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->name('register.submit')->middleware(['recaptcha', 'throttle:5,1']);
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
        Route::get('google', 'redirectToGoogle')->name('redirectToGoogle');
        Route::get('google/callback', 'handleGoogleCallback')->name('handleGoogleCallback');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email')->middleware(['recaptcha', 'throttle:5,1']);
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code')->middleware('recaptcha');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update')->middleware(['recaptcha', 'throttle:5,1']);
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
});


Route::middleware('auth')->name('user.')->group(function () {
    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit')->middleware('recaptcha');
    Route::get('kyc-form', 'User\UserController@kycForm')->name('kyc.form');
    Route::get('kyc-data', 'User\UserController@kycData')->name('kyc.data');
    Route::post('kyc-submit', 'User\UserController@kycSubmit')->name('kyc.submit')->middleware('recaptcha');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
        Route::post('verify-2fa-sms', 'sms2faVerification')->name('2fa.verify.sms');
        Route::post('verify-2fa-email', 'email2faVerification')->name('2fa.verify.email');
        Route::post('resend-2fa-code', 'resend2faCode')->name('2fa.resend');
    });

    Route::middleware(['check.status', 'registration.complete', 'kyc'])->group(function () {
        Route::namespace('User')->group(function () {
            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::post('/notifications/read', function () {
                    \App\Support\NotificationHelper::markAsRead();
                    return back();
                })->name('notifications.read');
                Route::post('theme', 'updateTheme')->name('theme.update');
                Route::get('/transaction/{id}/receipt', 'downloadReceipt')->name('transaction.downloadReceipt');
                Route::get('/attachment/{path}', 'viewAttachment')->name('view.attachment');


                Route::post('set_pin', 'setPin')->name('set_pin');
                Route::get('set-pin', 'viewSetPin')->name('view_set_pin');
                Route::post('reset_pin', 'resetPin')->name('reset_pin');
                Route::get('reset-pin', 'viewResetPin')->name('view_reset_pin');
                Route::get('reset-pin/form', 'viewResetPinForm')->name('reset_pin_form');
                Route::post('verify_pin', 'verifyPin')->name('verify_pin');
                Route::post('verify_security', 'verifySecurityAnswer')->name('verify_security');
                Route::post('pin-reset/send-code', 'sendPinResetCode')->name('pin_reset.send_code');
                Route::post('pin-reset/verify-code', 'verifyPinResetCode')->name('pin_reset.verify_code');
                // routes/web.php or api.php
                Route::get('/transfers/track/{mtcn}', 'track')->name('transfers.track');
                
                Route::get('recipients', 'reps')->name('reps');
                Route::post('recipients', 'saveReps')->name('save_reps');
                Route::put('/recipients/{recipient}', 'updateRep')->name('recipient.update');
                Route::delete('/recipients/{recipient}', 'deleteRep')->name('recipient.delete');
                
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                Route::post('deposit/{id}/receipt', 'uploadDepositReceipt')->name('deposit.receipt.upload');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/enable-sms', 'enable2faSms')->name('twofactor.enable.sms');
                Route::post('twofactor/enable-email', 'enable2faEmail')->name('twofactor.enable.email');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('wallets', 'wallets')->name('wallets');
                Route::post('wallets', 'walletStore')->name('wallets.store');
                Route::post('wallets/{id}/set-default', 'walletSetDefault')->name('wallets.set.default');
                Route::post('wallets/exchange', 'walletExchange')->name('wallets.exchange');
                Route::get('wallets/{id}/transactions', 'walletTransactions')->name('wallets.transactions');
                Route::get('wallets/transfer/lookup', 'walletTransferLookup')->name('wallets.transfer.lookup');
                Route::post('wallets/transfer', 'walletTransfer')->name('wallets.transfer');
                Route::get('wallets/transfer/history', 'walletTransferHistory')->name('wallets.transfer.history');

                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');
            });

            // send money
            Route::middleware('kyc:send_money')->controller('SendMoneyController')->prefix('send-money')->name('send.money.')->group(function () {
                Route::get('send-now', 'sendMoney')->name('now');
                Route::post('save', 'save')->name('save');
                Route::get('history', 'history')->name('history');
                Route::get('/send-money/again/{transfer}', 'sendAgain')->name('again');

                Route::middleware('kyc:direct_payment')->group(function () {
                    Route::get('pay-now', 'payNow')->name('pay.now');
                    Route::post('pay', 'pay')->name('pay');
                });
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile')->name('profile.submit');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');

                // Email change
                Route::post('email-change/request', 'requestEmailChange')->name('email.change.request');
                Route::get('email-change/verify', 'showEmailChangeVerify')->name('email.change.verify');
                Route::post('email-change/verify', 'verifyEmailChange')->name('email.change.verify.submit');
                Route::get('email-change/resend', 'resendEmailChangeCode')->name('email.change.resend');

                // Phone change
                Route::post('mobile-change/request', 'requestMobileChange')->name('mobile.change.request');
                Route::get('mobile-change/verify', 'showMobileChangeVerify')->name('mobile.change.verify');
                Route::post('mobile-change/verify', 'verifyMobileChange')->name('mobile.change.verify.submit');
                Route::get('mobile-change/resend', 'resendMobileChangeCode')->name('mobile.change.resend');
            });
        });

        // Payment
        Route::prefix('payment')->name('deposit.')->middleware('kyc:direct_payment')->controller('Gateway\PaymentController')->group(function () {
            Route::post('insert', 'depositInsert')->name('insert');
            Route::match(['GET', 'POST'], 'confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            Route::post('wallet/{walletId}/insert', 'walletDepositInsert')->name('wallet.insert');
        });
    });
});
