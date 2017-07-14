<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Client;
use App\Ninja\Datatables\ActivityDatatable;
use App\Ninja\Repositories\ActivityRepository;

/**
 * Class ActivityService.
 */
class ActivityService extends BaseService
{
    /**
     * @var ActivityRepository
     */
    protected $activityRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * ActivityService constructor.
     *
     * @param ActivityRepository $activityRepo
     * @param DatatableService   $datatableService
     */
    public function __construct(ActivityRepository $activityRepo, DatatableService $datatableService)
    {
        $this->activityRepo = $activityRepo;
        $this->datatableService = $datatableService;
    }

    public function getItems($type, $from = 0)
    {
        $limit = 12;

        switch ($type) {
            case 1:
                // Payments
                $type = [
                    ACTIVITY_TYPE_CREATE_PAYMENT,
                    ACTIVITY_TYPE_UPDATE_PAYMENT,
                    ACTIVITY_TYPE_ARCHIVE_PAYMENT,
                    ACTIVITY_TYPE_DELETE_PAYMENT,
                    ACTIVITY_TYPE_RESTORE_PAYMENT
                ];
                break;
            case 2:
                // Expenses
                $type = [
                    ACTIVITY_TYPE_CREATE_EXPENSE,
                    ACTIVITY_TYPE_ARCHIVE_EXPENSE,
                    ACTIVITY_TYPE_DELETE_EXPENSE,
                    ACTIVITY_TYPE_RESTORE_EXPENSE,
                    ACTIVITY_TYPE_UPDATE_EXPENSE
                ];
                break;
            case 3:
                // Upcoming Invoices
                $type = [];
                break;
            case 4:
                // Invoices Past Due
                $type = [];
                break;
            case 5:
                // Tasks
                $type = [
                    ACTIVITY_TYPE_CREATE_TASK,
                    ACTIVITY_TYPE_UPDATE_TASK,
                    ACTIVITY_TYPE_ARCHIVE_TASK,
                    ACTIVITY_TYPE_DELETE_TASK,
                    ACTIVITY_TYPE_RESTORE_TASK
                ];
                break;
            case 6:
                // Projects
                $type = [];
                break;
        }

        if ($type == ACTIVITY_TYPE_ALL) {
            $query = Activity::whereNotIn('activity_type_id', [
                ACTIVITY_TYPE_CHECK_CLIENT_VAT
            ]);
        }
        else {
            $query = Activity::whereIn('activity_type_id', $type);
        }
        $count = $query->count();

        // dd([$type, $from]);

        $items = $query->skip($from)->limit($limit)->orderBy('id', 'desc')->get()->map(function ($model) {
            return [
                'created_at' => $model->created_at,
                'message' => $model->getMessage()
            ];
        });

        // dd([$count, $from + $limit]);

        return [
            'first' => $from == 0,
            'last' => $count <= $from + $limit,
            'items' => $items
        ];
    }

    /**
     * @param null $clientPublicId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable($clientPublicId = null)
    {
        $clientId = Client::getPrivateId($clientPublicId);

        $query = $this->activityRepo->findByClientId($clientId);

        return $this->datatableService->createDatatable(new ActivityDatatable(false), $query);
    }
}
