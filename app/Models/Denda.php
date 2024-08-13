<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    protected $table = 'dendas';
    protected $primaryKey = 'id';

    public function penyewas()
    {
        return $this->hasOne(Penyewa::class, 'id', 'penyewa_id');
    }

    public function lokasis()
    {
        return $this->hasOne(Lokasi::class, 'id', 'lokasi_id');
    }
}
