<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangIn extends Model
{
    use HasFactory;

    protected $table = 'barang_in';

    protected $fillable = [
        'tanggal',
        'code',
        'item_id',
        'supplier_id',
        'jumlah',
        'harga',
        'total_harga',
        'note',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
