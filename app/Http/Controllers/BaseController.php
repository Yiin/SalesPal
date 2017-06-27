<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Utils;
use Auth;

class BaseController extends Controller
{
    use DispatchesJobs, AuthorizesRequests;

    protected $entityType;

    public function getFilters()
    {
        return $this->datatable->filters();
    }

    public function getSearchBy()
    {
        return $this->datatable->searchBy();
    }

    public function getDatatableColumns()
    {
        return [
            'columns' => array_map(function ($column) {
                    return [
                        'field' => $column['field'],
                        'label' => trans('texts.' . $column['field']),
                        'width' => $column['width'] ?? 'auto'
                    ];
                }, array_filter($this->datatable->columns(), function ($column) {
                    if (isset($column['visible'])) {
                        return $column['visible'];
                    }
                    return true;
                })),
            'calculator' => $this->datatable->calculator()
        ];
    }

    public function getDatatable(Request $request, $entityPublicId = null)
    {
        // get table state and filters
        $table_state = $request->get('state');
        $filter = $request->get('filter');
        $searchBy = $request->get('searchBy');
        list($orderBy, $orderDirection) = $request->get('orderBy');

        // extract data from table_state
        $page = $table_state['page'] - 1; // adjust page value, so first page is 0, not 1
        $entities_per_page = $table_state['entities_per_page'];

        $query = $this->entityQuery;

        // we should only show results belonging to logged in user
        $this->filterAccount($query);
        $this->filterEntity($query, $entityPublicId);
        $query->filter($filter);
        $query->searchBy($searchBy);

        // update table state with fresh values
        $table_state['entities_count'] = $query->count();
        $table_state['page_count'] = $entities_per_page ? max(ceil($table_state['entities_count'] / $entities_per_page), 1) : 1;
        $table_state['is_empty'] = $table_state['entities_count'] === 0;

        $rows = $this->getEntities($query, $this->datatable, $page, $entities_per_page, $orderBy, $orderDirection);

        return [
            // bulkEdit should be false only on settings tables
            'bulkEdit' => $this->bulkEdit ?? true,
            'rows' => $rows,
            'table_state' => $table_state
        ];
    }

    protected function filterAccount(&$query)
    {
        $query = $query->where('account_id', Auth::user()->account_id);
    }

    /**
     * Filter entities by {entity}_id. Override function in child class to disable filter
     *
     * @param      mixed    $query     The query
     * @param      integer  $entityId  The entity identifier
     */
    protected function filterEntity(&$query, $entityId)
    {
        // do nothing by default
    }

    protected function getEntities($query, $datatable, $page, $entities_per_page, $orderBy, $orderDirection)
    {
        return $query->orderBy($orderBy, $orderDirection)
            ->skip($entities_per_page * $page)
            ->limit($entities_per_page)
            ->get()
            ->map(function ($model) use ($datatable) {
                return $this->getRowData($model, $datatable);
            });
    }

    protected function getRowData($model, $datatable)
    {
        $row = [];

        /**
         * Row Title
         */
        $row['__title'] = $datatable->getEntityTitle($model);

        /**
         * Row Actions
         */
        $row['__actions'] = $this->getRowActions($model, $datatable);

        /**
         * Row Checkbox
         */
        $row['__checkbox'] = $this->getRowCheckboxData($model);

        /**
         * Row id
         */
        $row['__id'] = $model->public_id ?? $model->id;

        /**
         * Row Columns
         */
        $this->loadRowColumns($row, $model, $datatable);

        return (object)$row;
    }

    protected function getRowActions($model, $datatable)
    {
        $actions = [];

        foreach($datatable->actions() as $action) {
            if(count($action) < 2) {
                continue;
            }

            list($title, $getUrl) = $action;
            $visible = (count($action) > 2) ? $action[2] : true;

            if(is_callable($visible)) {
                $visible = $visible($model);
            }

            if($visible) {
                if($title === '--divider--') {
                    $actions[] = '';
                    continue;
                }

                $url = $getUrl($model);

                $urlString = is_string($url) ? $url : $url['url'];

                if(strpos($urlString, "javascript:") !== FALSE) {
                    $jsAction = $urlString;
                }
                else {
                    $jsAction = "window.location = '$urlString'";
                }

                $actions[] = [
                    'title' => $title,
                    'action' => $jsAction
                ];
            }
        }

        // if last action is divider, remove it.
        if(end($actions) === '') {
            array_pop($actions);
        }

        return $actions;
    }

    protected function getRowCheckboxData($model)
    {
        return [
            'data' => [
                'id' => $model->public_id ?? $model->id,
                'class' => Utils::getEntityRowClass($model)
            ],
            'show' => Auth::user()->hasPermission('edit_all') || (isset($model->user_id) && Auth::user()->id == $model->user_id)
        ];
    }

    protected function loadRowColumns(&$row, $model, $datatable)
    {
        foreach ($datatable->columns() as $column) {
            $field = $column['field'];
            $value = $column['value'];
            $visible = $column['visible'] ?? true;

            if(!$visible) {
                $row[$field] = '';
                continue;
            }

            $row[$field] = is_callable($value) ? $value($model) : $value;
        }
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (! is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function returnBulk($entityType, $action, $ids)
    {
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $isDatatable = filter_var(request()->datatable, FILTER_VALIDATE_BOOLEAN);
        $referer = \Request::server('HTTP_REFERER');
        $entityTypes = Utils::pluralizeEntityType($entityType);

        // when restoring redirect to entity
        if ($action == 'restore' && count($ids) == 1) {
            return redirect("{$entityTypes}/" . $ids[0] . '/edit');
        // when viewing from a datatable list
        } elseif (strpos($referer, '/clients/')) {
            return redirect($referer);
        } elseif ($isDatatable || ($action == 'archive' || $action == 'delete')) {
            return redirect("{$entityTypes}");
        // when viewing individual entity
        } elseif (count($ids)) {
            return redirect("{$entityTypes}/" . $ids[0] . '/edit');
        } else {
            return redirect("{$entityTypes}");
        }
    }
}
