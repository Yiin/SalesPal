<?php

namespace App\Ninja\Datatables;

use Auth;
use URL;
use Utils;
use App\Models\Invoice;

class RecurringInvoiceDatatable extends EntityDatatable
{
    public $entityType = ENTITY_RECURRING_INVOICE;

    public function getEntityTitle($model)
    {
        return 'ID ' . $model->public_id;
    }

    public function calculator()
    {
        return [
            'default' => 'amount',
            'options' => [
                [
                    'name' => 'amount',
                    'label' => trans('texts.amount')
                ]
            ]
        ];
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
                'value' => 'archived',
                'label' => trans('texts.archived'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'deleted',
                'label' => trans('texts.deleted'),
            ],
            [
                'type' => 'separator',
            ],
            [
                'type' => 'checkbox',
                'value' => 'draft',
                'label' => trans('texts.status_draft'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'pending',
                'label' => trans('texts.status_pending'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'overdue',
                'label' => trans('texts.overdue'),
            ],
            [
                'type' => 'separator',
            ],
            $this->frequenciesDropdown('invoices', function ($query) {
                $query->recurring();
            }),
            [
                'type' => 'separator',
            ],
            $this->clientsDropdown('invoices', function ($query) {
                $query->recurring();
            }),
            $this->productsDropdown('invoice_items', function ($query) {
                $query->whereHas('invoice', function ($query) {
                    $query->recurring();
                });
            }),
        ];
    }

    public function searchBy()
    {
        return [
            [
                'type' => 'date',
                'name' => 'invoice_date',
                'label' => trans('texts.invoice_date'),
            ],
            [
                'type' => 'date',
                'name' => 'invoice_due_date',
                'label' => trans('texts.invoice_due_date'),
            ],
            [
                'type' => 'number',
                'name' => 'invoice_amount',
                'label' => trans('texts.invoice_amount'),
            ],
            [
                'type' => 'separator',
            ],
            [
                'type' => 'text',
                'name' => 'client_name',
                'label' => trans('texts.client_name'),
            ],
            [
                'type' => 'text',
                'name' => 'product_name',
                'label' => trans('texts.product_name'),
            ],
        ];
    }

    public function columns()
    {
        return [
            [
                'field' => 'frequency',
                'width' => '14%',
                'value' => function ($model) {
                    $frequency = strtolower($model->frequency->name);
                    $frequency = preg_replace('/\s/', '_', $frequency);

                    return [
                        'data' => $model->frequency,
                        'display' => link_to("invoices/{$model->public_id}", trans('texts.freq_'.$frequency))->toHtml()
                    ];
                },
            ],
            [
                'field' => 'client_name',
                'width' => '20%',
                'value' => function ($model) {
                    if ($model->client->vat_number) {
                        $checkVatFeature = [
                            'feature' => 'CHECK_VAT',
                            'vat' => $model->client->vat_number,
                            'state' => $model->client->getVatState(),
                            'client_id' => $model->client->public_id
                        ];
                    }
                    return [
                        'data' => $checkVatFeature ?? null,
                        'display' => link_to("clients/{$model->client->public_id}", Utils::getClientDisplayName($model->client))->toHtml()
                    ];
                },
                'visible' => !$this->hideClient,
            ],
            [
                'field' => 'start_date',
                'width' => '11%',
                'value' => function ($model) {
                    return Utils::fromSqlDate($model->start_date);
                },
            ],
            [
                'field' => 'last_sent',
                'width' => '11%',
                'value' => function ($model) {
                    return Utils::fromSqlDate($model->last_sent_date);
                },
            ],
            [
                'field' => 'end_date',
                'width' => '11%',
                'value' => function ($model) {
                    return Utils::fromSqlDate($model->end_date);
                },
            ],
            [
                'field' => 'amount',
                'width' => '16%',
                'value' => function ($model) {
                    $currency = Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
                    $parts = explode(' ', $currency);

                    return [
                        'data' => [
                            'symbol' => Utils::currencySymbol($model->currency_id),
                            'value' => $model->amount
                        ],
                        'display' => "<span class='currency_symbol'>{$parts[0]}</span><span class='currency_value'>{$parts[1]}</span>"
                    ];
                },
            ],
            [
                'field' => 'status',
                'width' => '13%',
                'value' => function ($model) {
                    return self::getStatusLabel($model);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = Invoice::calcStatusClass($model, $model->invoice_status->id, $model->balance, $model->due_date_sql, $model->is_recurring);
        $label = Invoice::calcStatusLabel($model, $model->invoice_status->name, $class, $this->entityType, $model->quote_invoice_id);

        if ($model->invoice_status->id == INVOICE_STATUS_SENT && (! $model->last_sent_date || $model->last_sent_date == '0000-00-00')) {
            $label = trans('texts.pending');
        }

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_invoice'),
                'icon-dropdown-edit',
                function ($model) {
                    return URL::to("invoices/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->user_id]);
                },
            ],
            [
                trans('texts.clone_invoice'),
                'icon-dropdown-clone',
                function ($model) {
                    return URL::to("invoices/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],

        ];
    }
}
