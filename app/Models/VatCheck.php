<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatCheck extends Model
{
    protected $fillable = ['client_id', 'address', 'country_code', 'name', 'vat_number', 'is_valid'];

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
}
