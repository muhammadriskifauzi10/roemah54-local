<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ritel extends Model
{
    protected $table = 'ritels';
    protected $primaryKey = 'id';

    public function penyewas()
    {
        return $this->hasOne(Penyewa::class, 'id', 'penyewa_id');
    }

    public function lokasis()
    {
        return $this->hasOne(Lokasi::class, 'id', 'lokasi_id');
    }

    public function tagihs()
    {
        return $this->hasOne(Tagih::class, 'id', 'jenis_ritel');
    }
}
