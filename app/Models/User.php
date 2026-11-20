<?php

namespace App\Models;

use App\Constants\Status;
use App\Models\Wallet;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\NotifiesChanges;

class User extends Authenticatable
{
    use HasApiTokens, UserNotify, NotifiesChanges;
    
    protected $fillable = ['email', 'firstname', 'lastname', 'ev', 'sv', 'tv', 'pin', 'kyc_rejection_attachments', 'kyc_approve_attachments',
    'security_question_id',
    'security_answer_hash',
    'has_pin',
    'pin_set_at',
    'pin_failed_attempts',
    'pin_locked_until',
    'two_fa_method',
    'force_tfa',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance',
        'kyc_data',
        'tsc',
        'pin',
        'security_answer_hash',
        'profile_change_code',
        'two_fa_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'           => 'datetime',
        'address'                     => 'object',
        'kyc_data'                    => 'object',
        'ver_code_send_at'            => 'datetime',
        'profile_change_code_sent_at' => 'datetime',
        'email_change_at'             => 'datetime',
        'mobile_change_at'            => 'datetime',
        'business_profile_data'       => 'object',
        'kyc_rejection_attachments'   => 'object',
        'kyc_approve_attachments' => 'object',
    ];

    public function referrer()
    {
        return $this->belongsTo(self::class, 'ref_by');
    }

    public function backupCodes()
    {
        return $this->hasMany(BackupCode::class);
    }

    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn () => $this->dial_code . $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function scopePersonalAccount($query)
    {
        $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED)->where('type', 0);
    }

    public function scopeBusinessAccount($query)
    {
        $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED)->where('type', 1);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->type == Status::PERSONAL_USER) {
                $html = '<span class="badge badge--primary">' . trans('Personal') . '</span>';
            } elseif ($this->type == Status::BUSINESS_USER) {
                $html = '<span><span class="badge badge--success">' . trans('Business') . '</span>';
            }
            return $html;
        });
    }

    public function isAuthorized($user)
    {
        return ($user->ev && $user->sv && $user->tv) ? true : false;
    }

    public function getOnboardingProgress(): array
    {
        $steps = [];

        $steps[] = [
            'key'       => 'profile',
            'label'     => 'Profile Completion',
            'completed' => $this->profile_complete == Status::YES,
        ];

        if (gs('ev')) {
            $steps[] = [
                'key'       => 'email_verification',
                'label'     => 'Email Verification',
                'completed' => $this->ev == Status::VERIFIED,
            ];
        }

        if (gs('sv')) {
            $steps[] = [
                'key'       => 'mobile_verification',
                'label'     => 'Mobile Verification',
                'completed' => $this->sv == Status::VERIFIED,
            ];
        }

        $steps[] = [
            'key'       => 'two_factor',
            'label'     => 'Two-Factor Authentication',
            'completed' => $this->tv == Status::ENABLE,
        ];

        if (gs('kv')) {
            $steps[] = [
                'key'       => 'kyc',
                'label'     => 'Identity Verification (KYC)',
                'completed' => $this->kv == Status::KYC_VERIFIED,
                'status'    => $this->getKycStatus()['state'],
            ];
        }

        $total      = count($steps);
        $completed  = count(array_filter($steps, fn ($s) => $s['completed']));
        $remaining  = $total - $completed;
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            'steps'       => $steps,
            'total'       => $total,
            'completed'   => $completed,
            'remaining'   => $remaining,
            'percentage'  => $percentage,
            'is_complete' => $remaining === 0,
        ];
    }

    public function getKycStatus(): array
    {
        if ($this->kv == Status::KYC_VERIFIED) {
            return [
                'state'   => 'verified',
                'label'   => 'Verified',
                'message' => 'Your identity has been verified. You have full access to all platform features.',
                'rejection_reason'      => null,
                'rejection_attachments' => null,
            ];
        }

        if ($this->kv == Status::KYC_PENDING) {
            return [
                'state'   => 'pending',
                'label'   => 'Under Review',
                'message' => 'Your KYC documents are under review. We\'ll notify you once a decision is made.',
                'rejection_reason'      => null,
                'rejection_attachments' => null,
            ];
        }

        if ($this->kv == Status::KYC_REJECTED) {
            return [
                'state'   => 'rejected',
                'label'   => 'Rejected',
                'message' => 'Your KYC submission was rejected. Please review the reason and re-submit your documents.',
                'rejection_reason'      => $this->kyc_rejection_reason,
                'rejection_attachments' => $this->kyc_rejection_attachments,
            ];
        }

        return [
            'state'   => 'unverified',
            'label'   => 'Not Submitted',
            'message' => 'Identity verification is required to access all platform features.',
            'rejection_reason'      => null,
            'rejection_attachments' => null,
        ];
    }
}
