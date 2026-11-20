<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\Extension;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Captcha{

    /*
    |--------------------------------------------------------------------------
    | Captcha
    |--------------------------------------------------------------------------
    |
    | Supports Google reCAPTCHA v2 (invisible) and a custom image captcha.
    | Use verify() to check whichever extension is active.
    |
    */

    /**
     * Returns the active Google reCAPTCHA v2 site key, or null when disabled.
     *
     * @return string|null
     */
    public static function reCaptchaKey(): ?string
    {
        if (config('app.env') === 'local' || in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            return null;
        }
        $ext = Extension::where('act', 'google-recaptcha2')->where('status', Status::ENABLE)->first();
        return $ext ? $ext->shortcode->site_key->value : null;
    }

    /**
     * @deprecated kept for backward-compatibility — always returns null.
     * @return null
     */
    public static function reCaptcha(): ?string
    {
        return null;
    }

    /**
    * Custom captcha script
    *
    * @return string
    */
    public static function customCaptcha($width = '100%', $height = 46, $bgColor = '#003'){

        $textColor = '#'.gs('base_color');
        $captcha = Extension::where('act', 'custom-captcha')->where('status', Status::ENABLE)->first();
        if (!$captcha) {
            return 0;
        }
        $code = rand(100000, 999999);
        $char = str_split($code);
        $ret = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
        $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgColor . '; color: ' . $textColor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';
        foreach ($char as $value) {
            $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
        }
        $ret .= '</div>';
        $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
        $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
        return $ret;

    }

    /**
    * Verify all captcha
    *
    * @return boolean
    */
    public static function verify(){
        $gCaptchaPass = self::verifyGoogleCaptcha();
        $cCaptchaPass = self::verifyCustomCaptcha();
        if ($gCaptchaPass && $cCaptchaPass) {
            return true;
        }
        return false;
    }

    /**
    * Verify Google reCAPTCHA v2 token submitted as `g-recaptcha-response`.
    *
    * @return boolean
    */
    public static function verifyGoogleCaptcha(): bool
    {
        if (config('app.env') === 'local' || in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            return true;
        }
        $ext = Extension::where('act', 'google-recaptcha2')->where('status', Status::ENABLE)->first();
        if (!$ext) {
            return true; // extension disabled — pass through
        }

        $token = request('g-recaptcha-response');
        if (!$token) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $ext->shortcode->secret_key->value,
            'response' => $token,
            'remoteip' => getRealIP(),
        ]);

        $result = $response->json();

        return !empty($result['success']);
    }

    /**
    * Verify custom captcha
    *
    * @return boolean
    */
    public static function verifyCustomCaptcha(){
        $pass = true;
        $customCaptcha = Extension::where('act', 'custom-captcha')->where('status', Status::ENABLE)->first();
        if ($customCaptcha) {
            $captchaSecret = hash_hmac('sha256', request()->captcha, $customCaptcha->shortcode->random_key->value);
            if ($captchaSecret != request()->captcha_secret) {
                $pass = false;
            }
        }
        return $pass;
    }

}
