<?php

namespace App\Ninja\Datatables;

use App\Models\Expense;
use Auth;
use URL;
use Utils;

class ExpenseDatatable extends EntityDatatable
{
    public $entityType = ENTITY_EXPENSE;
    public $sortCol = 3;

    public function getEntityTitle($model)
    {
        return 'ID ' . $model->public_id;
    }

    public function filters()
    {
        return [
            [
                'type' => 'checkbox',
                'value' => 'active',
                'label' => trans('texts.active'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'inactive',
                'label' => trans('texts.inactive'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'deleted',
                'label' => trans('texts.deleted'),
            ],
            [
                'type' => 'separator'
            ],
            [
                'type' => 'dropdown',
                'label' => trans('texts.status'),
                'options' => [
                    [
                        'type' => 'checkbox',
                        'value' => 'invoiced',
                        'label' => trans('texts.invoiced'),
                    ],
                    [
                        'type' => 'checkbox',
                        'value' => 'paid',
                        'label' => trans('texts.paid'),
                    ],
                    [
                        'type' => 'checkbox',
                        'value' => 'pending',
                        'label' => trans('texts.pending'),
                    ],
                    [
                        'type' => 'checkbox',
                        'value' => 'logged',
                        'label' => trans('texts.logged'),
                    ],
                ],
            ],
        ];
    }

    public function searchBy()
    {
        return [
            [
                'type' => 'text',
                'name' => 'invoice_number',
                'label' => trans('texts.invoice_number'),
            ],
            [
                'type' => 'date',
                'name' => 'expense_date',
                'label' => trans('texts.expense_date'),
            ],
            [
                'type' => 'text',
                'name' => 'expense_amount',
                'label' => trans('texts.expense_amount'),
            ],
            [
                'type' => 'separator',
            ],
            [
                'type' => 'text',
                'name' => 'client_name',
                'label' => trans('texts.client_name'),
            ],
        ];
    }

    public function columns()
    {
        return [
            [
                'vendor_name',
                function ($model) {
                    if ($model->vendor) {
                        if (! Auth::user()->can('viewByOwner', [ENTITY_VENDOR, $model->vendor->user_id])) {
                            return $model->vendor->name;
                        }

                        return link_to("vendors/{$model->vendor->public_id}", $model->vendor->name)->toHtml();
                    } else {
                        return '';
                    }
                },
                ! $this->hideClient,
            ],
            [
                'client_name',
                function ($model) {
                    if ($model->client) {
                        if (! Auth::user()->can('viewByOwner', [ENTITY_CLIENT, $model->client->user_id])) {
                            return Utils::getClientDisplayName($model);
                        }
                        return link_to("clients/{$model->client->public_id}", Utils::getClientDisplayName($model->client))->toHtml();
                    } else {
                        return '';
                    }
                },
                ! $this->hideClient,
            ],
            [
                'category',
                function ($model) {
                    $category = $model->expense_category ? substr($model->expense_category->name, 0, 100) : '';

                    if (! Auth::user()->can('editByOwner', [ENTITY_EXPENSE_CATEGORY, $model->expense_category ? $model->expense_category->user_id : null])) {
                        return $category;
                    }

                    return $model->expense_category ? link_to("expense_categories/{$model->expense_category->public_id}/edit", $category)->toHtml() : '';
                },
            ],
            [
                'public_notes',
                function ($model) {
                    return $model->public_notes != null ? substr($model->public_notes, 0, 100) : '';
                },
            ],
            [
                'expense_date',
                function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_EXPENSE, $model->user_id])) {
                        return Utils::fromSqlDate($model->expense_date);
                    }

                    return link_to("expenses/{$model->public_id}/edit", Utils::fromSqlDate($model->expense_date))->toHtml();
                },
            ],
            [
                'amount',
                function ($model) {
                    $amount = Utils::calculateTaxes($model->amount, $model->tax_rate1, $model->tax_rate2);
                    $str = Utils::formatMoney($amount, $model->expense_currency_id);

                    // show both the amount and the converted amount
                    if ($model->exchange_rate != 1) {
                        $converted = round($amount * $model->exchange_rate, 2);
                        $str .= ' | ' . Utils::formatMoney($converted, $model->invoice_currency_id);
                    }

                    return $str;
                },
            ],
            [
                'status',
                function ($model) {
                    return self::getStatusLabel($model->invoice_id, $model->should_be_invoiced, $model->invoice ? $model->invoice->balance : null);
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_expense'),
                function ($model) {
                    return URL::to("expenses/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_EXPENSE, $model->user_id]);
                },
            ],
            [
                trans('texts.view_invoice'),
                function ($model) {
                    return URL::to("/invoices/{$model->invoice->public_id}/edit");
                },
                function ($model) {
                    return $model->invoice && Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->invoice->user_id]);
                },
            ],
            [
                trans('texts.invoice_expense'),
                function ($model) {
                    return "javascript:submitForm_expense('invoice', {$model->public_id})";
                },
                function ($model) {
                    return ! $model->invoice_id && (! $model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
        ];
    }

    private function getStatusLabel($invoiceId, $shouldBeInvoiced, $balance)
    {
        $label = Expense::calcStatusLabel($shouldBeInvoiced, $invoiceId, $balance);
        $class = Expense::calcStatusClass($shouldBeInvoiced, $invoiceId, $balance);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }
}
