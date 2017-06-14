<?php

namespace App\Ninja\Datatables;

use Auth;
use Str;
use URL;
use Utils;

class ProductDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PRODUCT;
    public $sortCol = 4;

    public function getEntityTitle($model)
    {
        return $model->product_key;
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
                'value' => 'in_stock',
                'label' => trans('texts.in_stock'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'non_stock',
                'label' => trans('texts.non_stock'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'out_of_stock',
                'label' => trans('texts.out_of_stock'),
            ],
        ];
    }

    public function columns()
    {
        return [
            [
                'product_key',
                function ($model) {
                    return [
                        'data' => $model->product_key,
                        'display' => link_to('products/' . $model->public_id . '/edit', $model->product_key)->toHtml()
                    ];
                },
            ],
            [
                'notes',
                function ($model) {
                    return [
                        'data' => $model->notes,
                        'display' => nl2br(Str::limit($model->notes, 100))
                    ];
                },
            ],
            [
                'cost',
                function ($model) {
                    return [
                        'data' => $model->cost,
                        'display' => Utils::formatMoney($model->cost)
                    ];
                },
            ],
            [
                'tax_rate',
                function ($model) {
                    $tax_rate = $model->default_tax_rate ? $model->default_tax_rate->rate : 0;
                    $tax_name = $model->default_tax_rate ? $model->default_tax_rate->name : '';

                    return [
                        'data' => [
                            'rate' => $tax_rate,
                            'name' => $tax_name
                        ],
                        'display' => $tax_rate ? ($tax_name . ' ' . $tax_rate . '%') : ''
                    ];
                },
                Auth::user()->account->invoice_item_taxes,
            ],
            [
                'stock',
                function ($model) {
                    if($model->qty === null) {
                        $text = trans('texts.service');
                    }
                    else if($model->qty > 0) {
                        $text = number_format($model->qty);
                    }
                    else {
                        $text = trans('texts.out_of_stock');
                    }
                    return [
                        'data' => $model->qty,
                        'display' => $text
                    ];
                }
            ]
        ];
    }

    public function actions()
    {
        return [
            [
                uctrans('texts.edit_product'),
                function ($model) {
                    return URL::to("products/{$model->public_id}/edit");
                },
            ],
        ];
    }
}
