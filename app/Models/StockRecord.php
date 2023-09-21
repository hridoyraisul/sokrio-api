<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRecord extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function stockRecordDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockRecordDetails::class);
    }
}
