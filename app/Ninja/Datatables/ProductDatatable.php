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

    public function currenciesDropdown()
    {
        $currenciesDropdown = [
            'type' => 'dropdown',
            'label' => trans('texts.currency'),
            'options' => [],
        ];

        $currencies = \App\Models\Currency::whereHas('products', function ($query) {
            
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
                    return [
                        'data' => [
                            'symbol' => Utils::currencySymbol(),
                            'value' => $model->cost
                        ],
                        'display' => Utils::formatMoney($model->cost)
                    ];
                },
            ],
            [
                'field' => 'stock',
                'width' => '14%',
                'value' => function ($model) {
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
