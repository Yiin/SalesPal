<?php

namespace App\Models;

use App\Events\CreditWasCreated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Credit.
 */
class Credit extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\CreditPresenter';

    /**
     * @var array
     */
    protected $fillable = [
        'public_notes',
        'private_notes',
    ];

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
     * @return mixed
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/credits/{$this->public_id}";
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_CREDIT;
    }

    /**
     * @param $amount
     *
     * @return mixed
     */
    public function apply($amount)
    {
        if ($amount > $this->balance) {
            $applied = $this->balance;
            $this->balance = 0;
        } else {
            $applied = $amount;
            $this->balance = $this->balance - $amount;
        }

        $this->save();

        return $applied;
    }

    public function scopeFilter($query, $filter)
    {
        if ($filter) foreach ($filter as $f) {
            switch ($f) {
                case 'active':
                    $query = $query->withTrashed()->orWhere(function($query) {
                        $query->where('is_deleted', false)->whereNull('deleted_at');
                    });
                    break;
                case 'archived':
                    $query = $query->withTrashed()->orWhere(function($query) {
                        $query->where('is_deleted', false)->whereNotNull('deleted_at');
                    });
                    break;
                case 'deleted':
                    $query = $query->withTrashed()->orWhere(function($query) {
                        $query->where('is_deleted', true)->whereNotNull('deleted_at');
                    });
                    break;
            }
        }
        
        $this->filterClients($query, $filter);
    }

    public function filterClients(&$query, $filter)
    {
        $ids = $this->getIdsFromFilter($filter, 'client');

        if (!empty($ids)) {
            $query->whereHas('client', function ($query) use ($ids) {
                $query->whereIn('id', $ids);
            });
        }
    }

    public function scopeSearchBy($query, $searchBy)
    {
        if ($searchBy) foreach ($searchBy as $search => $value) {
            switch ($search) {
                case 'credit_date':
                    Utils::querySearchDate($query, 'credit_date', $value);
                    break;
                case 'amount':
                    Utils::querySearchValue($query, 'amount', $value);
                    break;
                case 'balance':
                    Utils::querySearchValue($query, 'balance', $value);
                    break;
                case 'client_name':
                    $query->whereHas('client', function ($query) use ($value) {
                        Utils::querySearchText($query, 'name', $value);

                        $query->orWhereHas('contacts', function ($query) use ($value) {
                            Utils::querySearchText($query, 
                                \DB::raw("CONCAT(`contacts`.`first_name`, ' ', `contacts`.`last_name`)"), 
                                $value
                            );
                        });
                    });
                    break;
            }
        }
    }
}

Credit::creating(function ($credit) {
});

Credit::created(function ($credit) {
    event(new CreditWasCreated($credit));
});
