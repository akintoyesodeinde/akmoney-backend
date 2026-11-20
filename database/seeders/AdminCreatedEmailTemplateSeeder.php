<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class AdminCreatedEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Email sent to the newly created admin with their login credentials
        NotificationTemplate::updateOrCreate(
            ['act' => 'ADMIN_CREATED'],
            [
                'name'        => 'Admin Account Created - Welcome',
                'subject'     => 'Welcome to {{site_name}} Admin Panel',
                'email_body'  => '<div style="font-family:Montserrat,sans-serif">
<p>Hi <strong>{{name}}</strong>,</p>
<p>Your admin account on <strong>{{site_name}}</strong> has been created. Here are your login credentials:</p>
<table style="border-collapse:collapse;width:100%;max-width:400px">
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Username</td><td style="padding:6px 10px">{{username}}</td></tr>
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Role</td><td style="padding:6px 10px">{{role}}</td></tr>
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Password</td><td style="padding:6px 10px">{{password}}</td></tr>
</table>
<p style="margin-top:16px">Please log in and change your password immediately.</p>
<p><a href="{{login_url}}" style="display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px">Login to Admin Panel</a></p>
<p style="color:#888;font-size:12px">If you did not expect this email, please contact the Super Admin.</p>
</div>',
                'sms_body'    => 'Your {{site_name}} admin account has been created. Username: {{username}}, Role: {{role}}. Login: {{login_url}}',
                'shortcodes'  => json_encode([
                    'name'      => 'Full name of the new admin',
                    'username'  => 'Username of the new admin',
                    'role'      => 'Role assigned to the admin',
                    'password'  => 'Temporary password',
                    'login_url' => 'URL to the admin login page',
                ]),
                'email_status' => 1,
                'sms_status'   => 0,
                'push_status'  => 0,
            ]
        );

        // Email sent to all Super Admins when a new admin is created
        NotificationTemplate::updateOrCreate(
            ['act' => 'ADMIN_CREATED_ALERT'],
            [
                'name'        => 'Admin Account Created - Super Admin Alert',
                'subject'     => 'New Admin Account Created on {{site_name}}',
                'email_body'  => '<div style="font-family:Montserrat,sans-serif">
<p>Hi <strong>{{super_admin_name}}</strong>,</p>
<p>A new admin account has been created on <strong>{{site_name}}</strong>.</p>
<table style="border-collapse:collapse;width:100%;max-width:400px">
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Name</td><td style="padding:6px 10px">{{name}}</td></tr>
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Username</td><td style="padding:6px 10px">{{username}}</td></tr>
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Email</td><td style="padding:6px 10px">{{email}}</td></tr>
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Role</td><td style="padding:6px 10px">{{role}}</td></tr>
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Created By</td><td style="padding:6px 10px">{{created_by}}</td></tr>
  <tr><td style="padding:6px 10px;background:#f4f4f4;font-weight:600">Created At</td><td style="padding:6px 10px">{{created_at}}</td></tr>
</table>
<p style="margin-top:16px"><a href="{{admin_url}}" style="display:inline-block;padding:10px 20px;background:#28a745;color:#fff;text-decoration:none;border-radius:4px">View Admin Accounts</a></p>
</div>',
                'sms_body'    => 'New admin created on {{site_name}}: {{name}} ({{role}}) by {{created_by}}.',
                'shortcodes'  => json_encode([
                    'super_admin_name' => 'Name of the super admin receiving this alert',
                    'name'             => 'Full name of the new admin',
                    'username'         => 'Username of the new admin',
                    'email'            => 'Email of the new admin',
                    'role'             => 'Role assigned to the new admin',
                    'created_by'       => 'Name of the admin who created this account',
                    'created_at'       => 'Date and time the account was created',
                    'admin_url'        => 'URL to the admin accounts management page',
                ]),
                'email_status' => 1,
                'sms_status'   => 0,
                'push_status'  => 0,
            ]
        );
    }
}
