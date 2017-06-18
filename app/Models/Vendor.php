<?php

namespace App\Models;

use App\Events\VendorWasCreated;
use App\Events\VendorWasDeleted;
use App\Events\VendorWasUpdated;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Vendor.
 */
class Vendor extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\VendorPresenter';
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'id_number',
        'vat_number',
        'work_phone',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'private_notes',
        'currency_id',
        'website',
        'transaction_name',
    ];

    /**
     * @var string
     */
    public static $fieldName = 'name';
    /**
     * @var string
     */
    public static $fieldPhone = 'work_phone';
    /**
     * @var string
     */
    public static $fieldAddress1 = 'address1';
    /**
     * @var string
     */
    public static $fieldAddress2 = 'address2';
    /**
     * @var string
     */
    public static $fieldCity = 'city';
    /**
     * @var string
     */
    public static $fieldState = 'state';
    /**
     * @var string
     */
    public static $fieldPostalCode = 'postal_code';
    /**
     * @var string
     */
    public static $fieldNotes = 'notes';
    /**
     * @var string
     */
    public static $fieldCountry = 'country';

    /**
     * @return array
     */
    public static function getImportColumns()
    {
        return [
            self::$fieldName,
            self::$fieldPhone,
            self::$fieldAddress1,
            self::$fieldAddress2,
            self::$fieldCity,
            self::$fieldState,
            self::$fieldPostalCode,
            self::$fieldCountry,
            self::$fieldNotes,
            VendorContact::$fieldFirstName,
            VendorContact::$fieldLastName,
            VendorContact::$fieldPhone,
            VendorContact::$fieldEmail,
        ];
    }

    /**
     * @return array
     */
    public static function getImportMap()
    {
        return [
            'first' => 'first_name',
            'last' => 'last_name',
            'email' => 'email',
            'mobile|phone' => 'phone',
            'name|organization' => 'name',
            'street2|address2' => 'address2',
            'street|address|address1' => 'address1',
            'city' => 'city',
            'state|province' => 'state',
            'zip|postal|code' => 'postal_code',
            'country' => 'country',
            'note' => 'notes',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendor_contacts()
    {
        return $this->hasMany('App\Models\VendorContact');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function size()
    {
        return $this->belongsTo('App\Models\Size');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo('App\Models\Industry');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany('App\Models\Expense', 'vendor_id', 'id');
    }

    /**
     * @param $data
     * @param bool $isPrimary
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addVendorContact($data, $isPrimary = false)
    {
        //$publicId = isset($data['public_id']) ? $data['public_id'] : false;
        $publicId = isset($data['public_id']) ? $data['public_id'] : (isset($data['id']) ? $data['id'] : false);

        if ($publicId && $publicId != '-1') {
            $contact = VendorContact::scope($publicId)->firstOrFail();
        } else {
            $contact = VendorContact::createNew();
        }

        $contact->fill($data);
        $contact->is_primary = $isPrimary;

        return $this->vendor_contacts()->save($contact);
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/vendors/{$this->public_id}";
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getCityState()
    {
        $swap = $this->country && $this->country->swap_postal_code;

        return Utils::cityStateZip($this->city, $this->state, $this->postal_code, $swap);
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return 'vendor';
    }

    /**
     * @return bool
     */
    public function showMap()
    {
        return $this->hasAddress() && env('GOOGLE_MAPS_ENABLED') !== false;
    }

    /**
     * @return bool
     */
    public function hasAddress()
    {
        $fields = [
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'country_id',
        ];

        foreach ($fields as $field) {
            if ($this->$field) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getDateCreated()
    {
        if ($this->created_at == '0000-00-00 00:00:00') {
            return '---';
        } else {
            return $this->created_at->format('m/d/y h:i a');
        }
    }

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        if ($this->currency_id) {
            return $this->currency_id;
        }

        if (! $this->account) {
            $this->load('account');
        }

        return $this->account->currency_id ?: DEFAULT_CURRENCY;
    }

    /**
     * @return float|int
     */
    public function getTotalExpenses()
    {
        return DB::table('expenses')
                ->select('expense_currency_id', DB::raw('SUM(amount) as amount'))
                ->whereVendorId($this->id)
                ->whereIsDeleted(false)
                ->groupBy('expense_currency_id')
                ->get();
    }

    public function scopeFilter($query, $filter)
    {
        if ($filter) foreach ($filter as $f) {
            switch ($f) {
                case 'active':
                    $query = $query->orWhere(function($query) {
                        $query->where('is_deleted', false)->whereNull('deleted_at');
                    });
                    break;
                case 'archived':
                    $query = $query->orWhere(function($query) {
                        $query->where('is_deleted', false)->whereNotNull('deleted_at');
                    });
                    break;
                case 'deleted':
                    $query = $query->orWhere(function($query) {
                        $query->where('is_deleted', true)->whereNotNull('deleted_at');
                    });
                    break;

                case 'buying':
                    $query = $query->orWhere(function($query) {
                        $query->whereHas('status', function ($query) {
                            $query->where('name', 'buying');
                        });
                    });
                    break;
                case 'reselling':
                    $query = $query->orWhere(function($query) {
                        $query->whereHas('status', function ($query) {
                            $query->where('name', 'reselling');
                        });
                    });
                    break;
                default:
                    $query = $query->whereHas('country', function ($query) use ($f) {
                        $query->where('id', $f);
                    });
            }
        }
        return $query;
    }

    public function scopeSearchBy($query, $searchBy)
    {
        if ($searchBy) foreach ($searchBy as $search => $value) {
            switch ($search) {
                case 'vendor_name':
                    Utils::querySearchText($query, 'name', $value);
                    break;
                case 'contact_number':
                    Utils::querySearchText($query, 'work_phone', $value);
                    
                    $query->orWhereHas('vendor_contacts', function ($query) use ($value) {
                        Utils::querySearchText($query, 'phone', $value);
                    });
                    break;
                case 'email':
                    Utils::querySearchText($query, 'email', $value);

                    $query->orWhereHas('vendor_contacts', function ($query) use ($value) {
                        Utils::querySearchText($query, 'email', $value);
                    });
                    break;
                case 'date_created':
                    Utils::querySearchDate($query, 'created_at', $value);
                    break;
                case 'expenses_amount':
                    $query->whereHas('expenses', function ($query) use ($value) {
                        $query->where('expenses.is_deleted', false);
                        $query->where('expenses.expense_currency_id', '=', DB::raw("`vendors`.`currency_id`"));

                        Utils::queryHavingValue($query, DB::raw("SUM(`expenses`.`amount`)"), $value);

                        $query->groupBy('expenses.expense_currency_id');
                    });
                    break;
            }
        }
    }
}

Vendor::creating(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::created(function ($vendor) {
    event(new VendorWasCreated($vendor));
});

Vendor::updating(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::updated(function ($vendor) {
    event(new VendorWasUpdated($vendor));
});

Vendor::deleting(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::deleted(function ($vendor) {
    event(new VendorWasDeleted($vendor));
});
