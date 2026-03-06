<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';
    protected $primaryKey = 'id_evaluacion';
     protected $fillable = [
        'id_expo',
        'id_usuario'
    ];
    public function exposicion()
    {
        return $this->belongsTo(Exposicion::class, 'id_expo', 'id_expo');
    }


    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    
    public function detalles()
    {
        return $this->belongsToMany(Criterio::class, 'evaluacion_detalles', 'id_evaluacion', 'id_criterios')
                    ->withPivot('calificacion')
                    ->withTimestamps();
    }
}
