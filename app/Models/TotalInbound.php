<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalInbound extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'periode',
        'jumlah',
        'harga',
        'total_harga',
    ];

    protected $casts = [
        'periode' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
