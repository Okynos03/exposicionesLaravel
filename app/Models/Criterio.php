<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterio extends Model
{
    use HasFactory;

    protected $table = 'criterios';
    protected $primaryKey = 'id_criterios';

    protected $fillable = [
        'id_rubrica',
        'descripcion',
        'porcentaje'
    ];


    public function rubrica()
    {
        return $this->belongsTo(Rubrica::class, 'id_rubrica', 'id_rubrica');
    }

    public function evaluaciones()
    {
        return $this->belongsToMany(Evaluacion::class, 'evaluacion_detalles', 'id_criterios', 'id_evaluacion')
            ->withPivot('calificacion')
            ->withTimestamps();
    }
}
