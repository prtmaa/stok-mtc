<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'kategori_id',
        'nama',
        'satuan',
        'stok_akhir',
    ];

    public function barangMasuk()
    {
        return $this->hasMany(BarangIn::class, 'item_id');
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangOut::class, 'item_id');
    }

    public function stokTransaksi()
    {
        return $this->hasMany(StokTransaksi::class, 'item_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
}
