<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penggunaanbaranginventaris extends Model
{
    use HasFactory;

    protected $table = 'penggunaanbaranginventaris';
    protected $primaryKey = 'id';

    public function kategoris()
    {
        return $this->hasOne(Kategoribaranginventaris::class, 'id', 'kategoribaranginventaris_id');
    }

    public function lokasis()
    {
        return $this->hasOne(Lokasi::class, 'id', 'lokasi_id');
    }
}
