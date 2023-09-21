<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRecordDetails extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function stockRecord()
    {
        return $this->belongsTo(StockRecord::class);
    }
}
