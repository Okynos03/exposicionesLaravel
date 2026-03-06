<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubrica extends Model
{
    use HasFactory;

    protected $table = 'rubricas';
    protected $primaryKey = 'id_rubrica';

    protected $fillable = [
        'rubrica'
    ];

    public function criterios()
    {
        return $this->hasMany(Criterio::class, 'id_rubrica', 'id_rubrica');
    }

    public function exposiciones()
    {
        return $this->hasMany(Exposicion::class, 'id_rubrica', 'id_rubrica');
    }
}
