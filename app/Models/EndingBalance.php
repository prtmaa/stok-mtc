<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndingBalance extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'ending_balance';

    protected $fillable = ['item_id', 'periode', 'jumlah', 'harga', 'total_harga'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
