<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\GeneralSetting;

return new class extends Migration
{
    public function up(): void
    {
        $template = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Sudanicash Notification</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { background: #F2F4F7; font-family: 'Inter', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
  .wrapper { background: #F2F4F7; padding: 40px 16px; min-height: 100vh; }
  .card { background: #ffffff; border-radius: 20px; max-width: 580px; margin: 0 auto; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
  .header { background: #0C0C0C; padding: 32px 40px 28px; text-align: center; }
  .logo-wrap { text-align: center; }
  .header-sub { color: rgba(255,255,255,0.45); font-size: 12px; margin-top: 6px; letter-spacing: 0.05em; text-transform: uppercase; }
  .divider-bar { height: 4px; background: linear-gradient(90deg, #E8192C 0%, #2B6B7F 50%, #E8B800 100%); }
  .body { padding: 40px 40px 32px; }
  .greeting { font-size: 18px; font-weight: 700; color: #0C0C0C; margin-bottom: 6px; }
  .message-area { font-size: 15px; color: #444; line-height: 1.75; }
  .message-area p { margin-bottom: 12px; }
  .cta-wrap { text-align: center; margin: 32px 0; }
  .cta-btn { display: inline-block; background: #E8192C; color: #ffffff; font-size: 15px; font-weight: 700; text-decoration: none; padding: 14px 36px; border-radius: 12px; letter-spacing: 0.01em; }
  .info-box { background: #F7F9FC; border: 1px solid #E8ECF2; border-radius: 12px; padding: 18px 20px; margin: 24px 0 0; }
  .info-box p { font-size: 13px; color: #667; line-height: 1.6; }
  .info-box strong { color: #333; }
  .footer { background: #0C0C0C; padding: 28px 40px; text-align: center; }
  .footer-links { margin-bottom: 14px; }
  .footer-links a { color: rgba(255,255,255,0.5); font-size: 12px; text-decoration: none; margin: 0 10px; }
  .footer-links a:hover { color: #E8192C; }
  .footer-copy { font-size: 11px; color: rgba(255,255,255,0.3); line-height: 1.7; }
  .footer-badges { display: inline-flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin: 12px 0; }
  .footer-badge { display: inline-block; padding: 3px 10px; border: 1px solid rgba(255,255,255,0.1); border-radius: 999px; font-size: 10px; color: rgba(255,255,255,0.4); font-weight: 600; }
  @media (max-width: 600px) {
    .body { padding: 28px 24px 24px; }
    .header { padding: 24px 24px 20px; }
    .footer { padding: 24px; }
    .logo-text { font-size: 19px; }
  }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">

    <!-- Header -->
    <div class="header">
      <div class="logo-wrap">
        <img src="{{site_logo}}" alt="{{site_name}}" style="max-height:40px;width:auto;max-width:180px;object-fit:contain;display:block;margin:0 auto;">
      </div>
      <p class="header-sub">Secure Money Transfers</p>
    </div>

    <!-- Brand gradient bar -->
    <div class="divider-bar"></div>

    <!-- Body -->
    <div class="body">
      <p class="greeting">Hello, {{fullname}}</p>
      <div class="message-area">
        {{message}}
      </div>
      <div class="info-box">
        <p>If you did not request this notification or have any questions, please contact our support team at <strong>support@sudanicash.com</strong> or call <strong>+249912529039</strong>.</p>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <div class="footer-links">
        <a href="#">Dashboard</a>
        <a href="#">Contact Us</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
      <div class="footer-badges">
        <span class="footer-badge">FINTRAC Regulated</span>
        <span class="footer-badge">MSB #C10000108</span>
        <span class="footer-badge">256-bit SSL</span>
        <span class="footer-badge">AML Compliant</span>
      </div>
      <p class="footer-copy">
        &copy; {{date('Y')}} Sudanicash &mdash; AL REAL TRADE AND INVESTMENT INC.<br>
        60-6440 Centre Street NE, Calgary, Alberta T2K 0VA, Canada<br>
        This email was sent to <strong style="color:rgba(255,255,255,0.5);">{{username}}</strong>. Do not reply to this email.
      </p>
    </div>

  </div>
</div>
</body>
</html>
HTML;

        $gs = GeneralSetting::first();
        if ($gs) {
            $gs->email_template = $template;
            $gs->save();
        }
    }

    public function down(): void
    {
        // No rollback — don't overwrite whatever was there before
    }
};
