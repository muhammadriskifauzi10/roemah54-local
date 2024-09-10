<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksiasrama extends Model
{
    protected $table = 'transaksiasramas';
    protected $primaryKey = 'id';

    public function tagihan()
    {
        return $this->hasOne(Tagih::class, 'id', 'tagih_id');
    }

    public function penyewas()
    {
        return $this->hasOne(Penyewa::class, 'id', 'penyewa_id');
    }

    public function lokasis()
    {
        return $this->hasOne(Lokasi::class, 'id', 'lokasi_id');
    }
}
