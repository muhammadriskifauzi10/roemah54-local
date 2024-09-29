<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    protected $primaryKey = 'id';

    // Relasi untuk submenu
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order', 'ASC');
    }

    // Relasi untuk menu induk
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
}
