<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayarandetail extends Model
{
    protected $table = 'pembayarandetails';
    protected $primaryKey = 'id';

    public function penyewas()
    {
        return $this->hasOne(Penyewa::class, 'id', 'penyewa_id');
    }
}
