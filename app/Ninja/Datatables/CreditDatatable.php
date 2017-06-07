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

    public function columns()
    {
        return [
            [
                'client_name',
                function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_CLIENT, $model->client->user_id])) {
                        return Utils::getClientDisplayName($model);
                    }

                    return $model->client ? link_to("clients/{$model->client->public_id}", Utils::getClientDisplayName($model->client))->toHtml() : '';
                },
                ! $this->hideClient,
            ],
            [
                'public_notes',
                function ($model) {
                    return $model->public_notes;
                },
            ],
            [
                'private_notes',
                function ($model) {
                    return $model->private_notes;
                },
            ],
            [
                'credit_date',
                function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_CREDIT, $model->user_id])) {
                        return Utils::fromSqlDate($model->credit_date);
                    }

                    return link_to("credits/{$model->public_id}/edit", Utils::fromSqlDate($model->credit_date))->toHtml();
                },
            ],
            [
                'amount',
                function ($model) {
                    return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id) . '<span '.Utils::getEntityRowClass($model).'/>';
                },
            ],
            [
                'balance',
                function ($model) {
                    return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_credit'),
                function ($model) {
                    return URL::to("credits/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_CREDIT, $model->user_id]);
                },
            ],
            [
                trans('texts.apply_credit'),
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
