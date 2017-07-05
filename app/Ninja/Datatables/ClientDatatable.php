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

    public function calculator()
    {
        return [
            'default' => 'balance',
            'options' => [
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
                'value' => 'inactive',
                'label' => trans('texts.archived'),
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

        $filters [] = $this->countriesDropdown('clients');
        $filters [] = $this->currenciesDropdown('clients');

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
                'type' => 'text',
                'name' => 'contact_number',
                'label' => trans('texts.contact_number'),
            ],
            [
                'type' => 'text',
                'name' => 'email',
                'label' => trans('texts.email'),
            ],
            [
                'type' => 'separator',
            ],
            [
                'type' => 'date',
                'name' => 'date_created',
                'label' => trans('texts.date_created'),
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
                'field' => 'name',
                'width' => '18%',
                'value' => function ($model) {
                    return [
                        'data' => $model->name,
                        'display' => link_to("clients/{$model->public_id}", $model->name ?: '')->toHtml()
                    ];
                },
            ],
            [
                'field' => 'vat_number',
                'width' => '20%',
                'value' => function ($model) {
                    if ($model->vat_number) {
                        $checkVatFeature = [
                            'feature' => 'CHECK_VAT',
                            'vat' => $model->vat_number,
                            'state' => $model->getVatState()
                        ];
                    }
                    return [
                        'data' => $checkVatFeature ?? null,
                        'display' => link_to("clients/{$model->public_id}", $model->vat_number)->toHtml()
                    ];
                },
            ],
            [
                'field' => 'work_phone',
                'width' => '13%',
                'value' => function ($model) {
                    return [
                        'data' => $model->work_phone,
                        'display' => $model->work_phone
                    ];
                },
            ],
            [
                'field' => 'email',
                'width' => '20%',
                'value' => function ($model) {
                    $contact = $model->contacts()->first();
                    $email = $contact ? $contact->email : '';

                    return [
                        'data' => $email,
                        'display' => link_to("clients/{$model->public_id}", $email ?: '')->toHtml()
                    ];
                },
            ],
            [
                'field' => 'date_created',
                'width' => '11%',
                'value' => function ($model) {
                    return [
                        'data' => $model->created_at,
                        'display' => Utils::timestampToDateString(strtotime($model->created_at))
                    ];
                },
            ],
            [
                'field' => 'balance',
                'width' => '14%',
                'value' => function ($model) {
                    $currency_id = $model->currency_id ?: Auth::user()->account->currency_id;
                    $balance = Utils::formatMoney($model->balance, $currency_id);
                    $parts = explode(' ', $balance);

                    return [
                        'data' => [
                            'symbol' => Utils::currencySymbol($currency_id),
                            'value' => $model->balance
                        ],
                        'display' => "
                            <span class='currency_symbol'>{$parts[0]}</span>
                            <span class='currency_value'>{$parts[1]}</span>
                        "
                    ];
                },
            ]
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.preview'),
                'icon-dropdown-preview',
                function ($model) {
                    return '#';
                },
                function ($model) {
                    return true;
                }
            ],
            [
                trans('texts.edit_client'),
                'icon-dropdown-edit',
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
                'icon-dropdown-new_task',
                function ($model) {
                    return URL::to("tasks/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_TASK);
                },
            ],
            [
                trans('texts.new_invoice'),
                'icon-dropdown-new_invoice',
                function ($model) {
                    return URL::to("invoices/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
            [
                trans('texts.new_quote'),
                'icon-dropdown-quote',
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
                'icon-dropdown-payment',
                function ($model) {
                    return URL::to("payments/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
            [
                trans('texts.enter_credit'),
                'icon-dropdown-credit',
                function ($model) {
                    return URL::to("credits/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_CREDIT);
                },
            ],
            [
                trans('texts.enter_expense'),
                'icon-dropdown-expense',
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
