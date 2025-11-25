<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokTransaksi extends Model
{
    use HasFactory;

    protected $table = 'stok_transaksi';

    protected $fillable = [
        'tanggal',
        'item_id',
        'tipe',
        'jumlah',
        'stok_akhir',
        'referensi',
        'note',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
