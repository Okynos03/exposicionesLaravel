<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exposicion extends Model
{
    use HasFactory;

    protected $table = 'exposiciones';
    protected $primaryKey = 'id_expo';

    protected $fillable = [
        'id_equipo',
        'id_rubrica',
        'tema',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];


    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'id_equipo', 'id_equipo');
    }

    public function rubrica()
    {
        return $this->belongsTo(Rubrica::class, 'id_rubrica', 'id_rubrica');
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'id_expo', 'id_expo');
    }
}