<?php

namespace App\Http\Controllers;

use App\Ninja\Datatables\RecurringInvoiceDatatable;
use App\Ninja\Repositories\InvoiceRepository;
use App\Models\Invoice;

/**
 * Class RecurringInvoiceController.
 */
class RecurringInvoiceController extends BaseController
{
    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepo;

    /**
     * RecurringInvoiceController constructor.
     *
     * @param InvoiceRepository $invoiceRepo
     */
    public function __construct(InvoiceRepository $invoiceRepo, RecurringInvoiceDatatable $datatable)
    {
        //parent::__construct();

        $this->invoiceRepo = $invoiceRepo;

        $this->entityQuery = Invoice::recurring();
        
        $this->datatable = $datatable;
    }

    public function filterEntity(&$query, $entityId = null)
    {
        // filter by client
        if ($entityId) {
            $query = $query->whereHas('client', function ($query) use ($entityId) {
                $query->where('id', $entityId);
            });
        }
        else {
            $query->whereHas('client', function ($query) {
                $query->whereNull('deleted_at');
            });
        }
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $data = [
            'title' => trans('texts.recurring_invoices'),
            'entityType' => ENTITY_RECURRING_INVOICE,
            'datatable' => $this->datatable,
        ];

        return response()->view('list_wrapper', $data);
    }
}
