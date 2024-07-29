<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lantai extends Model
{
    use HasFactory;

    protected $table = 'lantais';
    protected $primaryKey = 'id';

    protected $fillable = [
        'namalantai',
        'operator_id'
    ];

    public function lokasis()
    {
        return $this->hasMany(Lokasi::class)->where('jenisruangan_id', 2);
    }
}
