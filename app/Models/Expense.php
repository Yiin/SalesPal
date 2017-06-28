<?php

namespace App\Models;

use App\Events\ExpenseWasCreated;
use App\Events\ExpenseWasUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Expense.
 */
class Expense extends EntityModel
{
    // Expenses
    use SoftDeletes;
    use PresentableTrait;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ExpensePresenter';

    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'vendor_id',
        'expense_currency_id',
        'expense_date',
        'invoice_currency_id',
        'amount',
        'foreign_amount',
        'exchange_rate',
        'private_notes',
        'public_notes',
        'bank_id',
        'transaction_id',
        'expense_category_id',
        'tax_rate1',
        'tax_name1',
        'tax_rate2',
        'tax_name2',
    ];

    public static function getImportColumns()
    {
        return [
            'client',
            'vendor',
            'amount',
            'public_notes',
            'expense_category',
            'expense_date',
        ];
    }

    public static function getImportMap()
    {
        return [
            'amount|total' => 'amount',
            'category' => 'expense_category',
            'client' => 'client',
            'vendor' => 'vendor',
            'notes|details' => 'public_notes',
            'date' => 'expense_date',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expense_category()
    {
        return $this->belongsTo('App\Models\ExpenseCategory')->withTrashed();
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
     * @return mixed
     */
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
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
    public function documents()
    {
        return $this->hasMany('App\Models\Document')->orderBy('id');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->transaction_id) {
            return $this->transaction_id;
        } elseif ($this->public_notes) {
            return Utils::truncateString($this->public_notes, 16);
        } else {
            return '#' . $this->public_id;
        }
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
    public function getRoute()
    {
        return "/expenses/{$this->public_id}";
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_EXPENSE;
    }

    /**
     * @return bool
     */
    public function isExchanged()
    {
        return $this->invoice_currency_id != $this->expense_currency_id || $this->exchange_rate != 1;
    }

    /**
     * @return float
     */
    public function convertedAmount()
    {
        return round($this->amount * $this->exchange_rate, 2);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        if (empty($this->visible) || in_array('converted_amount', $this->visible)) {
            $array['converted_amount'] = $this->convertedAmount();
        }

        return $array;
    }

    /**
     * @param $query
     * @param null $bankdId
     *
     * @return mixed
     */
    public function scopeBankId($query, $bankdId = null)
    {
        if ($bankdId) {
            $query->whereBankId($bankId);
        }

        return $query;
    }

    public function amountWithTax()
    {
        return Utils::calculateTaxes($this->amount, $this->tax_rate1, $this->tax_rate2);
    }

    public static function getStatuses($entityType = false)
    {
        $statuses = [];
        $statuses[EXPENSE_STATUS_LOGGED] = trans('texts.logged');
        $statuses[EXPENSE_STATUS_INVOICED] = trans('texts.invoiced');
        $statuses[EXPENSE_STATUS_PAID] = trans('texts.paid');

        return $statuses;
    }

    public static function calcStatusLabel($model, $shouldBeInvoiced, $invoiceId, $balance)
    {
        if ($model->isArchived()) {
            return trans('texts.archived');
        }
        if ($model->isDeleted()) {
            return trans('texts.deleted');
        }
        if ($invoiceId) {
            if (floatval($balance) > 0) {
                $label = 'invoiced';
            } else {
                $label = 'paid';
            }
        } elseif ($shouldBeInvoiced) {
            $label = 'pending';
        } else {
            $label = 'logged';
        }

        return trans("texts.{$label}");
    }

    public static function calcStatusClass($model, $shouldBeInvoiced, $invoiceId, $balance)
    {
        if ($model->isArchived()) {
            return 'archived';
        }
        if ($model->isDeleted()) {
            return 'deleted';
        }
        if ($invoiceId) {
            if (floatval($balance) > 0) {
                return 'invoiced';
            } else {
                return 'paid';
            }
        } elseif ($shouldBeInvoiced) {
            return 'pending';
        } else {
            return 'logged';
        }
    }

    public function statusClass()
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusClass($this, $this->should_be_invoiced, $this->invoice_id, $balance);
    }

    public function statusLabel()
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusLabel($this, $this->should_be_invoiced, $this->invoice_id, $balance);
    }

    public function scopeFilter($query, $filter)
    {
        if ($filter) foreach ($filter as $f) {
            switch ($f) {
                case 'active':
                    $query = $query->withTrashed()->orWhere(function ($query) {
                        $query->where('is_deleted', false)->whereNull('deleted_at');
                    });
                    break;
                case 'inactive':
                    $query = $query->withTrashed()->orWhere(function ($query) {
                        $query->where('is_deleted', false)->whereNotNull('deleted_at');
                    });
                    break;
                case 'deleted':
                    $query = $query->withTrashed()->orWhere(function ($query) {
                        $query->where('is_deleted', true)->whereNotNull('deleted_at');
                    });
                    break;
                    
                case 'invoiced':
                    $query = $query->orWhere(function ($query) {
                        $query->whereNotNull('invoice_id')->whereHas('invoice', function ($query) {
                            $query->where('balance', '>', 0);
                        });
                    });
                    break;
                case 'paid':
                    $query = $query->orWhere(function ($query) {
                        $query->whereNotNull('invoice_id')->whereHas('invoice', function ($query) {
                            $query->where('balance', '<=', 0);
                        });
                    });
                    break;
                case 'pending':
                    $query = $query->orWhere(function ($query) {
                        $query->whereNull('invoice_id')->where('should_be_invoiced', true);
                    });
                    break;
                case 'logged':
                    $query = $query->orWhere(function ($query) {
                        $query->whereNull('invoice_id')->where('should_be_invoiced', false);
                    });
                    break;
            }
        }
    }

    public function scopeSearchBy($query, $searchBy)
    {
        if ($searchBy) foreach ($searchBy as $search => $value) {
            switch ($search) {
                case 'invoice_number':
                    $query->whereHas('invoice', function ($query) use ($value) {
                        Utils::querySearchText($query, 'invoice_number', $value);
                    });
                    break;
                case 'expense_date':
                    Utils::querySearchDate($query, 'expense_date', $value);
                    break;
                case 'expense_amount':
                    Utils::querySearchValue($query, 'amount', $value);
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

Expense::creating(function ($expense) {
    $expense->setNullValues();
});

Expense::created(function ($expense) {
    event(new ExpenseWasCreated($expense));
});

Expense::updating(function ($expense) {
    $expense->setNullValues();
});

Expense::updated(function ($expense) {
    event(new ExpenseWasUpdated($expense));
});

Expense::deleting(function ($expense) {
    $expense->setNullValues();
});
