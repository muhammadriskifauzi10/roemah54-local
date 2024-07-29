<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipekamar extends Model
{
    use HasFactory;

    protected $table = 'tipekamars';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipekamar',
        'operator_id'
    ];

    public function hargas()
    {
        return $this->hasOne(Harga::class);
    }
}
