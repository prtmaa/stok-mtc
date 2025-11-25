<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trading extends Model
{
    use HasFactory;

    protected $table = 'trading'; // nama tabel di database

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
