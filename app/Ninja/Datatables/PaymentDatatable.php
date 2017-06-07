<?php

namespace App\Ninja\Datatables;

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

    public function columns()
    {
        return [
            [
                'invoice_name',
                function ($model) {
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
                'client_name',
                function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_CLIENT, $model->client ? $model->client->user_id : null])) {
                        return [
                            'data' => $model->client ? $model->client->public_id : null, 
                            'display' => $model->client ? Utils::getClientDisplayName($model->client) : ''
                        ];
                    }

                    return [
                        'data' => Utils::getClientDisplayName($model->client), 
                        'display' => $model->client ? link_to("clients/{$model->client->public_id}", Utils::getClientDisplayName($model->client))->toHtml() : ''
                    ];
                },
                ! $this->hideClient,
            ],
            [
                'transaction_reference',
                function ($model) {
                    return [
                        'data' => $model->transaction_reference, 
                        'display' => $model->transaction_reference ? $model->transaction_reference : '<i>'.trans('texts.manual_entry').'</i>'
                    ];
                },
            ],
            [
                'method',
                function ($model) {
                    return [
                        'data' => ($model->payment_type && ! $model->last4) ? $model->payment_type->name : ($model->account_gateway_id ? $model->account_gateway->name : ''),
                        'display' => ($model->payment_type && ! $model->last4) ? $model->payment_type->name : ($model->account_gateway_id ? $model->account_gateway->name : '')
                    ];
                },
            ],
            [
                'amount',
                function ($model) {
                    return [
                        'data' => [
                            'currency_id' => $model->currency_id,
                            'amount' => $model->amount,
                        ],
                        'display' => Utils::formatMoney($model->amount, $model->currency_id, $model->country_id)
                    ];
                },
            ],
            [
                'date',
                function ($model) {
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
                'status',
                function ($model) {
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
                function ($model) {
                    return URL::to("payments/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_PAYMENT, $model->user_id]);
                },
            ],
            [
                trans('texts.refund_payment'),
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
        $label = Payment::calcStatusLabel($model->payment_status_id, $model->payment_status->name, $amount);
        $class = Payment::calcStatusClass($model->payment_status_id);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }
}
