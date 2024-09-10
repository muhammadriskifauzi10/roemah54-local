<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    protected $table = 'asramas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'lantai_id',
        'nomor_kamar',
        'tipekamar_id',
        'tipekamar',
        'jumlah_mahasiswa',
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
}
