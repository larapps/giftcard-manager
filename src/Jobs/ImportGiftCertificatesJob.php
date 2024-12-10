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

class ImportGiftCertificatesJob implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public $backoff = 3;

    private $fileId;

    private $storeId;

    private $toProcessIds;

    private $jobService;

    private $bcStore;

    private $bcAgent;

    /**
     * Create a new job instance.
     */
    public function __construct(int $storeId, int $fileId, string $toProcessIds)
    {
        $this->fileId = $fileId;
        $this->storeId = $storeId;
        $this->toProcessIds = $toProcessIds;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try{
            Log::channel('my-package')->info("process Ids". $this->toProcessIds);            

            $toProcessRecords = DB::selectResultSets(
                "CALL UpdateStatus(?,?)", [ $this->toProcessIds, 'import' ]
            );

            if(isset($toProcessRecords[0][0])){

                $toProcessRecords = (array)$toProcessRecords[0];
                $toUpdateRecords = [];
                if(count($toProcessRecords) > 0){
                    $this->bcStore = BCStore::query()->where(["id" => $this->storeId])->get();
                    if($this->bcStore->count() > 0){
                        $this->bcStore = $this->bcStore[0];
                        $this->bcAgent = new BigCommerceAgent(new Http, new BCStore);
                        $this->bcAgent->setStoreHash($this->bcStore->store_hash)->setAccessToken($this->bcStore->access_token);
                    }
                    foreach($toProcessRecords as $giftCertificateIndex => $giftCertificateRow){
                        if(empty($giftCertificateRow->bc_id)){
                            /** CREATE GIFT CERTIFICATES */
                            $resource = GiftCertificateResource::make($giftCertificateRow)->resolve();
                            $response = $this->bcAgent->createGiftCertificate($resource);
                            if($response->getStatusCode() === 201){
                                $toUpdateRecords[] = [
                                    'id' => $giftCertificateRow->id,
                                    'table_output' => 'success',
                                    'table_status' => 'completed'
                                ];
                            }else{
                                $errorMessage = json_decode($response->getBody()->getContents());
                                if(isset($errorMessage[0]->details->conflict_reason)){
                                    $errorMessage = $errorMessage[0]->details->conflict_reason;
                                }else{
                                    $errorMessage = json_encode($errorMessage);
                                }
                                $toUpdateRecords[] = [
                                    'id' => $giftCertificateRow->id,
                                    'table_output' => 'failure',
                                    'table_status' => 'completed',
                                    'table_output_reason' => $errorMessage
                                ];
                            }
                        }else{
                            /** UPDATE GIFT CERTIFICATES */
                            $resource = GiftCertificateResource::make($giftCertificateRow)->resolve();
                            unset($resource['currency_code']);
                            $response = $this->bcAgent->updateGiftCertificate($giftCertificateRow->bc_id, $resource);
                            if($response->getStatusCode() === 200){
                                $toUpdateRecords[] = [
                                    'id' => $giftCertificateRow->id,
                                    'table_output' => 'success',
                                    'table_status' => 'completed'
                                ];
                            }else{
                                $errorMessage = json_decode($response->getBody()->getContents());
                                if(isset($errorMessage[0]->details->conflict_reason)){
                                    $errorMessage = $errorMessage[0]->details->conflict_reason;
                                }else{
                                    $errorMessage = json_encode($errorMessage);
                                }
                                $toUpdateRecords[] = [
                                    'id' => $giftCertificateRow->id,
                                    'table_output' => 'failure',
                                    'table_status' => 'completed',
                                    'table_output_reason' => $errorMessage
                                ];
                            }
                        }
                        DB::selectResultSets(
                            "CALL UpdateMultipleRecords(?)", [ json_encode($toUpdateRecords) ]
                        );
                        $toUpdateRecords = [];
                    }
                    /** BULK UPDATION OF STATUS */


                    /** UPDATE PENDING OR PROCESSING */
                    $manageFiles = DB::selectResultSets(
                        "CALL UpdateFileStatus(?)", [ $this->fileId ]
                    );
                }
            }
        }catch(Exception $e){
            Log::channel('my-package')->info("Error");            
        }

    }
}
