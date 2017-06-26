<?php

namespace App\Ninja\Datatables;

use App\Models\Invoice;
use Auth;
use URL;
use Utils;

class InvoiceDatatable extends EntityDatatable
{
    public $entityType = ENTITY_INVOICE;
    public $sortCol = 3;

    public function getEntityTitle($model)
    {
        return $this->entityType === ENTITY_INVOICE ? $model->invoice_number : $model->quote_number;
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
                'value' => 'draft',
                'label' => trans('texts.status_draft'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'sent',
                'label' => trans('texts.status_sent'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'viewed',
                'label' => trans('texts.status_viewed'),
            ],
            [
                'type' => 'checkbox',
                'value' => 'approved',
                'label' => trans('texts.status_approved'),
            ],
        ];

        if ($this->entityType == ENTITY_INVOICE) {
            foreach ([
                [
                    'type' => 'checkbox',
                    'value' => 'partial',
                    'label' => trans('texts.status_partial'),
                ],
                [
                    'type' => 'checkbox',
                    'value' => 'paid',
                    'label' => trans('texts.status_paid'),
                ],
                [
                    'type' => 'checkbox',
                    'value' => 'overdue',
                    'label' => trans('texts.overdue'),
                ],
            ] as $f) {
                $filters [] = $f;
            }
        }
        // $filters [] = $this->currenciesDropdown();

        return $filters;
    }

    public function currenciesDropdown()
    {
        $currenciesDropdown = [
            'type' => 'dropdown',
            'label' => trans('texts.currency'),
            'options' => [],
        ];

        $currencies = \App\Models\Currency::whereHas('invoices', function ($query) {
            if ($this->entityType === ENTITY_INVOICE) {
                $query->invoices();
            }
            else {
                $query->quotes();
            }
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
                'name' => 'invoice_number',
                'label' => $this->entityType == ENTITY_INVOICE ? trans('texts.invoice_number') : trans('texts.quote_number'),
            ],
            [
                'type' => 'date',
                'name' => 'invoice_date',
                'label' => $this->entityType == ENTITY_INVOICE ? trans('texts.invoice_date') : trans('texts.quote_date'),
            ],
            [
                'type' => 'date',
                'name' => 'invoice_due_date',
                'label' => $this->entityType == ENTITY_INVOICE ? trans('texts.invoice_due_date') : trans('texts.valid_until_date'),
            ],
            [
                'type' => 'text',
                'name' => 'invoice_amount',
                'label' => $this->entityType == ENTITY_INVOICE ? trans('texts.invoice_amount') : trans('texts.amount'),
            ],
            [
                'type' => 'separator',
            ],
            [
                'type' => 'text',
                'name' => 'client_name',
                'label' => trans('texts.client_name'),
            ],
            [
                'type' => 'text',
                'name' => 'product_name',
                'label' => trans('texts.product_name'),
            ]
        ];
    }

    public function columns()
    {
        $entityType = $this->entityType;

        return [
            [
                'field' => $entityType == ENTITY_INVOICE ? 'invoice_number' : 'quote_number',
                'width' => $entityType == ENTITY_INVOICE ? '14%' : '22%',
                'value' => function ($model) use ($entityType) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_INVOICE, $model->user_id])) {
                        return [
                            'data' => $model->invoice_number,
                            'display' => $model->invoice_number
                        ];
                    }

                    return [
                        'data' => $model->invoice_number,
                        'display' => link_to("{$entityType}s/{$model->public_id}/edit", $model->invoice_number, ['class' => Utils::getEntityRowClass($model)])->toHtml()
                    ];
                },
            ],
            [
                'field' => 'client_name',
                'width' => $entityType == ENTITY_INVOICE ? '20%' : '26%',
                'value' => function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_CLIENT, $model->client->user_id])) {
                        return [
                            'data' => null,
                            'display' => Utils::getClientDisplayName($model->client)
                        ];
                    }

                    return [
                        'data' => null,
                        'display' => link_to("clients/{$model->client->public_id}", Utils::getClientDisplayName($model->client))->toHtml()
                    ];
                },
                'visible' => !$this->hideClient,
            ],
            [
                'field' => 'date',
                'width' => '11%',
                'value' => function ($model) {
                    return [
                        'data' => $model->invoice_date,
                        'display' => Utils::fromSqlDate($model->invoice_date)
                    ];
                },
            ],
            [
                'field' => $entityType == ENTITY_INVOICE ? 'due_date' : 'valid_until',
                'width' => '11%',
                'value' => function ($model) {
                    $due_date = $model->due_date ?: '';
                    return [
                        'data' => $due_date,
                        'display' => Utils::fromSqlDate($due_date)
                    ];
                },
            ],
            [
                'field' => 'amount',
                'width' => '14%',
                'value' => function ($model) {
                    $currency = Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
                    $parts = explode(' ', $currency);

                    return [
                        'data' => [
                            'currency_id' => $model->currency_id,
                            'amount' => $model->amount
                        ],
                        'display' => "<span class='currency_symbol'>{$parts[0]}</span><span class='currency_value'>{$parts[1]}</span>"
                    ];
                },
            ],
            [
                'field' => 'paid_in',
                'width' => '14%',
                'value' => function ($model) {
                    $partial = $model->getAmountPaid(true);
                    $partial = $partial > 0 ? $partial : 0;

                    $currency = Utils::formatMoney($partial, $model->currency_id, $model->country_id);
                    $parts = explode(' ', $currency);

                    $color = $model->amount === $partial ? '' : '--color-a';

                    return [
                        'data' => [
                            'currency_id' => $model->currency_id,
                            'amount' => $partial,
                        ],
                        'display' => "<span class='currency_symbol'>{$parts[0]}</span><span class='currency_value {$color}'>{$parts[1]}</span>"
                    ];
                },
                'visible' => $entityType == ENTITY_INVOICE,
            ],
            [
                'field' => 'status',
                'width' => '12%',
                'value' => function ($model) use ($entityType) {
                    return [
                        'data' => $model->invoice_status_name,
                        'display' => $model->quote_invoice_id ? link_to("invoices/{$model->quote_invoice_id}/edit", trans('texts.converted'))->toHtml() : self::getStatusLabel($model)
                    ];
                },
            ],
        ];
    }

    public function actions()
    {
        $entityType = $this->entityType;

        return [
            [
                trans("texts.edit_{$entityType}"),
                function ($model) use ($entityType) {
                    return URL::to("{$entityType}s/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->user_id]);
                },
            ],
            [
                trans("texts.clone_{$entityType}"),
                function ($model) use ($entityType) {
                    return URL::to("{$entityType}s/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
            [
                trans('texts.view_history'),
                function ($model) use ($entityType) {
                    return URL::to("{$entityType}s/{$entityType}_history/{$model->public_id}");
                },
            ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->user_id]) || Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
            [
                trans('texts.mark_sent'),
                function ($model) use ($entityType) {
                    return "javascript:submitForm_{$entityType}('markSent', {$model->public_id})";
                },
                function ($model) {
                    return $model->status_id < INVOICE_STATUS_SENT && Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->user_id]);
                },
            ],
            [
                trans('texts.mark_paid'),
                function ($model) use ($entityType) {
                    return "javascript:submitForm_{$entityType}('markPaid', {$model->public_id})";
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_INVOICE && $model->invoice_status->name !== 'paid' && $model->balance != 0 && Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->user_id]);
                },
            ],
            [
                trans('texts.enter_payment'),
                function ($model) {
                    return URL::to("payments/create/{$model->client->public_id}/{$model->public_id}");
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_INVOICE && $model->balance > 0 && Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
            [
                trans('texts.view_quote'),
                function ($model) {
                    return URL::to("quotes/{$model->quote_id}/edit");
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_INVOICE && $model->quote_id && Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->user_id]);
                },
            ],
            [
                trans('texts.view_invoice'),
                function ($model) {
                    return URL::to("invoices/{$model->quote_invoice_id}/edit");
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_QUOTE && $model->quote_invoice_id && Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->user_id]);
                },
            ],
            [
                trans('texts.convert_to_invoice'),
                function ($model) {
                    return "javascript:submitForm_quote('convert', {$model->public_id})";
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_QUOTE && ! $model->quote_invoice_id && Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->user_id]);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = Invoice::calcStatusClass($model, $model->invoice_status->id, $model->balance, $model->due_date, $model->is_recurring);
        $label = Invoice::calcStatusLabel($model, $model->invoice_status->name, $class, $this->entityType, $model->quote_invoice_id);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }

    public function bulkActions()
    {
        $actions = parent::bulkActions();

        if ($this->entityType == ENTITY_INVOICE || $this->entityType == ENTITY_QUOTE) {
            $actions[] = \DropdownButton::DIVIDER;
            $actions[] = [
                'label' => mtrans($this->entityType, 'email_' . $this->entityType),
                'url' => 'javascript:submitForm_'.$this->entityType.'("emailInvoice")',
            ];
            $actions[] = [
                'label' => mtrans($this->entityType, 'mark_sent'),
                'url' => 'javascript:submitForm_'.$this->entityType.'("markSent")',
            ];
        }

        if ($this->entityType == ENTITY_INVOICE) {
            $actions[] = [
                'label' => mtrans($this->entityType, 'mark_paid'),
                'url' => 'javascript:submitForm_'.$this->entityType.'("markPaid")',
            ];
        }

        return $actions;
    }
}
