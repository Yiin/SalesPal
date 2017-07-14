<?php

namespace App\Ninja\Datatables;

use App\Models\Currency;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Auth;
use URL;
use Utils;

class PaymentDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PAYMENT;
    public $sortCol = 7;

    protected static $refundableGateways = [
        GATEWAY_STRIPE,
        GATEWAY_BRAINTREE,
        GATEWAY_WEPAY,
    ];

    public function getEntityTitle($model)
    {
        return $model->invoice->invoice_number;
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
        $filters = [
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
                'type' => 'checkbox',
                'value' => 'completed',
                'label' => trans('texts.status_completed'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'refunded',
                'label' => trans('texts.status_refunded'),
            ],
            [
                'type' => 'separator',
            ]
        ];

        $filters [] = $this->paymentTypesDropdown('payments');

        $filters [] = ['type' => 'separator'];

        $filters [] = $this->clientsDropdown('payments');
        $filters [] = $this->productsDropdown('invoice_items', function ($query) {
            $query->whereHas('invoice', function ($query) {
                $query->whereHas('payments', function (){});
            });
        });

        return $filters;
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
                'name' => 'payment_date',
                'label' => trans('texts.payment_date'),
            ],
            [
                'type' => 'number',
                'name' => 'payment_amount',
                'label' => trans('texts.payment_amount'),
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
                'field' => 'invoice_name',
                'width' => '14%',
                'value' => function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_INVOICE, $model->invoice->user_id])) {
                        return ['data' => $model->invoice->invoice_number, 'display' => $model->invoice->invoice_number];
                    }

                    return [
                        'data' => $model->invoice->invoice_number, 
                        'display' => link_to("invoices/{$model->invoice->public_id}/edit", $model->invoice->invoice_number, ['class' => Utils::getEntityRowClass($model)])->toHtml()
                    ];
                },
            ],
            [
                'field' => 'client_name',
                'width' => '20%',
                'value' => function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_CLIENT, $model->client ? $model->client->user_id : null])) {
                        return [
                            'data' => $model->client ? $model->client->public_id : null, 
                            'display' => $model->client ? Utils::getClientDisplayName($model->client) : ''
                        ];
                    }
                    if ($model->client && $model->client->vat_number) {
                        $checkVatFeature = [
                            'feature' => 'CHECK_VAT',
                            'vat' => $model->client->vat_number,
                            'state' => $model->client->getVatState(),
                            'client_id' => $model->client->public_id
                        ];
                    }
                    return [
                        'data' => $checkVatFeature ?? null,
                        'display' => $model->client ? link_to("clients/{$model->client->public_id}", Utils::getClientDisplayName($model->client))->toHtml() : ''
                    ];
                },
                'visible' => !$this->hideClient,
            ],
            [
                'field' => 'transaction_reference',
                'width' => '20%',
                'value' => function ($model) {
                    return [
                        'data' => $model->transaction_reference, 
                        'display' => $model->transaction_reference ? $model->transaction_reference : '<i>'.trans('texts.manual_entry').'</i>'
                    ];
                },
            ],
            [
                'field' => 'method',
                'width' => '9%',
                'value' => function ($model) {
                    return [
                        'data' => ($model->payment_type && ! $model->last4) ? $model->payment_type->name : ($model->account_gateway_id ? $model->account_gateway->name : ''),
                        'display' => ($model->payment_type && ! $model->last4) ? $model->payment_type->name : ($model->account_gateway_id ? $model->account_gateway->name : '')
                    ];
                },
            ],
            [
                'field' => 'amount',
                'width' => '10%',
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
                'field' => 'date_created',
                'width' => '11%',
                'value' => function ($model) {
                    if ($model->is_deleted) {
                        return [
                            'data' => $model->payment_date,
                            'display' => Utils::dateToString($model->payment_date)
                        ];
                    } else {
                        return [
                            'data' => $model->payment_date,
                            'display' => link_to("payments/{$model->public_id}/edit", Utils::dateToString($model->payment_date))->toHtml()
                        ];
                    }
                },
            ],
            [
                'field' => 'status',
                'width' => '12%',
                'value' => function ($model) {
                    return [
                        'data' => $model->payment_status_id,
                        'display' => self::getStatusLabel($model)
                    ];
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_payment'),
                'icon-dropdown-edit',
                function ($model) {
                    return URL::to("payments/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_PAYMENT, $model->user_id]);
                },
            ],
            [
                trans('texts.refund_payment'),
                'icon-dropdown-refund',
                function ($model) {
                    $max_refund = number_format($model->amount - $model->refunded, 2);
                    $formatted = Utils::formatMoney($max_refund, $model->currency_id, $model->country_id);
                    $symbol = Utils::getFromCache($model->currency_id ? $model->currency_id : 1, 'currencies')->symbol;

                    return "javascript:showRefundModal({$model->public_id}, '{$max_refund}', '{$formatted}', '{$symbol}')";
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_PAYMENT, $model->user_id])
                        && $model->payment_status_id >= PAYMENT_STATUS_COMPLETED
                        && $model->refunded < $model->amount;
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $amount = Utils::formatMoney($model->refunded, $model->currency_id, $model->country_id);
        $label = Payment::calcStatusLabel($model, $model->payment_status_id, $model->payment_status->name, $amount);
        $class = Payment::calcStatusClass($model, $model->payment_status_id);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }
}
