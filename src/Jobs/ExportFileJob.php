<?php

namespace Larapps\GiftCertificateManager\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Support\Facades\DB;
use Larapps\GiftCertificateManager\Models\BCStore;
use Larapps\GiftCertificateManager\App\Http\Resources\GiftCertificateResource;
use Larapps\GiftCertificateManager\Connectors\BigCommerceAgent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Collection;
use Larapps\GiftCertificateManager\Models\FileStatus;
use Larapps\GiftCertificateManager\Models\GiftCertificate;

class ExportFileJob implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public $backoff = 3;

    private int $fileId;

    private int $storeId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $storeId, int $fileId)
    {
        $this->fileId = $fileId;
        $this->storeId = $storeId;
    }


    /**
     * Execute the job.
     */
    public function handle()
    {

        try{

            $limit = 1000;
            $page = 1;
            $totalPages = 1;

            $toProcessFiles = DB::selectResultSets(
                "CALL ManageFile(?,?,?)", [ $this->fileId, 'export', 'exported' ]
            );

            $filename = "";
            $additionalInfo = [];
            // dd($toProcessFiles);
            if(isset($toProcessFiles[0][0])){
                $filename = $toProcessFiles[0][0]->file_name;
                $additionalInfo = json_decode($toProcessFiles[0][0]->additional_info, true);
                $additionalInfo['file_name'] = $filename;
                $filePath = storage_path("app/$filename");
                if (!file_exists(dirname($filePath))) {
                    mkdir(dirname($filePath), 0755, true);
                }
            }

            $handle = fopen($filePath, 'w+');

            $columnHeader = array_keys(array_filter($additionalInfo['fields'], fn($value) => $value === true));
            fputcsv($handle, $columnHeader);

            for( $ii = $page; $ii <= $totalPages; $ii++){
                $toProcessRecords = DB::selectResultSets(
                    "CALL FetchExportRecords(?,?,?,?)", [ $this->storeId, $this->fileId, $limit, ($ii - 1) * $limit ]
                );
                if(isset($toProcessRecords[0][0])){
                    $totalPages = ceil ( $toProcessRecords[0][0]->total_count / $limit);
                    $toProcessRecords = $toProcessRecords[0];

                    foreach($toProcessRecords as $exportIndex => $exportRow){
                        $toBeExported = $this->generateRow( $columnHeader, $exportRow );
                        fputcsv($handle, $toBeExported );
                    }
                }
            }

            fclose($handle);

            FileStatus::where('id',$this->fileId)
            ->update(['additional_info'=> json_encode($additionalInfo) ]);

            $toProcessFiles = DB::selectResultSets(
                "CALL ManageFile(?,?,?)", [ $this->fileId, 'export', 'completed' ]
            );

            $headers = array('Content-Type' => 'text/csv');
            // return response()->download($filename, 'gift-certificates.csv', $headers);
        }catch(Exception $e){
            Log::channel('my-package')->info("Error");            
        }

    }

    public function generateRow($columns, $data){
        $fields = [];

        foreach($columns as $columnIndex => $columnRow){

            if($columnRow === 'id'){
                $columnRow = 'bc_id';
            }

            if(isset($data->{$columnRow})){
                array_push( $fields, $data->{$columnRow} );
            }
        }
        return $fields;
    }
}
