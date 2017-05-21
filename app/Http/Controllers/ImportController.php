<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImportService;
use App\Jobs\ImportData;
use Exception;
use Input;
use Redirect;
use Session;
use Utils;
use View;
use Auth;

class ImportController extends BaseController
{
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function doImport(Request $request)
    {
        if (! Auth::user()->confirmed) {
            return redirect('/settings/' . ACCOUNT_IMPORT_EXPORT)->withError(trans('texts.confirm_account_to_import'));
        }

        $source = Input::get('source');
        $files = [];
        $timestamp = time();

        foreach (ImportService::$entityTypes as $entityType) {
            $fileName = $entityType;
            if ($request->hasFile($fileName)) {
                $file = $request->file($fileName);
                $destinationPath = storage_path() . '/import';
                $extension = $file->getClientOriginalExtension();

                if (! in_array($extension, ['csv', 'xls', 'xlsx', 'json'])) {
                    continue;
                }

                $newFileName = sprintf('%s_%s_%s.%s', Auth::user()->account_id, $timestamp, $fileName, $extension);
                $file->move($destinationPath, $newFileName);
                $files[$entityType] = $newFileName;
            }
        }

        if (! count($files)) {
            Session::flash('error', trans('texts.select_file'));
            return Redirect::to('/settings/' . ACCOUNT_IMPORT_EXPORT);
        }

        try {
            if ($source === IMPORT_CSV) {
                $data = $this->importService->mapCSV($files);
                return View::make('accounts.import_map', [
                    'data' => $data,
                    'timestamp' => $timestamp,
                ]);
            } elseif ($source === IMPORT_JSON) {
                $includeData = filter_var(Input::get('data'), FILTER_VALIDATE_BOOLEAN);
                $includeSettings = filter_var(Input::get('settings'), FILTER_VALIDATE_BOOLEAN);
                if (config('queue.default') === 'sync') {
                    $results = $this->importService->importJSON($files[IMPORT_JSON], $includeData, $includeSettings);
                    $message = $this->importService->presentResults($results, $includeSettings);
                } else {
                    $settings = [
                        'files' => $files,
                        'include_data' => $includeData,
                        'include_settings' => $includeSettings,
                    ];
                    $this->dispatch(new ImportData(Auth::user(), IMPORT_JSON, $settings));
                    $message = trans('texts.import_started');
                }
            } else {
                if (config('queue.default') === 'sync') {
                    $results = $this->importService->importFiles($source, $files);
                    $message = $this->importService->presentResults($results);
                } else {
                    $settings = [
                        'files' => $files,
                        'source' => $source,
                    ];
                    $this->dispatch(new ImportData(Auth::user(), false, $settings));
                    $message = trans('texts.import_started');
                }
            }
            return redirect('/settings/' . ACCOUNT_IMPORT_EXPORT)->withWarning($message);
        } catch (Exception $exception) {
            Utils::logError($exception);
            Session::flash('error', $exception->getMessage());

            return Redirect::to('/settings/' . ACCOUNT_IMPORT_EXPORT);
        }
    }

    public function doImportCSV()
    {
        try {
            $map = Input::get('map');
            $headers = Input::get('headers');
            $timestamp = Input::get('timestamp');
            if (config('queue.default') === 'sync') {
                $results = $this->importService->importCSV($map, $headers, $timestamp);
                $message = $this->importService->presentResults($results);
            } else {
                $settings = [
                    'timestamp' => $timestamp,
                    'map' => $map,
                    'headers' => $headers,
                ];
                $this->dispatch(new ImportData(Auth::user(), IMPORT_CSV, $settings));
                $message = trans('texts.import_started');
            }

            return redirect('/settings/' . ACCOUNT_IMPORT_EXPORT)->withWarning($message);
        } catch (Exception $exception) {
            Utils::logError($exception);
            Session::flash('error', $exception->getMessage());

            return Redirect::to('/settings/' . ACCOUNT_IMPORT_EXPORT);
        }
    }
}
