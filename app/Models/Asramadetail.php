<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asramadetail extends Model
{
    protected $table = 'asramadetails';
    protected $primaryKey = 'id';

    public function penyewas()
    {
        return $this->hasOne(Penyewa::class, 'id', 'penyewa_id');
    }

    public function lokasis()
    {
        return $this->hasOne(Lokasi::class, 'id', 'lokasi_id');
    }

    public function tipekamars()
    {
        return $this->hasOne(Tipekamar::class, 'id', 'tipekamar_id');
    }
}
