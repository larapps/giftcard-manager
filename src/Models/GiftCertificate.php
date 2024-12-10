<?php

namespace Larapps\GiftCertificateManager\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCertificate extends Model
{
    
    protected $table = 'gift_certificates';

    protected $fillable = [
        'bc_id',
        'store_id',
        'file_id',
        'to_name',
        "to_email",
        'from_name',
        "from_email",
        "amount",
        "balance",
        "purchase_date",
        "expiry_date",
        "customer_id",
        "template",
        "message",
        "code",
        "status",
        "currency_code",
        "table_output",
        "table_status",
        "table_output_reason",
        "type",
        "order_id"
    ];

    public $timestamps = false;

    public function scopeRetrieveRows($query, $filters){
        if(isset($filters['store_id'])){
            $query->where("store_id",$filters['store_id']);
        }
        if(isset($filters['file_id'])){
            $query->where("file_id",$filters['file_id']);
        }
        if(isset($filters['status'])){
            $query->where("status",$filters['status']);
        }

        if(isset($filters['output'])){
            $query->where("output",$filters['output']);
        }
    }
}
