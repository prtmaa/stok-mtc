<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Used extends Model
{
    use HasFactory;

    protected $table = 'used'; // nama tabel di database

    protected $fillable = [
        'item_id',
        'periode',
        'jumlah',
        'harga',
        'total_harga',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
