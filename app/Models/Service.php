<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['country_delivery_method_id', 'form_id', 'name', 'status'];
    use GlobalStatus;

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function countryDeliveryMethod()
    {
        return $this->belongsTo(CountryDeliveryMethod::class);
    }
}
