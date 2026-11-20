<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Traits\NotifiesChanges;

class SendMoney extends Model
{
    use NotifiesChanges;
    
    protected $casts = [
        'sender'            => 'object',
        'recipient'         => 'object',
        'service_form_data' => 'object',
        'received_at'       => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function sendingCountry()
    {
        return $this->belongsTo(Country::class, 'sending_country_id');
    }

    public function recipientCountry()
    {
        return $this->belongsTo(Country::class, 'recipient_country_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function countryDeliveryMethod()
    {
        return $this->belongsTo(CountryDeliveryMethod::class);
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }

    public function payoutBy()
    {
        return $this->hasOne(Agent::class, 'id', 'payout_by');
    }

    public function sourceOfFund()
    {
        return $this->belongsTo(SourceOfFund::class);
    }

    public function sendingPurpose()
    {
        return $this->belongsTo(SendingPurpose::class);
    }

    public function scopeInitiated($query)
    {
        return $query->where('status', Status::SEND_MONEY_INITIATED);
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::SEND_MONEY_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', Status::SEND_MONEY_COMPLETED);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', Status::SEND_MONEY_REFUNDED);
    }

    public function scopePaymentPending($query)
    {
        return $query->where('payment_status', Status::PAYMENT_PENDING);
    }

    public function scopePaymentRejected($query)
    {
        return $query->where('payment_status', Status::PAYMENT_REJECT);
    }

    public function scopeFilterAgent($query)
    {
        return $query->where('agent_id', authAgent()->id);
    }

    public function scopeFilterUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeFilterByDay($query, $day = 7)
    {
        return $query->whereDate('created_at', '>=', now()->subDays($day));
    }

    public function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Completed (admin confirmed)
                if ($this->status == Status::SEND_MONEY_COMPLETED) {
                    return [
                        'text'      => 'Completed',
                        'bg'        => 'bg-green-50',
                        'textColor' => 'text-green-700',
                        'dot'       => 'bg-green-600',
                    ];
                }

                // Refunded
                if ($this->status == Status::SEND_MONEY_REFUNDED) {
                    return [
                        'text'      => 'Refunded',
                        'bg'        => 'bg-purple-50',
                        'textColor' => 'text-purple-700',
                        'dot'       => 'bg-purple-600',
                    ];
                }

                // Pending (admin processing)
                if ($this->status == Status::SEND_MONEY_PENDING) {
                    return [
                        'text'      => 'Processing',
                        'bg'        => 'bg-blue-50',
                        'textColor' => 'text-blue-700',
                        'dot'       => 'bg-blue-600',
                    ];
                }

                // Status is INITIATED (0) — refine by payment_status
                if ($this->payment_status == Status::PAYMENT_SUCCESS) {
                    return [
                        'text'      => 'Paid',
                        'bg'        => 'bg-teal-50',
                        'textColor' => 'text-teal-700',
                        'dot'       => 'bg-teal-600',
                    ];
                }

                if ($this->payment_status == Status::PAYMENT_PENDING) {
                    return [
                        'text'      => 'Pending',
                        'bg'        => 'bg-yellow-50',
                        'textColor' => 'text-yellow-700',
                        'dot'       => 'bg-yellow-600',
                    ];
                }

                if ($this->payment_status == Status::PAYMENT_REJECT) {
                    return [
                        'text'      => 'Payment Failed',
                        'bg'        => 'bg-red-50',
                        'textColor' => 'text-red-700',
                        'dot'       => 'bg-red-600',
                    ];
                }

                // payment_status == INITIATE (0) — awaiting payment
                return [
                    'text'      => 'Awaiting Payment',
                    'bg'        => 'bg-amber-50',
                    'textColor' => 'text-amber-700',
                    'dot'       => 'bg-amber-600',
                ];
            }
        );
    }

    public function paymentStatusBadge(): Attribute
    {
        return new Attribute(
            function () {
                $className = '';
                if ($this->payment_status == Status::PAYMENT_INITIATE && $this->deposit) {
                    $className .= 'dark';
                    $text = 'Initiated';
                } elseif ($this->payment_status == Status::PAYMENT_PENDING) {
                    $className .= 'warning';
                    $text = 'Pending';
                } elseif ($this->payment_status == Status::PAYMENT_SUCCESS) {
                    $className .= 'success';
                    $text = 'Completed';
                } elseif ($this->payment_status == Status::PAYMENT_REJECT) {
                    $className .= 'danger';
                    $text = 'Reject';
                } else {
                    $className .= 'danger';
                    $text = 'Yet to Pay';
                }
                return "<span class='badge badge--$className'>" . trans($text) . "</span>";
            }
        );
    }

    public function getSenderInfoAttribute()
    {
        if ($this->user_id) {
            $user = $this->user;
            $address = $user->address;
            $sender['name']    = $user->fullname;
            $sender['mobile']  = $user->mobile;
            $sender['address'] = $address->zip . ', ' . $address->state  . ', ' .  $address->city  . ', ' .  $address->country;
            return (object) $sender;
        } else {
            return $this->sender;
        }
    }

    public function payoutApiMethod()
    {
        return $this->belongsTo(PayoutApiCountryMethod::class, 'payout_api_method_id');
    }

    public function paidAmount()
    {
        $totalAmount = $this->base_currency_amount + $this->base_currency_charge;

        if ($this->deposit) {
            $totalAmount += $this->deposit->charge;
        }
        return $totalAmount;
    }
}
