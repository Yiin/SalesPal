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

    public function columns()
    {
        return [
            [
                'name',
                function ($model) {
                    return link_to("vendors/{$model->public_id}", $model->name ?: '')->toHtml();
                },
            ],
            [
                'city',
                function ($model) {
                    return $model->city;
                },
            ],
            [
                'work_phone',
                function ($model) {
                    $contact = $model->vendor_contacts()->first();
                    $phone = $model->work_phone ? $model->work_phone : $contact ? $contact->phone : '';
                    
                    return $phone;
                },
            ],
            [
                'email',
                function ($model) {
                    $contact = $model->vendor_contacts()->first();
                    $email = $model->email ? $model->email : $contact ? $contact->email : '';

                    return link_to("vendors/{$model->public_id}", $email ?: '')->toHtml();
                },
            ],
            [
                'expenses',
                function ($model) {
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
