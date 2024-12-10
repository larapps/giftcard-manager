<?php

namespace Larapps\GiftCertificateManager\Connectors;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Larapps\GiftCertificateManager\Models\BCStore;

class BigCommerceAgent{

  const MAX_TIMEOUT = 0;

  private $agent;
  private $url;
  private $token;
  private $clientId;
  private $bcStore;

  public function __construct(Http $httpClient, BCStore $bcStore){
    $this->agent = $httpClient;
    $this->bcStore = $bcStore;
  }

  public function initStoreDetails($storeHash){
    $this->url = config("giftcertificatepackage.bc_url"). $storeHash ."/";
    $this->bcStore = BCStore::where('store_hash','=',$storeHash)->get();
    if($this->bcStore->count() > 0){
        $this->token = $this->bcStore[0]->access_token;
    }
    return $this;
  }

  public function setStoreHash(string $hash){
    $this->url = config("giftcertificatepackage.bc_url").$hash."/";
    return $this;
  }

  public function setAccessToken(string $token){
    $this->token = $token;
    return $this;
  }

  public function getGiftCertificates(array $query): Response
  {
    return $this->request()->get($this->url . 'v2/gift_certificates?' . http_build_query($query));
  }
  
  public function createGiftCertificate(array $data): Response
  {
    return $this->request()->post($this->url . 'v2/gift_certificates', $data);
  }

  public function updateGiftCertificate(int $giftCertificateId, array $data): Response
  {
    return $this->request()->put($this->url . 'v2/gift_certificates/'.$giftCertificateId, $data);
  }

  public function deleteGiftCards(int $giftCertificateId): Response
  {
    return $this->request()->delete($this->url . 'v2/gift_certificates/'.$giftCertificateId);
  }


  protected function request()
  {
    $request = $this->agent::withHeaders([
      'X-Auth-Token' => $this->token,
      'Accept' => 'application/json',
      'Content-Type' => 'application/json'
    ]);
    $request->timeout(self::MAX_TIMEOUT);
    $request->asJson();
    return $request;
  }
}
