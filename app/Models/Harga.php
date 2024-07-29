<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harga extends Model
{
    use HasFactory;

    protected $table = 'hargas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipekamar_id',
        'mitra_id',
        'harian',
        'mingguan',
        'hari14',
        'bulanan',
        'operator_id'
    ];

    public function tipekamars()
    {
        return $this->hasOne(Tipekamar::class, 'id', 'tipekamar_id');
    }

    public function mitras()
    {
        return $this->hasOne(Mitra::class, 'id', 'mitra_id');
    }
}
