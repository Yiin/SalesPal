<?php

namespace App\Models;

use Eloquent;

/**
 * Class Currency.
 */
class Currency extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $casts = [
        'swap_currency_symbol' => 'boolean',
    ];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'invoice_currency_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_currency_id');
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}
