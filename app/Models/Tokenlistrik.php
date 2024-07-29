<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tokenlistrik extends Model
{
    use HasFactory;

    protected $table = 'tokenlistriks';
    protected $primaryKey = 'id';
}
