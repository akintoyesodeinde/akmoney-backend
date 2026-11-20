<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if (!Schema::hasTable('admin_notifications')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `admin_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `click_url` text DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `triggered_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_notifications_module_index` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('admin_password_resets')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `admin_password_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('admin_role_permissions')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `admin_role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_create` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `can_approve` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_role_permissions_role_module_unique` (`role`,`module`),
  KEY `admin_role_permissions_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('admins')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(255) NOT NULL DEFAULT \'super_admin\',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('agent_password_resets')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `agent_password_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('agents')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `agents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL DEFAULT 0,
  `firstname` varchar(40) DEFAULT NULL,
  `lastname` varchar(40) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email` varchar(40) NOT NULL,
  `dial_code` varchar(40) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `ref_by` int(10) unsigned NOT NULL DEFAULT 0,
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL COMMENT \'contains full address\',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT \'0: banned, 1: active\',
  `kyc_data` text DEFAULT NULL,
  `kyc_rejection_reason` varchar(255) DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: KYC Unverified, 2: KYC pending, 1: KYC verified\',
  `ts` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: 2fa off, 1: 2fa on\',
  `tv` tinyint(1) NOT NULL DEFAULT 1 COMMENT \'0: 2fa unverified, 1: 2fa verified\',
  `tsc` varchar(255) DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('aml_risk_scores')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `aml_risk_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `transaction_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `risk_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `risk_level` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'low\',
  `risk_factors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `is_reviewed` tinyint(4) NOT NULL DEFAULT 0,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('backup_codes')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `backup_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `code` varchar(64) NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('countries')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `dial_code` varchar(255) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `image` varchar(255) DEFAULT NULL,
  `is_sending` tinyint(1) NOT NULL DEFAULT 1,
  `is_receiving` tinyint(1) NOT NULL DEFAULT 1,
  `has_agent` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('country_delivery_method')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `country_delivery_method` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL DEFAULT 0,
  `delivery_method_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`),
  KEY `delivery_method_id` (`delivery_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('cron_job_logs')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `cron_job_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cron_job_id` int(10) unsigned NOT NULL DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('cron_jobs')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `cron_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `cron_schedule_id` int(11) NOT NULL DEFAULT 0,
  `next_run` datetime DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `is_running` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('cron_schedules')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `cron_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `interval` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('currency_conversion_rates')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `currency_conversion_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_country` int(10) unsigned NOT NULL DEFAULT 0,
  `to_country` int(10) unsigned NOT NULL DEFAULT 0,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('delivery_charges')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `delivery_charges` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `country_delivery_method_id` int(10) unsigned NOT NULL DEFAULT 0,
  `fixed_charge` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) unsigned NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('delivery_methods')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `delivery_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('deposits')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `deposits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `send_money_id` int(11) NOT NULL DEFAULT 0,
  `wallet_id` bigint(20) unsigned DEFAULT NULL,
  `method_code` int(10) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `method_currency` varchar(40) DEFAULT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `final_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `detail` text DEFAULT NULL,
  `btc_amount` varchar(255) DEFAULT NULL,
  `btc_wallet` varchar(255) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `payment_try` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'1=>success, 2=>pending, 3=>cancel\',
  `from_api` tinyint(1) NOT NULL DEFAULT 0,
  `is_web` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'This will be 1 if the request is from NextJs application\',
  `admin_feedback` text DEFAULT NULL,
  `receipt_required` tinyint(4) NOT NULL DEFAULT 0,
  `receipt_path` varchar(255) DEFAULT NULL,
  `receipt_verified_at` timestamp NULL DEFAULT NULL,
  `success_url` varchar(255) DEFAULT NULL,
  `failed_url` varchar(255) DEFAULT NULL,
  `last_cron` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('device_tokens')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `device_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_app` tinyint(1) NOT NULL DEFAULT 0,
  `token` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('extensions')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `extensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `script` text DEFAULT NULL,
  `shortcode` text DEFAULT NULL COMMENT \'object\',
  `support` text DEFAULT NULL COMMENT \'help section\',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT \'1=>enable, 2=>disable\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('forms')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `forms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `form_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('frontends')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `frontends` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data_keys` varchar(40) DEFAULT NULL,
  `data_values` longtext DEFAULT NULL,
  `seo_content` longtext DEFAULT NULL,
  `tempname` varchar(40) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('gateway_currencies')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `gateway_currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `symbol` varchar(40) DEFAULT NULL,
  `method_code` int(11) DEFAULT NULL,
  `gateway_alias` varchar(40) DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `max_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `gateway_parameter` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('gateways')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `gateways` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned NOT NULL DEFAULT 0,
  `code` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) NOT NULL DEFAULT \'NULL\',
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT \'1=>enable, 2=>disable\',
  `gateway_parameters` text DEFAULT NULL,
  `supported_currencies` text DEFAULT NULL,
  `crypto` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: fiat currency, 1: crypto currency\',
  `extra` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('general_settings')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `general_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_name` varchar(40) DEFAULT NULL,
  `cur_text` varchar(40) DEFAULT NULL COMMENT \'currency text\',
  `cur_sym` varchar(40) DEFAULT NULL COMMENT \'currency symbol\',
  `email_from` varchar(40) DEFAULT NULL,
  `email_from_name` varchar(255) DEFAULT NULL,
  `email_template` text DEFAULT NULL,
  `sms_template` varchar(255) DEFAULT NULL,
  `sms_from` varchar(255) DEFAULT NULL,
  `push_title` varchar(255) DEFAULT NULL,
  `push_template` varchar(255) DEFAULT NULL,
  `base_color` varchar(40) DEFAULT NULL,
  `agent_fixed_commission` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `agent_percent_commission` decimal(5,2) NOT NULL DEFAULT 0.00,
  `referral_commission` decimal(5,2) unsigned NOT NULL DEFAULT 0.00,
  `commission_count` int(10) unsigned NOT NULL DEFAULT 0,
  `user_send_money_limit` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `user_daily_send_money_limit` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `user_monthly_send_money_limit` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `agent_send_money_limit` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `agent_daily_send_money_limit` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `agent_monthly_send_money_limit` decimal(28,8) unsigned NOT NULL DEFAULT 0.00000000,
  `resent_code_duration` int(11) NOT NULL DEFAULT 0,
  `mail_config` text DEFAULT NULL COMMENT \'email configuration\',
  `sms_config` text DEFAULT NULL,
  `global_shortcodes` text DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0,
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'email verification, 0 - dont check, 1 - check\',
  `en` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'email notification, 0 - dont send, 1 - send\',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'mobile verication, 0 - dont check, 1 - check\',
  `sn` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'sms notification, 0 - dont send, 1 - send\',
  `pn` tinyint(1) NOT NULL DEFAULT 1,
  `force_ssl` tinyint(1) NOT NULL DEFAULT 0,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `secure_password` tinyint(1) NOT NULL DEFAULT 0,
  `agree` tinyint(1) NOT NULL DEFAULT 0,
  `multi_language` tinyint(1) NOT NULL DEFAULT 1,
  `registration` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: Off	, 1: On\',
  `active_template` varchar(40) DEFAULT NULL,
  `system_customized` tinyint(1) NOT NULL DEFAULT 0,
  `paginate_number` int(11) NOT NULL DEFAULT 0,
  `currency_format` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'1=>Both\\r\\n2=>Text Only\\r\\n3=>Symbol Only\',
  `config_progress` text DEFAULT NULL,
  `last_cron` datetime DEFAULT NULL,
  `available_version` varchar(40) DEFAULT NULL,
  `kyc_modules` longtext DEFAULT NULL,
  `referral_system` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `agent_module` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `wallet_module` tinyint(4) NOT NULL DEFAULT 1,
  `wallet_currency_mode` tinyint(4) NOT NULL DEFAULT 1,
  `wallet_allowed_currencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`wallet_allowed_currencies`)),
  `wallet_transfer` tinyint(4) NOT NULL DEFAULT 1,
  `wallet_transfer_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`wallet_transfer_methods`)),
  `wallet_transfer_fixed_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wallet_transfer_percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `wallet_exchange_fixed_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wallet_exchange_percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `agent_charges` text DEFAULT NULL,
  `firebase_config` text DEFAULT NULL,
  `push_notify` tinyint(1) NOT NULL DEFAULT 1,
  `conversion_rate_api` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_blacklist` text DEFAULT NULL,
  `ip_whitelist` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('kyc_providers')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `kyc_providers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `api_key` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `secret_key` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credential_schema` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `help_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kyc_providers_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('languages')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `code` varchar(40) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: not default language, 1: default language\',
  `image` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('notification_logs')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `notification_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender` varchar(40) DEFAULT NULL,
  `sent_from` varchar(40) DEFAULT NULL,
  `sent_to` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `notification_type` varchar(40) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_read` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('notification_templates')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `notification_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `push_title` varchar(255) DEFAULT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `push_body` text DEFAULT NULL,
  `shortcodes` text DEFAULT NULL,
  `email_status` tinyint(1) NOT NULL DEFAULT 1,
  `email_sent_from_name` varchar(40) DEFAULT NULL,
  `email_sent_from_address` varchar(40) DEFAULT NULL,
  `sms_status` tinyint(1) NOT NULL DEFAULT 1,
  `sms_sent_from` varchar(40) DEFAULT NULL,
  `push_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('notifications')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `model_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `action` enum(\'created\',\'updated\') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_model` (`model_type`,`model_id`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('paga_api_settings')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `paga_api_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `principal` varchar(255) DEFAULT NULL,
  `credentials` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `environment` varchar(10) NOT NULL DEFAULT \'sandbox\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('pages')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `slug` varchar(40) DEFAULT NULL,
  `tempname` varchar(40) DEFAULT NULL COMMENT \'template name\',
  `secs` text DEFAULT NULL,
  `seo_content` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('password_resets')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('payout_api_countries')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `payout_api_countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_code` varchar(3) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `payout_provider` varchar(20) NOT NULL DEFAULT \'manual\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('payout_api_country_methods')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `payout_api_country_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payout_api_country_id` bigint(20) unsigned NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `pawapay_provider` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payout_api_country_methods_payout_api_country_id_foreign` (`payout_api_country_id`),
  CONSTRAINT `payout_api_country_methods_payout_api_country_id_foreign` FOREIGN KEY (`payout_api_country_id`) REFERENCES `payout_api_countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('payout_api_logs')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `payout_api_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `send_money_id` bigint(20) unsigned NOT NULL,
  `event` varchar(50) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payout_api_logs_send_money_id_index` (`send_money_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('payout_api_settings')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `payout_api_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `secret_key` text DEFAULT NULL,
  `public_key` text DEFAULT NULL,
  `encryption_key` text DEFAULT NULL,
  `environment` varchar(20) NOT NULL DEFAULT \'sandbox\',
  `webhook_secret` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('profile_audit_logs')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `profile_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(60) NOT NULL,
  `field` varchar(60) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_audit_logs_user_id_created_at_index` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('recaptcha_logs')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `recaptcha_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `url` varchar(500) NOT NULL,
  `action` varchar(100) DEFAULT NULL,
  `score` decimal(3,2) DEFAULT NULL,
  `success` tinyint(1) NOT NULL,
  `error_codes` varchar(500) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recaptcha_logs_ip_index` (`ip`),
  KEY `recaptcha_logs_success_index` (`success`),
  KEY `recaptcha_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('recipients')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `recipients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `country_id` bigint(20) unsigned DEFAULT NULL,
  `delivery_method_id` bigint(20) unsigned DEFAULT NULL,
  `service_id` bigint(20) unsigned DEFAULT NULL,
  `service_form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`service_form_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('sanctions_screening_results')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `sanctions_screening_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `screening_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'clear\',
  `provider` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'internal\',
  `match_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `screened_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sanctions_screening_results_user_id_screening_type_index` (`user_id`,`screening_type`),
  KEY `sanctions_screening_results_status_screening_type_index` (`status`,`screening_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('send_money')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `send_money` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mtcn_number` varchar(40) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `tx_hash` varchar(256) DEFAULT NULL,
  `country_delivery_method_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT \'receiver country delivery method\',
  `service_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT \'receiver country service\',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `service_form_data` text DEFAULT NULL COMMENT \'receiver country service form\',
  `base_currency_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `base_currency_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `sending_country_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sending_currency` varchar(255) DEFAULT NULL,
  `sending_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `sending_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `recipient_country_id` int(10) unsigned NOT NULL,
  `recipient_currency` varchar(255) DEFAULT NULL,
  `recipient_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `conversion_rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `base_currency_rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `sender` longtext DEFAULT NULL,
  `recipient` longtext DEFAULT NULL,
  `source_of_fund_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sending_purpose_id` int(10) unsigned NOT NULL DEFAULT 0,
  `verification_code` varchar(40) DEFAULT NULL,
  `verification_time` datetime NOT NULL DEFAULT current_timestamp(),
  `payout_by` int(10) unsigned DEFAULT 0,
  `payout_channel` varchar(10) DEFAULT NULL,
  `payout_api_method_id` bigint(20) unsigned DEFAULT NULL,
  `api_payout_id` varchar(255) DEFAULT NULL,
  `api_status` varchar(50) DEFAULT NULL,
  `api_initiated_at` timestamp NULL DEFAULT NULL,
  `payment_status` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT \'0 = initiated; 1 = success; 2 = pending; 3 = rejected\',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT \'0 = initiated, 1 = completed, 2 = pending, 3 = refunded	\',
  `admin_feedback` longtext DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sending_country_id` (`sending_country_id`),
  KEY `recipient_country_id` (`recipient_country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('sending_purposes')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `sending_purposes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('services')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_delivery_method_id` int(10) unsigned NOT NULL DEFAULT 0,
  `form_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('source_of_funds')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `source_of_funds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('support_attachments')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `support_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_message_id` int(10) unsigned NOT NULL DEFAULT 0,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('support_messages')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `support_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` int(10) unsigned NOT NULL DEFAULT 0,
  `admin_id` int(10) unsigned NOT NULL DEFAULT 0,
  `message` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('support_tickets')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `ticket` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: Open, 1: Answered, 2: Replied, 3: Closed\',
  `priority` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'1 = Low, 2 = medium, 3 = heigh\',
  `last_reply` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('transactions')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `wallet_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `post_balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx_type` varchar(40) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `remark` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('update_logs')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `update_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(40) DEFAULT NULL,
  `update_log` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('user_logins')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `user_logins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_ip` varchar(40) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `longitude` varchar(40) DEFAULT NULL,
  `latitude` varchar(40) DEFAULT NULL,
  `browser` varchar(40) DEFAULT NULL,
  `os` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('users')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pending_email` varchar(255) DEFAULT NULL,
  `dial_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mobile` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pending_mobile` varchar(255) DEFAULT NULL,
  `pending_mobile_code` varchar(255) DEFAULT NULL,
  `current_mode` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'light\',
  `ref_by` int(10) unsigned NOT NULL DEFAULT 0,
  `total_bonus_given` int(10) unsigned DEFAULT 0,
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'contains full address\',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT \'0: banned, 1: active\',
  `type` tinyint(1) DEFAULT 0,
  `business_profile_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kyc_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kyc_approve_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kyc_approve_attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `kyc_rejection_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kyc_rejection_attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: KYC Unverified, 2: KYC pending, 1: KYC verified	\',
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: email unverified, 1: email verified\',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: mobile unverified, 1: mobile verified\',
  `profile_complete` tinyint(1) NOT NULL DEFAULT 0,
  `ver_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'stores verification code\',
  `profile_change_code` varchar(6) DEFAULT NULL,
  `profile_change_code_sent_at` timestamp NULL DEFAULT NULL,
  `email_change_at` timestamp NULL DEFAULT NULL,
  `mobile_change_at` timestamp NULL DEFAULT NULL,
  `ver_code_send_at` datetime DEFAULT NULL COMMENT \'verification send time\',
  `ts` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'0: 2fa off, 1: 2fa on\',
  `tv` tinyint(1) NOT NULL DEFAULT 1 COMMENT \'0: 2fa unverified, 1: 2fa verified\',
  `tsc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_fa_method` varchar(10) DEFAULT NULL,
  `force_tfa` tinyint(4) NOT NULL DEFAULT 0,
  `ban_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `per_send_money_limit` decimal(28,8) DEFAULT NULL,
  `daily_send_money_limit` decimal(28,8) DEFAULT NULL,
  `monthly_send_money_limit` decimal(28,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `has_pin` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'Whether the user has set a PIN\',
  `pin` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'Hashed 6-digit PIN\',
  `security_question_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'Selected security question ID\',
  `security_answer_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'Hashed answer to security question\',
  `pin_failed_attempts` int(11) NOT NULL DEFAULT 0 COMMENT \'Failed PIN attempts counter\',
  `pin_locked_until` datetime DEFAULT NULL COMMENT \'Timestamp until which PIN is locked\',
  `pin_set_at` datetime DEFAULT NULL COMMENT \'Timestamp when the PIN was set\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('wallet_exchanges')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `wallet_exchanges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trx` varchar(40) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `from_wallet_id` bigint(20) unsigned NOT NULL,
  `to_wallet_id` bigint(20) unsigned NOT NULL,
  `from_currency` varchar(10) NOT NULL,
  `to_currency` varchar(10) NOT NULL,
  `from_amount` decimal(28,8) NOT NULL,
  `to_amount` decimal(28,8) NOT NULL,
  `exchange_rate` decimal(28,8) NOT NULL,
  `fee` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wallet_exchanges_trx_unique` (`trx`),
  KEY `wallet_exchanges_from_wallet_id_foreign` (`from_wallet_id`),
  KEY `wallet_exchanges_to_wallet_id_foreign` (`to_wallet_id`),
  KEY `wallet_exchanges_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('wallet_transfers')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `wallet_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trx` varchar(40) NOT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `receiver_id` bigint(20) unsigned NOT NULL,
  `sender_wallet_id` bigint(20) unsigned NOT NULL,
  `receiver_wallet_id` bigint(20) unsigned NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `amount` decimal(18,8) NOT NULL,
  `charge` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `sender_deducted` decimal(18,8) NOT NULL,
  `receiver_credited` decimal(18,8) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wallet_transfers_trx_unique` (`trx`),
  KEY `wallet_transfers_sender_wallet_id_foreign` (`sender_wallet_id`),
  KEY `wallet_transfers_receiver_wallet_id_foreign` (`receiver_wallet_id`),
  KEY `wallet_transfers_sender_id_created_at_index` (`sender_id`,`created_at`),
  KEY `wallet_transfers_receiver_id_created_at_index` (`receiver_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('wallets')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `wallets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `currency_name` varchar(60) NOT NULL,
  `currency_symbol` varchar(10) NOT NULL DEFAULT \'\',
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `remittance_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wallets_user_id_currency_code_unique` (`user_id`,`currency_code`),
  KEY `wallets_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('withdraw_methods')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `withdraw_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `min_limit` decimal(28,8) DEFAULT 0.00000000,
  `max_limit` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `fixed_charge` decimal(28,8) DEFAULT 0.00000000,
  `rate` decimal(28,8) DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('withdrawals')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `withdrawals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `method_id` int(10) unsigned NOT NULL DEFAULT 0,
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `currency` varchar(40) DEFAULT NULL,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx` varchar(40) DEFAULT NULL,
  `final_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `after_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `withdraw_information` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT \'1=>success, 2=>pending, 3=>cancel,  \',
  `admin_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('sessions')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `sessions` (
        `id` varchar(255) NOT NULL,
        `user_id` bigint unsigned DEFAULT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` text,
        `payload` longtext NOT NULL,
        `last_activity` int NOT NULL,
        PRIMARY KEY (`id`),
        KEY `sessions_user_id_index` (`user_id`),
        KEY `sessions_last_activity_index` (`last_activity`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('cache')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `cache` (
        `key` varchar(255) NOT NULL,
        `value` mediumtext NOT NULL,
        `expiration` int NOT NULL,
        PRIMARY KEY (`key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('cache_locks')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `cache_locks` (
        `key` varchar(255) NOT NULL,
        `owner` varchar(255) NOT NULL,
        `expiration` int NOT NULL,
        PRIMARY KEY (`key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        }

        if (!Schema::hasTable('jobs')) {
            DB::unprepared('CREATE TABLE IF NOT EXISTS `jobs` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT,
        `queue` varchar(255) NOT NULL,
        `payload` longtext NOT NULL,
        `attempts` tinyint unsigned NOT NULL,
        `reserved_at` int unsigned DEFAULT NULL,
        `available_at` int unsigned NOT NULL,
        `created_at` int unsigned NOT NULL,
        PRIMARY KEY (`id`),
        KEY `jobs_queue_index` (`queue`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        }

        if (Schema::hasTable('general_settings') && DB::table('general_settings')->count() === 0) {
            DB::table('general_settings')->insert(array (
  'id' => 1,
  'site_name' => 'AkMoney',
  'cur_text' => 'CAD',
  'cur_sym' => 'CA$',
  'email_from' => 'notifications@kinmoney.ca',
  'email_from_name' => 'Ak Money',
  'email_template' => 'Ak Money Notification

  @import url(\'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap\');
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { background: #F2F4F7; font-family: \'Inter\', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
  .wrapper { background: #F2F4F7; padding: 40px 16px; min-height: 100vh; }
  .card { background: #ffffff; border-radius: 20px; max-width: 580px; margin: 0 auto; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
  .header { background: #0C0C0C; padding: 32px 40px 28px; text-align: center; }
  .logo-wrap { text-align: center; }
  .header-sub { color: rgba(255,255,255,0.45); font-size: 12px; margin-top: 10px; letter-spacing: 0.05em; text-transform: uppercase; }
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
  .footer-copy { font-size: 11px; color: rgba(255,255,255,0.3); line-height: 1.7; }
  .footer-badges { display: inline-flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin: 12px 0; }
  .footer-badge { display: inline-block; padding: 3px 10px; border: 1px solid rgba(255,255,255,0.1); border-radius: 999px; font-size: 10px; color: rgba(255,255,255,0.4); font-weight: 600; }
  @media (max-width: 600px) {
    .body { padding: 28px 24px 24px; }
    .header { padding: 24px 24px 20px; }
    .footer { padding: 24px; }
  }




  
    
      
        
      
      Secure Money Transfers
    
    
    
      Hello, {{fullname}}
      
        {{message}}
      
      
        If you did not request this notification or have any questions, please contact our support team at support@kinmoney.ca or call +1234567890.
      
    
    
      
        Dashboard
        Contact Us
        Privacy Policy
        Terms of Service
      
      
        FINTRAC Regulated
        MSB #C10000108
        256-bit SSL
        AML Compliant
      
      
        &copy; 2025 {{site_name}}
        Calgary, Alberta, Canada
        This email was sent to {{username}}. Do not reply to this email.',
  'sms_template' => 'hi {{fullname}} ({{username}}), {{message}}',
  'sms_from' => 'Tahweelak',
  'push_title' => NULL,
  'push_template' => NULL,
  'base_color' => 'A71B47',
  'agent_fixed_commission' => '1.00000000',
  'agent_percent_commission' => '1.00',
  'referral_commission' => '10.00',
  'commission_count' => 1,
  'user_send_money_limit' => '1000.00000000',
  'user_daily_send_money_limit' => '5000.00000000',
  'user_monthly_send_money_limit' => '50000.00000000',
  'agent_send_money_limit' => '1000.00000000',
  'agent_daily_send_money_limit' => '10000.00000000',
  'agent_monthly_send_money_limit' => '100000.00000000',
  'resent_code_duration' => 120,
  'mail_config' => NULL,
  'sms_config' => NULL,
  'global_shortcodes' => '{
    "site_name":"Name of your site",
    "site_currency":"Currency of your site",
    "currency_symbol":"Symbol of currency"
}',
  'kv' => 1,
  'ev' => 0,
  'en' => 0,
  'sv' => 1,
  'sn' => 1,
  'pn' => 0,
  'force_ssl' => 1,
  'maintenance_mode' => 0,
  'secure_password' => 0,
  'agree' => 0,
  'multi_language' => 1,
  'registration' => 1,
  'active_template' => 'basic',
  'system_customized' => 0,
  'paginate_number' => 20,
  'currency_format' => 2,
  'config_progress' => '["general_setting","logo_favicon","seo","policy_content","deposit_method","notification_template"]',
  'last_cron' => '2026-06-01 23:44:11',
  'available_version' => '1',
  'kyc_modules' => NULL,
  'referral_system' => 1,
  'agent_module' => 1,
  'wallet_module' => 1,
  'wallet_currency_mode' => 1,
  'wallet_allowed_currencies' => NULL,
  'wallet_transfer' => 1,
  'wallet_transfer_methods' => '["username","mobile"]',
  'wallet_transfer_fixed_charge' => '0.00',
  'wallet_transfer_percent_charge' => '1.00',
  'wallet_exchange_fixed_charge' => '0.00',
  'wallet_exchange_percent_charge' => '1.00',
  'agent_charges' => '{"fixed_charge":"3","percent_charge":"2"}',
  'firebase_config' => NULL,
  'push_notify' => 1,
  'conversion_rate_api' => '{"status":"on","provider":"exchangerate","exchangerate":{"api_key":null},"fixed_adjustment":"0","percent_adjustment":"1","auto_synchronization":true}',
  'created_at' => NULL,
  'updated_at' => '2026-06-02 16:17:02',
  'ip_blacklist' => NULL,
  'ip_whitelist' => NULL,
));
        }

        if (Schema::hasTable('migrations')) {
            foreach (glob(database_path('migrations/*.php')) as $migFile) {
                $migName = basename($migFile, '.php');
                if ($migName !== '2026_01_01_000000_create_akmoney_base_schema_tables' && !DB::table('migrations')->where('migration', $migName)->exists()) {
                    DB::table('migrations')->insert(['migration' => $migName, 'batch' => 1]);
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        // Baseline schema tables preserved on down
    }
};
