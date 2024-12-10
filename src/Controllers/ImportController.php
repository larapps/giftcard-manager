<?php

namespace Larapps\GiftCertificateManager\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Larapps\GiftCertificateManager\Connectors\BigCommerceAgent;
use Larapps\GiftCertificateManager\Helpers\ArrayHelper;

use Larapps\GiftCertificateManager\Models\FileStatus;
use Larapps\GiftCertificateManager\Models\BCStore;
use Larapps\GiftCertificateManager\Models\GiftCertificate;
use Session;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Larapps\GiftCertificateManager\Services\ImportGiftCertificateService;

use Larapps\GiftCertificateManager\Jobs\ImportGiftCertificatesJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;


class ImportController {

    private $bcAgent;

    private $fileStatus;

    private $bcStore;

    private $storeId;

    private $processingFile;

    public function __construct(BigCommerceAgent $bcAgent, FileStatus $fileStatus, BCStore $bcStore){
        $this->bcAgent = $bcAgent;
        $this->fileStatus = $fileStatus;
        $this->bcStore = $bcStore;
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
            return Inertia::render('GiftCertificates/Import');
        }else{
            if($this->processingFile['status'] === 'completed'){

                $errorReport = [];
                if($this->processingFile['failure_count'] > 0){
                    $errorReport = $this->getErrorReport($this->storeId, $this->processingFile['file_id'], 1, 10 );
                }

                return Inertia::render('Import/FileReport', [
                    'progress' => $this->processingFile,
                    'error_log' => $errorReport
                ]);
            }else{
                return Inertia::render('Import/FileProgress', [
                    'progress' => $this->processingFile
                ]);
            }
            
        }
    }

    private function getErrorReport($storeId, $fileId, $page, $limit){
        $errorReport = [];
        $offset = ($page - 1) * $limit;

        $errorReport = DB::selectResultSets(
            "CALL GetFileErrorReport(?,?,?,?)", [ $storeId, $fileId, $limit, $offset ]
        );
        if(isset($errorReport[0][0])){
            $errorReport = $errorReport[0];
        }
        

        return $errorReport;
    }

    public function report(int $fileId, Request $request )
    {
        $bcStore = BCStore::retrieveStore( ["store_hash" => Session::get("store_hash")] )->get();
        if($bcStore->count() === 0){
            return Inertia::render('Error',["status"=> 403]);
        }

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 50;

        return  [
            'data' => $this->getErrorReport($bcStore[0]->id, $fileId, $page, $limit )
        ];

    }

    private function returnRowValue($headerRow, $columnName, $row){
        if(array_search($columnName ,$headerRow)!== false){
            return $row[array_search($columnName ,$headerRow)];
        }
        return '';
    }

    public function import(Request $request): RedirectResponse
    {
        $bcStore = BCStore::retrieveStore( ["store_hash" => Session::get("store_hash")] )->get();
        if($bcStore->count() > 0){
            $bcStore = $bcStore[0];

            $sheet = $request->file('import_file')->store('sheets');
            $this->fileStatus->store_id = $bcStore->id;
            $this->fileStatus->file_name = $request->file('import_file')->getClientOriginalName();
            $this->fileStatus->status = "pending";
            $this->fileStatus->type = "import";
            $this->fileStatus->additional_info = null;
            $this->fileStatus->save();

            $fileId = $this->fileStatus->id;
            $storeId = $bcStore->id;

            if(isset($this->fileStatus->id)){
                
                $filePath = $request->file('import_file')->store('sheets');
                
                $generateRow = function($row, $headerRow) use ($storeId, $fileId){

                    if( $this->returnRowValue($headerRow, 'to_name', $row) !== ""){
                        return [
                            'file_id' => $fileId,
                            'store_id' => $storeId,
                            'to_name' => $this->returnRowValue($headerRow, 'to_name', $row),
                            "to_email" => $this->returnRowValue($headerRow,'to_email' ,$row),
                            'from_name' => $this->returnRowValue($headerRow,'from_name' ,$row),
                            "from_email" => $this->returnRowValue($headerRow,'from_email' ,$row),
                            "amount" => $this->returnRowValue($headerRow,'amount' ,$row),
                            "balance" => $this->returnRowValue($headerRow,'balance' ,$row),
                            "purchase_date" =>  date(DATE_RFC2822, strtotime( $this->returnRowValue($headerRow,'purchase_date' ,$row) )),
                            "expiry_date" => date(DATE_RFC2822, strtotime( $this->returnRowValue($headerRow,'expiry_date' ,$row) )),
                            "customer_id" => $this->returnRowValue($headerRow,'customer_id' ,$row),
                            "template" => $this->returnRowValue($headerRow,'template' ,$row),
                            "message" => $this->returnRowValue($headerRow,'message' ,$row),
                            "code" => $this->returnRowValue($headerRow,'code' ,$row),
                            "status" => $this->returnRowValue($headerRow,'status' ,$row),
                            "bc_id" => (int)$this->returnRowValue($headerRow, 'id', $row),
                            "currency_code" => $this->returnRowValue($headerRow,'currency_code' ,$row),
                            "table_status" => "pending",
                            "type" => "import",
                        ];
                    }
                };
            
                foreach(ArrayHelper::chunkFile($filePath, $generateRow, 1000) as $chunk){
                    GiftCertificate::insert($chunk);
                }

                /** DETERMINE BATCH PROCESSING */
                $pendingIds = DB::selectResultSets(
                    "CALL FetchPendingRecords(?,?)", [ $storeId, $fileId ]
                );

                if(isset($pendingIds[0][0])){
                    $pendingIds = json_decode(json_encode($pendingIds),true);
                    $pendingIds = $pendingIds[0];
                    $totalIds = array_column($pendingIds, 'id');
                    $splitChunks = array_chunk($totalIds,10);

                    $fileProcessCount = count($pendingIds[0]);
                    $batchSize = ceil( $fileProcessCount / 100 );

                    for($ii = 0; $ii < count($splitChunks); $ii++){
                        ImportGiftCertificatesJob::dispatch($storeId, $fileId, "[".implode(",", $splitChunks[$ii])."]");
                    }
                }                 

                return Redirect::back()->with('success', 'The file has started to process.');
            }else{
                return Redirect::back()->with('success', 'Internal Error, Please try again later.');
            }
        }
    }

    private function checkIfProcessing(){

        /**  RETRIEVE CURRENT PROCESSING FILES AND DETERMINE PROGRESS PERCENTAGE  */
        $processing = DB::selectResultSets(
            "CALL GetFileStatus(?,?)", [ $this->storeId, 'import' ]
        );


        if(isset($processing[0][0])){
            $processing = $processing[0][0];
            if(!empty($processing->file_name)){

                $this->processingFile = [
                    "file_id" => $processing->file_id,
                    "file_name" => $processing->file_name,
                    "status" => $processing->status,
                    "updated_at" => $processing->updated_at,
                    "total_count" => $processing->total_count,
                    "pending_count" => $processing->pending_count,
                    "processing_count" => $processing->processing_count,
                    "success_count" => $processing->success_count,
                    "failure_count" => $processing->failure_count,
                    "completed_count" => $processing->completed_count,
                    "percentage" => $this->calculatePercentage( $processing->completed_count, $processing->total_count )
                ];
                return true;
            }
        }
        return false;
    }

    public function formatDownload(){

        $filename = "gift-certificates.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('to_name', 'to_email', 'from_name', 'from_email', 'amount', 'balance', 'purchase_date', 'expiry_date','customer_id', 'template', 'message', 'code', 'status', 'currency_code'));
        fputcsv($handle, array('Jon', 'jondoe@gmail.com', 'Bob', 'bobdoe@gmail.com', '500', '0', 'Mon, 19 Jan 1970 07:21:46 CST', 'Mon, 19 Jan 1970 07:21:46 CST', 1,'birthday.html', 'Congrats', 'GIFT-CODE-1', 'active', 'USD'));
        fclose($handle);

        $headers = array('Content-Type' => 'text/csv');

        return response()->download($filename, 'gift-certificates.csv', $headers);

    }

    public function acknowledge(int $fileId): RedirectResponse
    {
        DB::table('file_status')->where('id', $fileId)->update(array('status' => 'acknowledged'));  
        return Redirect::back()->with('success', 'The file has started to process.');
    }

    private function calculatePercentage($pending, $total){
        return ($pending / $total ) * 100;
    }

}