<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbaranginventaris extends Model
{
    use HasFactory;

    protected $table = 'logbaranginventaris';
    protected $primaryKey = 'id';

    public function kategoris()
    {
        return $this->hasOne(Kategoribaranginventaris::class, 'id', 'kategoribaranginventaris_id');
    }
}
