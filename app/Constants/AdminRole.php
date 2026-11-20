<?php

namespace App\Constants;

class AdminRole
{
    const SUPER_ADMIN         = 'super_admin';
    const OPERATIONS_MANAGER  = 'operations_manager';
    const COMPLIANCE_OFFICER  = 'compliance_officer';
    const FINANCE_ACCOUNTANT  = 'finance_accountant';
    const TECHNICAL_SUPPORT   = 'technical_support';
    const CUSTOMER_SUPPORT    = 'customer_support';
    const AGENT_MANAGER       = 'agent_manager';
    const SECURITY_ADMIN      = 'security_admin';

    const LABELS = [
        self::SUPER_ADMIN        => 'Super Admin',
        self::OPERATIONS_MANAGER => 'Operations Manager',
        self::COMPLIANCE_OFFICER => 'Compliance Officer',
        self::FINANCE_ACCOUNTANT => 'Finance / Accountant',
        self::TECHNICAL_SUPPORT  => 'Technical / Admin Support',
        self::CUSTOMER_SUPPORT   => 'Customer Support',
        self::AGENT_MANAGER      => 'Agent Manager',
        self::SECURITY_ADMIN     => 'Security Admin',
    ];

    // Modules that map to permission keys
    const MODULES = [
        'dashboard'          => 'Dashboard',
        'manage_users'       => 'Manage Users',
        'manage_agents'      => 'Manage Agents',
        'remittance_options' => 'Remittance Options',
        'manage_countries'   => 'Manage Countries',
        'payments'           => 'Payments',
        'send_money'         => 'Send Money',
        'agent_deposits'     => 'Agent Deposits',
        'withdrawals'        => 'Withdrawals',
        'support_ticket'     => 'Support Ticket',
        'security_logs'      => 'Security Logs',
        'system_settings'    => 'System Settings',
        'cache_management'   => 'Cache Management',
        'compliance'         => 'Compliance',
        'manage_admins'      => 'Manage Admins',
    ];

    // Default permissions for each role [module => [view, create, edit, delete, approve]]
    const DEFAULT_PERMISSIONS = [
        self::SUPER_ADMIN => [], // Handled by bypass in middleware

        self::OPERATIONS_MANAGER => [
            'dashboard'          => [true,  false, false, false, false],
            'manage_users'       => [true,  false, true,  false, true],
            'manage_agents'      => [true,  false, true,  false, true],
            'remittance_options' => [true,  true,  true,  false, false],
            'manage_countries'   => [true,  false, true,  false, false],
            'payments'           => [true,  false, false, false, true],
            'send_money'         => [true,  false, false, false, true],
            'agent_deposits'     => [true,  false, false, false, true],
            'withdrawals'        => [true,  false, false, false, true],
            'support_ticket'     => [true,  false, true,  false, false],
            'security_logs'      => [true,  false, false, false, false],
            'system_settings'    => [false, false, false, false, false],
            'cache_management'   => [false, false, false, false, false],
            'compliance'         => [true,  false, false, false, false],
            'manage_admins'      => [false, false, false, false, false],
        ],

        self::COMPLIANCE_OFFICER => [
            'dashboard'          => [true,  false, false, false, false],
            'manage_users'       => [true,  false, false, false, false],
            'manage_agents'      => [true,  false, false, false, false],
            'remittance_options' => [false, false, false, false, false],
            'manage_countries'   => [false, false, false, false, false],
            'payments'           => [true,  false, false, false, false],
            'send_money'         => [true,  false, false, false, false],
            'agent_deposits'     => [true,  false, false, false, false],
            'withdrawals'        => [true,  false, false, false, false],
            'support_ticket'     => [false, false, false, false, false],
            'security_logs'      => [true,  false, false, false, false],
            'system_settings'    => [false, false, false, false, false],
            'cache_management'   => [false, false, false, false, false],
            'compliance'         => [true,  true,  true,  false, true],
            'manage_admins'      => [false, false, false, false, false],
        ],

        self::FINANCE_ACCOUNTANT => [
            'dashboard'          => [true,  false, false, false, false],
            'manage_users'       => [true,  false, false, false, false],
            'manage_agents'      => [true,  false, false, false, false],
            'remittance_options' => [false, false, false, false, false],
            'manage_countries'   => [false, false, false, false, false],
            'payments'           => [true,  false, false, false, true],
            'send_money'         => [true,  false, false, false, false],
            'agent_deposits'     => [true,  false, false, false, true],
            'withdrawals'        => [true,  false, false, false, true],
            'support_ticket'     => [false, false, false, false, false],
            'security_logs'      => [false, false, false, false, false],
            'system_settings'    => [false, false, false, false, false],
            'cache_management'   => [false, false, false, false, false],
            'compliance'         => [true,  false, false, false, false],
            'manage_admins'      => [false, false, false, false, false],
        ],

        self::TECHNICAL_SUPPORT => [
            'dashboard'          => [true,  false, false, false, false],
            'manage_users'       => [true,  false, false, false, false],
            'manage_agents'      => [true,  false, false, false, false],
            'remittance_options' => [true,  true,  true,  true,  false],
            'manage_countries'   => [true,  true,  true,  false, false],
            'payments'           => [true,  false, false, false, false],
            'send_money'         => [true,  false, false, false, false],
            'agent_deposits'     => [true,  false, false, false, false],
            'withdrawals'        => [true,  false, false, false, false],
            'support_ticket'     => [true,  false, true,  false, false],
            'security_logs'      => [true,  false, false, false, false],
            'system_settings'    => [true,  true,  true,  false, false],
            'cache_management'   => [true,  false, false, false, false],
            'compliance'         => [false, false, false, false, false],
            'manage_admins'      => [false, false, false, false, false],
        ],

        self::CUSTOMER_SUPPORT => [
            'dashboard'          => [true,  false, false, false, false],
            'manage_users'       => [true,  false, true,  false, false],
            'manage_agents'      => [false, false, false, false, false],
            'remittance_options' => [false, false, false, false, false],
            'manage_countries'   => [false, false, false, false, false],
            'payments'           => [true,  false, false, false, false],
            'send_money'         => [true,  false, false, false, false],
            'agent_deposits'     => [false, false, false, false, false],
            'withdrawals'        => [true,  false, false, false, false],
            'support_ticket'     => [true,  true,  true,  false, false],
            'security_logs'      => [false, false, false, false, false],
            'system_settings'    => [false, false, false, false, false],
            'cache_management'   => [false, false, false, false, false],
            'compliance'         => [false, false, false, false, false],
            'manage_admins'      => [false, false, false, false, false],
        ],

        self::AGENT_MANAGER => [
            'dashboard'          => [true,  false, false, false, false],
            'manage_users'       => [false, false, false, false, false],
            'manage_agents'      => [true,  true,  true,  false, true],
            'remittance_options' => [true,  false, false, false, false],
            'manage_countries'   => [true,  false, false, false, false],
            'payments'           => [false, false, false, false, false],
            'send_money'         => [true,  false, false, false, false],
            'agent_deposits'     => [true,  false, false, false, true],
            'withdrawals'        => [true,  false, false, false, false],
            'support_ticket'     => [true,  false, true,  false, false],
            'security_logs'      => [false, false, false, false, false],
            'system_settings'    => [false, false, false, false, false],
            'cache_management'   => [false, false, false, false, false],
            'compliance'         => [false, false, false, false, false],
            'manage_admins'      => [false, false, false, false, false],
        ],

        self::SECURITY_ADMIN => [
            'dashboard'          => [true,  false, false, false, false],
            'manage_users'       => [true,  false, false, false, false],
            'manage_agents'      => [true,  false, false, false, false],
            'remittance_options' => [false, false, false, false, false],
            'manage_countries'   => [false, false, false, false, false],
            'payments'           => [true,  false, false, false, false],
            'send_money'         => [true,  false, false, false, false],
            'agent_deposits'     => [false, false, false, false, false],
            'withdrawals'        => [true,  false, false, false, false],
            'support_ticket'     => [false, false, false, false, false],
            'security_logs'      => [true,  false, false, true,  false],
            'system_settings'    => [true,  false, true,  false, false],
            'cache_management'   => [true,  false, false, false, false],
            'compliance'         => [true,  false, false, false, false],
            'manage_admins'      => [false, false, false, false, false],
        ],
    ];
}
