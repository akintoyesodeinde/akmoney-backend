<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    protected $fillable = [
        'user_id', 'name', 'mobile', 'email', 'address',
        'country_id', 'delivery_method_id', 'service_id', 'service_form_data',
    ];

    protected $casts = [
        'service_form_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
