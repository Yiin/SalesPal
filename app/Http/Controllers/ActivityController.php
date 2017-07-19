<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use App\Models\Activity;
use App\Ninja\Datatables\ActivityDatatable;

class ActivityController extends BaseController
{
    protected $activityService;

    public function __construct(ActivityService $activityService, ActivityDatatable $datatable)
    {
        //parent::__construct();

        $this->activityService = $activityService;

        $this->entityQuery = Activity::query();
        $this->datatable = $datatable;
    }

    public function getItems($type, $from, $client_id = null)
    {
        return $this->activityService->getItems($type, $from, $client_id);
    }

    public function filterEntity(&$query, $entityId)
    {
        // filter by client
        if ($entityId) {
            $query = $query->whereHas('client', function ($query) use ($entityId) {
                $query->where('id', $entityId);
            });
        }
    }
}
