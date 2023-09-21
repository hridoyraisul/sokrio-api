<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRecordDetails extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function salesRecord()
    {
        return $this->belongsTo(SalesRecord::class);
    }
}
