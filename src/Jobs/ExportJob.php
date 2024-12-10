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

use Larapps\GiftCertificateManager\Models\GiftCertificate;

class ExportJob implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public $backoff = 3;

    private $fileId;

    private $storeId;

    private $toProcessIds;

    private $page;

    private $bcStore;

    private $bcAgent;

    /**
     * Create a new job instance.
     */
    public function __construct(int $storeId, int $fileId, int $page)
    {
        $this->fileId = $fileId;
        $this->storeId = $storeId;
        $this->page = $page;
        
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Collection::macro('convertTableFormat', function ($storeId, $fileId) {
            return $this->map(function ($value) use($storeId, $fileId) {
                $value['bc_id'] = $value['id'];
                $value['store_id'] = $storeId;
                $value['file_id'] = $fileId;
                unset($value['id']);
                return $value;
            });
        });

        try{
            Log::channel('my-package')->info("process Ids". $this->toProcessIds);            

            $toProcessRecords = DB::selectResultSets(
                "CALL ManageFile(?,?,?)", [ $this->fileId, 'export', 'processing' ]
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

                    // dd($toProcessRecords[0]->additional_info);

                    $toProcessRecords[0]->additional_info = json_decode($toProcessRecords[0]->additional_info);

                    $filters = $toProcessRecords[0]->additional_info->filters;

                    $requestParams = [
                        "max_id" => $filters->min_id,
                        "min_id" => $filters->max_id,
                        "to_name" => $filters->to_name,
                        "to_email" => $filters->to_email,
                        "order_id" => $filters->order_id,
                        "from_name" => $filters->from_name,
                        "from_email" => $filters->from_email,
                        'limit' => 250,
                        'page' => $this->page,
                    ];
           
                    $response = $this->bcAgent->getGiftCertificates($requestParams);

                    if($response->getStatusCode() === 200){
                        $data = $response->json();
                        $collection = collect($data);
                        $insertData = $collection
                            ->convertTableFormat( $this->storeId, $this->fileId )
                            ->toArray();

                        $response = GiftCertificate::insert($insertData);

                        /*** Dispatch the next set of jobs */
                        $limit = 5;
                        $newStartPage = $this->page + 1;
                        /**
                         * 
                         *  To Run as below
                         * page 1, new start page = 2, limit = 5 and total runs upto 2, 3, 4, 5
                         * page 2, don't run 
                         * page 3, don't run
                         * page 4, don't run
                         * page 5, new start page = 6, limit = 5 and total runs upto 6, 7, 8, 9, 10
                         * page 6, don't run
                         * page 7, don't run
                         * page 8, don't run
                         * page 9, don't run
                         * page 10, new start page = 11, limit = 5 and total runs upto 11, 12, 13, 14, 15
                         * 
                         */
                        $pageToBeAdded = $this->page === 1 ? $this->page : $newStartPage;

                        if(($this->page % $limit === 0) || $this->page === 1){
                            for( $ii = $newStartPage; $ii < ($pageToBeAdded + $limit); $ii++ ){
                                $requestParams = [
                                    "max_id" => $filters->min_id,
                                    "min_id" => $filters->max_id,
                                    "to_name" => $filters->to_name,
                                    "to_email" => $filters->to_email,
                                    "order_id" => $filters->order_id,
                                    "from_name" => $filters->from_name,
                                    "from_email" => $filters->from_email,
                                    'limit' => 250,
                                    'page' => $ii,
                                ];
                       
                                $response = $this->bcAgent->getGiftCertificates($requestParams);
                                if($response->getStatusCode() === 200){
                                    dispatch(new ExportJob( $this->storeId, $this->fileId, $ii ));
                                }
    
                                if($response->getStatusCode() === 204){
                                    DB::selectResultSets(
                                        "CALL ManageFile(?,?,?)", [ $this->fileId, 'export', 'exported' ]
                                    );

                                    dispatch(new ExportFileJob( $this->storeId, $this->fileId ));
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }catch(Exception $e){
            Log::channel('my-package')->info("Error");            
        }

    }
}
