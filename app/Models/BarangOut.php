<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangOut extends Model
{
    use HasFactory;

    protected $table = 'barang_out';

    protected $fillable = [
        'tanggal',
        'code',
        'item_id',
        'divisi_id',
        'jumlah',
        'harga',
        'total_harga',
        'note',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}
