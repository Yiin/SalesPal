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

    public function calculator()
    {
        return [
            'default' => 'cost',
            'options' => [
                [
                    'name' => 'cost',
                    'label' => trans('texts.cost')
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
            // $this->currenciesDropdown()
        ];
    }

    public function searchBy()
    {
        return [
            [
                'type' => 'text',
                'name' => 'product_name',
                'label' => trans('texts.product_name'),
            ],
            [
                'type' => 'text',
                'name' => 'product_price',
                'label' => trans('texts.product_price'),
            ],
            [
                'type' => 'separator'
            ],
            [
                'type' => 'text',
                'name' => 'description',
                'label' => trans('texts.words_in_description'),
            ],
            [
                'type' => 'separator'
            ],
            [
                'type' => 'text',
                'name' => 'qty',
                'label' => trans('texts.stock_amount'),
            ],
        ];
    }

    public function columns()
    {
        return [
            [
                'field' => 'product_key',
                'width' => '20%',
                'value' => function ($model) {
                    return [
                        'data' => $model->product_key,
                        'display' => link_to('products/' . $model->public_id . '/edit', $model->product_key)->toHtml()
                    ];
                },
            ],
            [
                'field' => 'notes',
                'width' => '46%',
                'value' => function ($model) {
                    return [
                        'data' => $model->notes,
                        'display' => nl2br(Str::limit($model->notes, 100))
                    ];
                },
            ],
            [
                'field' => 'cost',
                'width' => '16%',
                'value' => function ($model) {
                    $currency = Utils::formatMoney($model->cost);
                    $parts = explode(' ', $currency);

                    return [
                        'data' => [
                            'symbol' => Utils::currencySymbol(),
                            'value' => $model->cost
                        ],
                        'display' => "
                            <span class='currency_symbol'>{$parts[0]}</span>
                            <span class='currency_value'>{$parts[1]}</span>
                        "
                    ];
                },
            ],
            [
                'field' => 'stock',
                'width' => '14%',
                'value' => function ($model) {
                    if($model->qty === null) {
                        $text = '<span class="stock-label --service">' . trans('texts.service') . '</span>';
                    }
                    else if($model->qty > 0) {
                        $text = '<span class="stock-label">' . number_format($model->qty) . '</span>';
                    }
                    else {
                        $text = '<span class="stock-label --out-of-stock">' . trans('texts.out_of_stock') . '</span>';
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
