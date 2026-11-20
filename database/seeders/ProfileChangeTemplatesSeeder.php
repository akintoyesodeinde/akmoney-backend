<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class ProfileChangeTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'act'          => 'EMAIL_CHANGE_OTP',
                'name'         => 'Email Change - Verification Code (sent to new email)',
                'subject'      => 'Verify Your New Email Address',
                'email_body'   => '<div style="font-family:Montserrat,sans-serif;line-height:1.6">
<p>Hello <strong>{{username}}</strong>,</p>
<p>We received a request to update the email address on your <strong>{{site_name}}</strong> account.</p>
<p>Your verification code is:</p>
<h2 style="letter-spacing:6px;color:#1a1a1a;text-align:center;background:#f4f4f4;padding:16px;border-radius:8px;">{{code}}</h2>
<p>This code expires in <strong>10 minutes</strong>. Do not share it with anyone.</p>
<p>If you did not make this request, please <a href="{{site_url}}">contact support</a> immediately.</p>
<p style="color:#888;font-size:12px;">Requested at {{time}} &bull; IP: {{ip}}</p>
</div>',
                'sms_body'     => 'Your {{site_name}} email change code is: {{code}}. Valid for 10 minutes. Do not share.',
                'push_title'   => 'Verify Your New Email',
                'push_body'    => 'Enter code {{code}} to confirm your new email address.',
                'shortcodes'   => json_encode([
                    'code'     => 'Verification code',
                    'username' => 'Account username',
                    'ip'       => 'Request IP address',
                    'time'     => 'Time of request',
                    'site_url' => 'Site URL',
                ]),
                'email_status' => 1,
                'sms_status'   => 0,
                'push_status'  => 0,
            ],
            [
                'act'          => 'EMAIL_CHANGE_ALERT',
                'name'         => 'Email Change - Security Alert (sent to old email)',
                'subject'      => 'Security Alert: Email Change Requested',
                'email_body'   => '<div style="font-family:Montserrat,sans-serif;line-height:1.6">
<p>Hello <strong>{{username}}</strong>,</p>
<p>A request was made to change the email address on your <strong>{{site_name}}</strong> account.</p>
<table style="border-collapse:collapse;width:100%;background:#f9f9f9;border-radius:8px;padding:12px;">
  <tr><td style="padding:6px 12px;font-weight:bold;">New email:</td><td style="padding:6px 12px;">{{new_email}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">Time:</td><td style="padding:6px 12px;">{{time}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">IP Address:</td><td style="padding:6px 12px;">{{ip}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">Browser:</td><td style="padding:6px 12px;">{{browser}}</td></tr>
</table>
<p style="color:#c0392b;font-weight:bold;">If you did not request this change, please contact support immediately to secure your account.</p>
</div>',
                'sms_body'     => 'SECURITY ALERT: A request to change your {{site_name}} email to {{new_email}} was made at {{time}}. Contact support if this was not you.',
                'push_title'   => 'Email Change Requested',
                'push_body'    => 'A request was made to change your email to {{new_email}}.',
                'shortcodes'   => json_encode([
                    'new_email' => 'The new email address requested',
                    'username'  => 'Account username',
                    'ip'        => 'Request IP address',
                    'browser'   => 'Browser used',
                    'time'      => 'Time of request',
                ]),
                'email_status' => 1,
                'sms_status'   => 0,
                'push_status'  => 1,
            ],
            [
                'act'          => 'EMAIL_CHANGED',
                'name'         => 'Email Changed - Confirmation',
                'subject'      => 'Your Email Address Has Been Updated',
                'email_body'   => '<div style="font-family:Montserrat,sans-serif;line-height:1.6">
<p>Hello <strong>{{username}}</strong>,</p>
<p>Your email address on <strong>{{site_name}}</strong> has been successfully updated.</p>
<table style="border-collapse:collapse;width:100%;background:#f9f9f9;border-radius:8px;padding:12px;">
  <tr><td style="padding:6px 12px;font-weight:bold;">Previous email:</td><td style="padding:6px 12px;">{{old_email}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">New email:</td><td style="padding:6px 12px;">{{new_email}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">Changed at:</td><td style="padding:6px 12px;">{{time}}</td></tr>
</table>
<p>If you did not make this change, please <a href="{{site_url}}">contact support</a> immediately.</p>
</div>',
                'sms_body'     => 'Your {{site_name}} email has been updated to {{new_email}}. Contact support if this was not you.',
                'push_title'   => 'Email Updated',
                'push_body'    => 'Your email address has been changed to {{new_email}}.',
                'shortcodes'   => json_encode([
                    'old_email' => 'Previous email address',
                    'new_email' => 'New email address',
                    'username'  => 'Account username',
                    'time'      => 'Time of change',
                    'site_url'  => 'Site URL',
                ]),
                'email_status' => 1,
                'sms_status'   => 0,
                'push_status'  => 1,
            ],
            [
                'act'          => 'PHONE_CHANGE_OTP',
                'name'         => 'Phone Change - Verification Code',
                'subject'      => 'Verify Your New Phone Number',
                'email_body'   => '<div style="font-family:Montserrat,sans-serif;line-height:1.6">
<p>Hello <strong>{{username}}</strong>,</p>
<p>We received a request to update the phone number on your <strong>{{site_name}}</strong> account to <strong>{{new_mobile}}</strong>.</p>
<p>Your verification code is:</p>
<h2 style="letter-spacing:6px;color:#1a1a1a;text-align:center;background:#f4f4f4;padding:16px;border-radius:8px;">{{code}}</h2>
<p>This code expires in <strong>10 minutes</strong>. Do not share it with anyone.</p>
<p style="color:#888;font-size:12px;">Requested at {{time}} &bull; IP: {{ip}}</p>
</div>',
                'sms_body'     => 'Your {{site_name}} phone change code is: {{code}}. Valid 10 min. Do not share.',
                'push_title'   => 'Verify Your New Phone',
                'push_body'    => 'Enter code {{code}} to confirm your new phone number.',
                'shortcodes'   => json_encode([
                    'code'       => 'Verification code',
                    'new_mobile' => 'New mobile number',
                    'username'   => 'Account username',
                    'ip'         => 'Request IP address',
                    'time'       => 'Time of request',
                ]),
                'email_status' => 1,
                'sms_status'   => 1,
                'push_status'  => 0,
            ],
            [
                'act'          => 'PHONE_CHANGE_ALERT',
                'name'         => 'Phone Change - Security Alert',
                'subject'      => 'Security Alert: Phone Number Change Requested',
                'email_body'   => '<div style="font-family:Montserrat,sans-serif;line-height:1.6">
<p>Hello <strong>{{username}}</strong>,</p>
<p>A request was made to change the phone number on your <strong>{{site_name}}</strong> account.</p>
<table style="border-collapse:collapse;width:100%;background:#f9f9f9;border-radius:8px;padding:12px;">
  <tr><td style="padding:6px 12px;font-weight:bold;">New number:</td><td style="padding:6px 12px;">{{new_mobile}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">Time:</td><td style="padding:6px 12px;">{{time}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">IP Address:</td><td style="padding:6px 12px;">{{ip}}</td></tr>
</table>
<p style="color:#c0392b;font-weight:bold;">If you did not request this change, please contact support immediately.</p>
</div>',
                'sms_body'     => 'ALERT: A request to change your {{site_name}} phone to {{new_mobile}} was received. Contact support if this was not you.',
                'push_title'   => 'Phone Change Requested',
                'push_body'    => 'A request was made to change your phone to {{new_mobile}}.',
                'shortcodes'   => json_encode([
                    'new_mobile' => 'The new phone number',
                    'username'   => 'Account username',
                    'ip'         => 'Request IP address',
                    'time'       => 'Time of request',
                ]),
                'email_status' => 1,
                'sms_status'   => 0,
                'push_status'  => 1,
            ],
            [
                'act'          => 'PHONE_CHANGED',
                'name'         => 'Phone Changed - Confirmation',
                'subject'      => 'Your Phone Number Has Been Updated',
                'email_body'   => '<div style="font-family:Montserrat,sans-serif;line-height:1.6">
<p>Hello <strong>{{username}}</strong>,</p>
<p>Your phone number on <strong>{{site_name}}</strong> has been successfully updated.</p>
<table style="border-collapse:collapse;width:100%;background:#f9f9f9;border-radius:8px;padding:12px;">
  <tr><td style="padding:6px 12px;font-weight:bold;">Previous number:</td><td style="padding:6px 12px;">{{old_mobile}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">New number:</td><td style="padding:6px 12px;">{{new_mobile}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">Changed at:</td><td style="padding:6px 12px;">{{time}}</td></tr>
</table>
<p>If you did not make this change, contact support immediately.</p>
</div>',
                'sms_body'     => 'Your {{site_name}} phone has been changed to {{new_mobile}}. Contact support if this was not you.',
                'push_title'   => 'Phone Updated',
                'push_body'    => 'Your phone number has been changed to {{new_mobile}}.',
                'shortcodes'   => json_encode([
                    'old_mobile' => 'Previous phone number',
                    'new_mobile' => 'New phone number',
                    'username'   => 'Account username',
                    'time'       => 'Time of change',
                ]),
                'email_status' => 1,
                'sms_status'   => 1,
                'push_status'  => 1,
            ],
            [
                'act'          => 'PROFILE_CHANGED',
                'name'         => 'Profile Information Updated',
                'subject'      => 'Your Profile Has Been Updated',
                'email_body'   => '<div style="font-family:Montserrat,sans-serif;line-height:1.6">
<p>Hello <strong>{{username}}</strong>,</p>
<p>Your profile information on <strong>{{site_name}}</strong> was updated successfully.</p>
<table style="border-collapse:collapse;width:100%;background:#f9f9f9;border-radius:8px;padding:12px;">
  <tr><td style="padding:6px 12px;font-weight:bold;">Fields updated:</td><td style="padding:6px 12px;">{{changed_fields}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">Time:</td><td style="padding:6px 12px;">{{time}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">IP Address:</td><td style="padding:6px 12px;">{{ip}}</td></tr>
  <tr><td style="padding:6px 12px;font-weight:bold;">Browser:</td><td style="padding:6px 12px;">{{browser}}</td></tr>
</table>
<p>If you did not make these changes, please <a href="{{site_url}}">contact support</a> immediately.</p>
</div>',
                'sms_body'     => 'Your {{site_name}} profile was updated at {{time}}. Contact support if this was not you.',
                'push_title'   => 'Profile Updated',
                'push_body'    => 'Your profile information has been updated: {{changed_fields}}.',
                'shortcodes'   => json_encode([
                    'changed_fields' => 'List of fields that were changed',
                    'username'       => 'Account username',
                    'ip'             => 'Request IP address',
                    'browser'        => 'Browser used',
                    'time'           => 'Time of change',
                    'site_url'       => 'Site URL',
                ]),
                'email_status' => 1,
                'sms_status'   => 0,
                'push_status'  => 1,
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['act' => $template['act']],
                $template
            );
        }
    }
}
