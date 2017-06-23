<?php

namespace App\Ninja\Datatables;

use Auth;
use URL;
use Utils;

class VendorDatatable extends EntityDatatable
{
    public $entityType = ENTITY_VENDOR;
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
                'value' => 'acrhived',
                'label' => trans('texts.archived'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'deleted',
                'label' => trans('texts.deleted'),
            ],
            // [
            //     'type' => 'separator'
            // ],
            // [
            //     'type' => 'checkbox',
            //     'value' => 'buying',
            //     'label' => trans('texts.buying'),
            // ],
            // [
            //     'type' => 'checkbox',
            //     'value' => 'reselling',
            //     'label' => trans('texts.reselling'),
            // ],
        ];

        $filters [] = $this->getCountriesDropdown();
        // $filters [] = $this->getCurrenciesDropdown();

        return $filters;
    }

    public function getCountriesDropdown()
    {
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

        return $countriesDropdown;
    }

    public function currenciesDropdown()
    {
        $currenciesDropdown = [
            'type' => 'dropdown',
            'label' => trans('texts.currency'),
            'options' => [],
        ];

        $currencies = \App\Models\Currency::whereHas('vendors', function ($query) {
            // nothing
        })->get();

        foreach ($currencies as $currency) {
            $currenciesDropdown['options'][] = [
                'type' => 'checkbox',
                'value' => 'currency_id:' . $currency->id,
                'label' => $currency->name,
            ];
        }

        return $currenciesDropdown;
    }

    public function searchBy()
    {
        return [
            [
                'type' => 'text',
                'name' => 'vendor_name',
                'label' => trans('texts.vendor_name'),
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
                'type' => 'date',
                'name' => 'date_created',
                'label' => trans('texts.date_created'),
            ],
            [
                'type' => 'text',
                'name' => 'expenses_amount',
                'label' => trans('texts.expenses_amount'),
            ],
        ];
    }

    public function columns()
    {
        return [
            [
                'field' => 'name',
                'width' => '15%',
                'value' => function ($model) {
                    return link_to("vendors/{$model->public_id}", $model->name ?: '')->toHtml();
                },
            ],
            [
                'field' => 'city',
                'width' => '15%',
                'value' => function ($model) {
                    return $model->city;
                },
            ],
            [
                'field' => 'work_phone',
                'width' => '12%',
                'value' => function ($model) {
                    $contact = $model->vendor_contacts()->first();
                    $phone = $model->work_phone ? $model->work_phone : $contact ? $contact->phone : '';
                    
                    return $phone;
                },
            ],
            [
                'field' => 'email',
                'width' => '30%',
                'value' => function ($model) {
                    $contact = $model->vendor_contacts()->first();
                    $email = $model->email ? $model->email : $contact ? $contact->email : '';

                    return link_to("vendors/{$model->public_id}", $email ?: '')->toHtml();
                },
            ],
            [
                'field' => 'date_created',
                'width' => '12%',
                'value' => function ($model) {
                    return Utils::timestampToDateString(strtotime($model->created_at));
                },
            ],
            [
                'field' => 'expenses',
                'width' => '13%',
                'value' => function ($model) {
                    $vendor_id = \App\Models\Vendor::getPrivateId($model->public_id);
                    $vendor = \App\Models\Vendor::find($vendor_id);
                    $currency_id = $vendor->currency_id ? $vendor->currency_id : 1;

                    $sum = 0;

                    foreach ($vendor->getTotalExpenses() as $expense) {
                        if($expense->expense_currency_id === $currency_id) { 
                            $sum += $expense->amount;
                        }
                    }

                    return Utils::formatMoney($sum, $currency_id, $vendor->country_id);
                }
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_vendor'),
                function ($model) {
                    return URL::to("vendors/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_VENDOR, $model->user_id]);
                },
            ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_VENDOR, $model->user_id]) && Auth::user()->can('create', ENTITY_EXPENSE);
                },

            ],
            [
                trans('texts.enter_expense'),
                function ($model) {
                    return URL::to("expenses/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_EXPENSE);
                },
            ],
        ];
    }
}
