<?php

namespace App\Ninja\Datatables;

use Auth;
use URL;
use Utils;

class CreditDatatable extends EntityDatatable
{
    public $entityType = ENTITY_CREDIT;
    public $sortCol = 4;

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
                ],
                [
                    'name' => 'balance',
                    'label' => trans('texts.balance')
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
                'type' => 'separator',
            ],
            $this->clientsDropdown('credits')
        ];

        return $filters;
    }

    public function searchBy()
    {
        return [
            [
                'type' => 'text',
                'name' => 'client_name',
                'label' => trans('texts.client_name'),
            ],
            [
                'type' => 'date',
                'name' => 'credit_date',
                'label' => trans('texts.credit_date'),
            ],
            [
                'type' => 'separator',
            ],
            [
                'type' => 'number',
                'name' => 'amount',
                'label' => trans('texts.amount'),
            ],
            [
                'type' => 'number',
                'name' => 'balance',
                'label' => trans('texts.balance'),
            ],
        ];
    }

    public function columns()
    {
        return [
            [
                'field' => 'client_name',
                'width' => '20%',
                'value' => function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_CLIENT, $model->client->user_id])) {
                        return ['display' => Utils::getClientDisplayName($model->client)];
                    }

                    return ['display' => $model->client ? link_to("clients/{$model->client->public_id}", Utils::getClientDisplayName($model->client))->toHtml() : ''];
                },
                'visible' => !$this->hideClient,
            ],
            [
                'field' => 'public_notes',
                'width' => '19%',
                'value' => function ($model) {
                    return ['display' => $model->public_notes];
                },
            ],
            [
                'field' => 'private_notes',
                'width' => '20%',
                'value' => function ($model) {
                    return ['display' => $model->private_notes];
                },
            ],
            [
                'field' => 'credit_date',
                'width' => '11%',
                'value' => function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_CREDIT, $model->user_id])) {
                        return Utils::fromSqlDate($model->credit_date);
                    }

                    return ['display' => link_to("credits/{$model->public_id}/edit", Utils::fromSqlDate($model->credit_date))->toHtml()];
                },
            ],
            [
                'field' => 'amount',
                'width' => '13%',
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
                'field' => 'balance',
                'width' => '13%',
                'value' => function ($model) {
                    $currency = Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                    $parts = explode(' ', $currency);

                    return [
                        'data' => [
                            'symbol' => Utils::currencySymbol($model->currency_id),
                            'value' => $model->balance
                        ],
                        'display' => "<span class='currency_symbol'>{$parts[0]}</span><span class='currency_value'>{$parts[1]}</span>"
                    ];
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_credit'),
                'icon-dropdown-edit',
                function ($model) {
                    return URL::to("credits/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_CREDIT, $model->user_id]);
                },
            ],
            [
                trans('texts.apply_credit'),
                'icon-dropdown-credit',
                function ($model) {
                    return URL::to("payments/create/{$model->client->public_id}") . '?paymentTypeId=1';
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
        ];
    }
}
