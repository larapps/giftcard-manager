<?php

namespace Larapps\GiftCertificateManager\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class GiftCertificateResource extends JsonResource {
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request){
        
        return [
            "to_name"=> $this->to_name,
            "to_email"=> $this->to_email,
            "from_name"=> $this->from_name,
            "from_email"=> $this->from_email,
            "amount"=> $this->amount,
            "balance"=> $this->balance,
            "purchase_date"=> $this->purchase_date,
            "expiry_date"=> $this->expiry_date,
            "customer_id"=> $this->customer_id,
            "template"=> $this->template,
            "message"=> $this->message,
            "code"=> $this->code,
            "status"=> $this->status,
            "currency_code"=> $this->currency_code,
        ];
    }


}

