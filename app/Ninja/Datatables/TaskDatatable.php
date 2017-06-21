<?php

namespace App\Ninja\Datatables;

use App\Models\Task;
use Auth;
use URL;
use Utils;

class TaskDatatable extends EntityDatatable
{
    public $entityType = ENTITY_TASK;
    public $sortCol = 3;

    public function getEntityTitle($model)
    {
        return $model->project . '(' . Utils::formatTime(Task::calcDuration($model)) . ')';
    }

    public function columns()
    {
        return [
            [
                'field' => 'client_name',
                'value' => function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_CLIENT, $model->client ? $model->client->user_id : null])) {
                        return Utils::getClientDisplayName($model->client);
                    }

                    return $model->client ? link_to("clients/{$model->client->public_id}", Utils::getClientDisplayName($model->client))->toHtml() : '';
                },
                'visible' => !$this->hideClient,
            ],
            [
                'field' => 'project',
                'value' => function ($model) {
                    if (! Auth::user()->can('editByOwner', [ENTITY_PROJECT, $model->project ? $model->project->user_id : null])) {
                        return $model->project->name;
                    }

                    return $model->project ? link_to("projects/{$model->project->public_id}/edit", $model->project->name)->toHtml() : '';
                },
            ],
            [
                'field' => 'date',
                'value' => function ($model) {
                    if (! Auth::user()->can('viewByOwner', [ENTITY_EXPENSE, $model->user_id])) {
                        return Task::calcStartTime($model);
                    }

                    return link_to("tasks/{$model->public_id}/edit", Task::calcStartTime($model))->toHtml();
                },
            ],
            [
                'field' => 'duration',
                'value' => function ($model) {
                    return Utils::formatTime(Task::calcDuration($model));
                },
            ],
            [
                'field' => 'description',
                'value' => function ($model) {
                    return $model->description;
                },
            ],
            [
                'field' => 'status',
                'value' => function ($model) {
                    return self::getStatusLabel($model);
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_task'),
                function ($model) {
                    return URL::to('tasks/'.$model->public_id.'/edit');
                },
                function ($model) {
                    return (! $model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->can('editByOwner', [ENTITY_TASK, $model->user_id]);
                },
            ],
            [
                trans('texts.view_invoice'),
                function ($model) {
                    return URL::to("/invoices/{$model->invoice->public_id}/edit");
                },
                function ($model) {
                    return $model->invoice && Auth::user()->can('editByOwner', [ENTITY_INVOICE, $model->invoice->user_id]);
                },
            ],
            [
                trans('texts.stop_task'),
                function ($model) {
                    return "javascript:submitForm_task('stop', {$model->public_id})";
                },
                function ($model) {
                    return $model->is_running && Auth::user()->can('editByOwner', [ENTITY_TASK, $model->user_id]);
                },
            ],
            [
                trans('texts.invoice_task'),
                function ($model) {
                    return "javascript:submitForm_task('invoice', {$model->public_id})";
                },
                function ($model) {
                    return ! $model->invoice && (! $model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $label = Task::calcStatusLabel($model->is_running, $model->balance, $model->invoice ? $model->invoice->invoice_number : null);
        $class = Task::calcStatusClass($model->is_running, $model->balance, $model->invoice ? $model->invoice->invoice_number : null);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }
}
