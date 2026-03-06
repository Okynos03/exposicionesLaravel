<?php

namespace App\Models;

// Importante: Usar Authenticatable para el sistema de Login/Tokens
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Si usas Sanctum para la API
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_rol',
        'nombre',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function rol()
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }


    public function maestro()
    {
        return $this->hasOne(Maestro::class, 'id_usuario', 'id_usuario');
    }


    public function alumno()
    {
        return $this->hasOne(Alumno::class, 'id_usuario', 'id_usuario');
    }
}
