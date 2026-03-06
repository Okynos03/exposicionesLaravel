<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';
    protected $primaryKey = 'id_grupo';

    protected $fillable = [
        'id_materia',
        'id_maestro',
        'grupo'
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'id_maestro', 'id_usuario');
    }

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'grupos_alumnos', 'id_grupo', 'id_usuario_alumno')
                    ->withTimestamps();
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'id_grupo', 'id_grupo');
    }
}