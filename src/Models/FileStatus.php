<?php

namespace Larapps\GiftCertificateManager\Models;

use Illuminate\Database\Eloquent\Model;

class FileStatus extends Model
{
    protected $table = 'file_status';

    protected $fillable = ['file_name', 'status', 'store_id', 'type', 'additional_info'];

    public function scopeRetrieveFiles($query, $filters){
        if(isset($filters['store_id'])){
            $query->where("store_id",$filters['store_id']);
        }
        if(isset($filters['file_id'])){
            $query->where("file_id",$filters['file_id']);
        }
        if(isset($filters['status'])){
            $query->where("status",$filters['status']);
        }
    }
}
