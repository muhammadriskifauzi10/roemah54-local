<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';
    protected $primaryKey = 'id';

    public function tagihan()
    {
        return $this->hasOne(Tagih::class, 'id', 'tagih_id');
    }

    // public function penyewas() {
    //     return $this->hasOne(Penyewa::class, 'id', 'penyewa_id');
    // }
}
