<?php

namespace Larapps\GiftCertificateManager\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Session;
use Illuminate\Http\Request;
use Larapps\GiftCertificateManager\Jobs\ExportJob;
use Larapps\GiftCertificateManager\Models\FileStatus;
use Larapps\GiftCertificateManager\Models\BCStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class ExportController {

    protected $fileStatus;

    private $processingFile;

    public function __construct(FileStatus $fileStatus){
        $this->fileStatus = $fileStatus;
    }
    
    public function index(Request $request): Response
    {
        $bcStore = BCStore::retrieveStore( ["store_hash" => Session::get("store_hash")] )->get();
        if($bcStore->count() === 0){
            return Inertia::render('Error',["status"=> 403]);
        }

        $bcStore = $bcStore[0];
        $this->storeId = $bcStore->id;

        if($this->checkIfProcessing() === false){
            return Inertia::render('GiftCertificates/Export');
        }else{
            if($this->processingFile['status'] === 'completed'){

                return Inertia::render('Export/Report', [
                    'file' => $this->processingFile,
                ]);
            }else{
                return Inertia::render('Export/Progress', [
                    'file' => $this->processingFile
                ]);
            }
        }
    }

    public function start(Request $request): RedirectResponse
    {

        $bcStore = BCStore::retrieveStore( ["store_hash" => Session::get("store_hash")] )->get();
        if($bcStore->count() === 0){
            return Inertia::render('Error',["status"=> 403]);
        }else{
            $bcStore = $bcStore[0];
        }

        $filters = $request->has('filters') ? $request->get('filters') : "";
        $fields = $request->has('fields') ? $request->get('fields') : "";

        $this->fileStatus->store_id = $bcStore->id;
        $this->fileStatus->file_name = 'exports/gift_certificates_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $this->fileStatus->status = "pending";
        $this->fileStatus->type = "export";
        $this->fileStatus->additional_info = json_encode(['filters' => $filters, "fields" => $fields]);
        $this->fileStatus->save();

        ExportJob::dispatch($bcStore->id, $this->fileStatus->id, 1);

        return Redirect::back()->with('success', 'The file has started to process.');

    }

    private function checkIfProcessing(){

        /**  RETRIEVE CURRENT PROCESSING FILES AND DETERMINE PROGRESS PERCENTAGE  */
        $processing = DB::selectResultSets(
            "CALL GetFileStatus(?,?)", [ $this->storeId, 'export' ]
        );


        if(isset($processing[0][0])){
            $processing = $processing[0][0];
            if(!empty($processing->file_name)){

                $additionalContent = json_decode($processing->additional_info);
                $this->processingFile = [
                    "file_id" => $processing->file_id,
                    "file_name" => $processing->file_name,
                    "status" => $processing->status,
                    "updated_at" => $processing->updated_at,
                    "total_count" => $processing->total_count,
                ];

                if(isset($additionalContent->file_name)){
                    $this->processingFile["download_file"] = $additionalContent->file_name;
                }
                return true;
            }
        }
        return false;
    }

    public function acknowledge(int $fileId): RedirectResponse
    {
        DB::table('file_status')->where('id', $fileId)->update(array('status' => 'acknowledged'));  
        return Redirect::back()->with('success', 'The file has started to process.');
    }

    public function download($exportFile)
    {
        $headers = array('Content-Type' => 'text/csv');
        $filePath = storage_path("app/exports/".$exportFile);
        return response()->download($filePath, 'gift-certificates.csv', $headers);
    }
}