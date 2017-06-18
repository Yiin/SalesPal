<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use App\Libraries\Utils;

/**
 * Class Product.
 */
class Product extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ProductPresenter';

    /**
     * @var array
     */
    protected $fillable = [
        'product_key',
        'notes',
        'cost',
        'qty',
        'default_tax_rate_id',
        'custom_value1',
        'custom_value2',
    ];

    /**
     * @return array
     */
    public static function getImportColumns()
    {
        return [
            'product_key',
            'notes',
            'cost',
        ];
    }

    /**
     * @return array
     */
    public static function getImportMap()
    {
        return [
            'product|item' => 'product_key',
            'notes|description|details' => 'notes',
            'cost|amount|price' => 'cost',
        ];
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PRODUCT;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return self::scope()->where('product_key', '=', $key)->first();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function default_tax_rate()
    {
        return $this->belongsTo('App\Models\TaxRate');
    }

    public function scopeFilter($query, $filter)
    {
        if ($filter) foreach ($filter as $f) {
            switch ($f) {
                case 'active':
                    $query->orWhere(function($query) {
                        $query->where('is_deleted', false)->whereNull('deleted_at');
                    });
                    break;
                case 'archived':
                    $query->orWhere(function($query) {
                        $query->where('is_deleted', false)->whereNotNull('deleted_at');
                    });
                    break;
                case 'deleted':
                    $query->orWhere(function($query) {
                        $query->where('is_deleted', true)->whereNotNull('deleted_at');
                    });
                    break;

                case 'in_stock':
                    $query->orWhere(function($query) {
                        $query->where('qty', '>', 0);
                    });
                    break;
                case 'non_stock':
                    $query->orWhere(function($query) {
                        $query->whereNull('qty');
                    });
                    break;
                case 'out_of_stock':
                    $query->orWhere(function($query) {
                        $query->where('qty', '<=', 0);
                    });
                    break;
            }
        }
    }

    public function scopeSearchBy($query, $searchBy)
    {
        if ($searchBy) foreach ($searchBy as $search => $value) {
            switch ($search) {
                case 'product_name':
                    Utils::querySearchText($query, 'product_key', $value);
                    break;
                case 'product_price':
                    Utils::querySearchValue($query, 'cost', $value);
                    break;
                case 'description':
                    Utils::querySearchText($query, 'notes', $value);
                    break;
                case 'qty':
                    if (strtolower($value) === strtolower(trans('texts.service'))) {
                        $query->whereNull('qty');
                    }
                    else if (strtolower($value) === strtolower(trans('texts.out_of_stock'))) {
                        $query->where('qty', '<=', 0);
                    }
                    else {
                        Utils::querySearchValue($query, 'qty', $value, true);
                    }
                    break;
            }
        }
    }
}
