<?php

namespace App\Models;

use Cache;
use Eloquent;
use Str;

/**
 * Class Frequency.
 */
class Frequency extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getTranslatedName()
    {
        $name = Str::snake(str_replace(' ', '_', $this->name));
        return trans('texts.freq_' . $name);
    }

    public static function selectOptions()
    {
        $data = [];

        foreach (Cache::get('frequencies') as $frequency) {
            $data[$frequency->id] = getTranslatedName();
        }

        return $data;
    }
}
