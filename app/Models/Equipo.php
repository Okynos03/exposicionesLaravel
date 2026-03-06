<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipos';
    protected $primaryKey = 'id_equipo';

    protected $fillable = [
        'id_grupo',
        'equipo',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }

    public function integrantes()
    {
        return $this->belongsToMany(Alumno::class, 'equipo_integrantes', 'id_equipo', 'id_usuario_alumno')
                    ->withTimestamps();
    }

    public function exposiciones()
    {
        return $this->hasMany(Exposicion::class, 'id_equipo', 'id_equipo');
    }
}