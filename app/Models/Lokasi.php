<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jenisruangan_id',
        'lantai_id',
        'nomor_kamar',
        'tipekamar_id',
        'token_listrik',
        'status',
        'operator_id'
    ];

    public function lantais()
    {
        return $this->hasOne(Lantai::class, 'id', 'lantai_id');
    }

    public function tipekamars()
    {
        return $this->hasOne(Tipekamar::class, 'id', 'tipekamar_id');
    }

    public function hargas()
    {
        return $this->hasOne(Harga::class, 'tipekamar_id', 'tipekamar_id');
    }

    public function transaksisewa_kamars()
    {
        return $this->hasOne(Pembayaran::class)->where('tagih_id', 1)->latest();
    }
}
