<?php

namespace App\Ninja\Datatables;

use Auth;
use URL;
use Utils;

class ClientDatatable extends EntityDatatable
{
    public $entityType = ENTITY_CLIENT;
    public $sortCol = 4;

    public function getEntityTitle($model)
    {
        return $model->name;
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
                'type' => 'checkbox',
                'value' => 'vat_verified',
                'label' => trans('texts.vat_verified'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'vat_pending',
                'label' => trans('texts.vat_pending'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'vat_invalid',
                'label' => trans('texts.vat_invalid'),
            ],
            [
                'type' => 'separator'
            ],
        ];

        $countriesDropdown = [
            'type' => 'dropdown',
            'label' => trans('texts.country'),
            'options' => [],
        ];

        foreach (\App\Models\Country::all() as $country) {
            $countriesDropdown['options'][] = [
                'type' => 'checkbox',
                'value' => $country->id,
                'label' => $country->name,
            ];
        }

        $filters [] = $countriesDropdown;

        return $filters;
    }

    public function searchBy()
    {
        return [
            [
                'type' => 'text',
                'label' => trans('texts.client_name'),
                'value' => 'client_name',
            ],
            [
                'type' => 'text',
                'label' => trans('texts.client_name'),
                'value' => 'client_name',
            ],
            [
                'type' => 'text',
                'label' => trans('texts.client_name'),
                'value' => 'client_name',
            ],
            [
                'type' => 'separator',
            ],
            [
                'type' => 'date',
                'label' => trans('texts.date_created'),
                'value' => 'date_created',
            ],
            [
                'type' => 'number',
                'label' => trans('texts.balance'),
                'value' => 'balance',
            ],
        ];
    }

    public function columns()
    {
        return [
            [
                'name',
                function ($model) {
                    return [
                        'data' => $model->name,
                        'display' => link_to("clients/{$model->public_id}", $model->name ?: '')->toHtml()
                    ];
                },
            ],
            [
                'vat_number',
                function ($model) {
                    return [
                        'data' => $model->vat_number,
                        'display' => link_to("clients/{$model->public_id}", $model->vat_number)->toHtml()
                    ];
                },
            ],
            [
                'work_phone',
                function ($model) {
                    return [
                        'data' => $model->work_phone,
                        'display' => $model->work_phone
                    ];
                },
            ],
            [
                'email',
                function ($model) {
                    $contact = $model->contacts()->first();
                    $email = $contact ? $contact->email : '';

                    return [
                        'data' => $email,
                        'display' => link_to("clients/{$model->public_id}", $email ?: '')->toHtml()
                    ];
                },
            ],
            [
                'created_at',
                function ($model) {
                    return [
                        'data' => $model->created_at,
                        'display' => Utils::timestampToDateString(strtotime($model->created_at))
                    ];
                },
            ],
            [
                'balance',
                function ($model) {
                    $currency_id = $model->currency_id ?: Auth::user()->account->currency_id;
                    $currency = Utils::formatMoney($model->balance, $currency_id);
                    $parts = explode(' ', $currency);

                    return [
                        'data' => $model->parts,
                        'display' => "<span class='currency_symbol'>{$parts[0]}</span> <span class='currency_value'>{$parts[1]}</span>"
                    ];
                },
            ]
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_client'),
                function ($model) {
                    return URL::to("clients/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_CLIENT, $model->user_id]);
                },
            ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    $user = Auth::user();

                    return $user->can('editByOwner', [ENTITY_CLIENT, $model->user_id]) && ($user->can('create', ENTITY_TASK) || $user->can('create', ENTITY_INVOICE));
                },
            ],
            [
                trans('texts.new_task'),
                function ($model) {
                    return URL::to("tasks/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_TASK);
                },
            ],
            [
                trans('texts.new_invoice'),
                function ($model) {
                    return URL::to("invoices/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
            [
                trans('texts.new_quote'),
                function ($model) {
                    return URL::to("quotes/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->hasFeature(FEATURE_QUOTES) && Auth::user()->can('create', ENTITY_QUOTE);
                },
            ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    $user = Auth::user();

                    return ($user->can('create', ENTITY_TASK) || $user->can('create', ENTITY_INVOICE)) && ($user->can('create', ENTITY_PAYMENT) || $user->can('create', ENTITY_CREDIT) || $user->can('create', ENTITY_EXPENSE));
                },
            ],
            [
                trans('texts.enter_payment'),
                function ($model) {
                    return URL::to("payments/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
            [
                trans('texts.enter_credit'),
                function ($model) {
                    return URL::to("credits/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_CREDIT);
                },
            ],
            [
                trans('texts.enter_expense'),
                function ($model) {
                    return URL::to("expenses/create/0/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_EXPENSE);
                },
            ],
        ];
    }
}
