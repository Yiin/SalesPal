<?php

namespace App\Ninja\Datatables;

class EntityDatatable
{
    public $entityType;
    public $isBulkEdit;
    public $hideClient;
    public $sortCol = 1;

    public function __construct($isBulkEdit = true, $hideClient = false, $entityType = false)
    {
        $this->isBulkEdit = $isBulkEdit;
        $this->hideClient = $hideClient;

        if ($entityType) {
            $this->entityType = $entityType;
        }
    }

    public function calculator()
    {
        return null;
    }

    public function filters()
    {
        return [];
    }

    public function searchBy()
    {
        return [];
    }

    public function columns()
    {
        return [];
    }

    public function actions()
    {
        return [];
    }

    public function bulkActions()
    {
        return [
            [
                'label' => mtrans($this->entityType, 'archive_'.$this->entityType),
                'url' => 'javascript:submitForm_'.$this->entityType.'("archive")',
            ],
            [
                'label' => mtrans($this->entityType, 'delete_'.$this->entityType),
                'url' => 'javascript:submitForm_'.$this->entityType.'("delete")',
            ],
        ];
    }

    public function columnFields()
    {
        $data = [];
        $columns = $this->columns();

        if ($this->isBulkEdit) {
            $data[] = 'checkbox';
        }

        foreach ($columns as $column) {
            if (isset($column['visible'])) {
                if (! $column['visible']) {
                    continue;
                }
            }
            $data[] = $column['field'];
        }

        //--
        $data[] = '';
        //--

        return $data;
    }

    public function clientsDropdown($relation = null, $queryCallback = null)
    {
        if (! $relation) return null;

        if (! $queryCallback) {
            $queryCallback = function () {};
        }

        $clientsDropdown = [
            'type' => 'dropdown',
            'label' => trans('texts.clients'),
            'options' => [],
        ];

        $clients = \App\Models\Client::withTrashed()->whereHas($relation, $queryCallback)->get();

        foreach ($clients as $client) {
            $clientsDropdown['options'][] = [
                'type' => 'checkbox',
                'value' => 'client_id:' . $client->id,
                'label' => $client->name,
            ];
        }

        return $clientsDropdown;
    }

    public function productsDropdown($relation = null, $queryCallback = null)
    {
        if (! $relation) return null;

        if (! $queryCallback) {
            $queryCallback = function () {};
        }

        $productsDropdown = [
            'type' => 'dropdown',
            'label' => trans('texts.products'),
            'options' => [],
        ];

        $products = \App\Models\Product::withTrashed()->whereHas($relation, $queryCallback)->get();

        foreach ($products as $product) {
            $productsDropdown['options'][] = [
                'type' => 'checkbox',
                'value' => 'product_id:' . $product->id,
                'label' => $product->product_key,
            ];
        }

        return $productsDropdown;
    }

    public function currenciesDropdown($relation = null, $queryCallback = null)
    {
        if (! $relation) return null;

        if (! $queryCallback) {
            $queryCallback = function () {};
        }

        $currenciesDropdown = [
            'type' => 'dropdown',
            'label' => trans('texts.currency_id'),
            'options' => [],
        ];

        $currencies = \App\Models\Currency::whereHas($relation, $queryCallback)->get();

        foreach ($currencies as $currency) {
            $currenciesDropdown['options'][] = [
                'type' => 'checkbox',
                'value' => 'currency_id:' . $currency->id,
                'label' => $currency->name,
            ];
        }

        return $currenciesDropdown;
    }

    public function rightAlignIndices()
    {
        return $this->alignIndices([/*'amount', 'balance', 'cost'*/]);
    }

    public function centerAlignIndices()
    {
        return $this->alignIndices(['status']);
    }

    public function alignIndices($fields)
    {
        $columns = $this->columnFields();
        $indices = [];

        foreach ($columns as $index => $column) {
            if (in_array($column, $fields)) {
                $indices[] = $index + 1;
            }
        }

        return $indices;
    }
}
