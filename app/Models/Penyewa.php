<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'penyewas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'namalengkap',
        'noktp',
        'nohp',
        'jenis_kelamin',
        'alamat',
        'jenis_penyewa',
        'fotoktp',
        'status',
        'operator_id'
    ];

    public function lantais()
    {
        return $this->belongsTo(Lantai::class, 'lantai_id');
    }

    public function transaksisewa_kamars()
    {
        return $this->hasOne(Pembayaran::class)->whereIn('mitra_id', [1, 2])->latest();
    }
}
