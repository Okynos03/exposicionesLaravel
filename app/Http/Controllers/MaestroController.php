<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MaestroController extends Controller
{
    // Listar todos los maestros con su información de usuario
    public function index()
    {
        $maestros = Maestro::with('usuario')->get();
        return $this->sendResponse($maestros, 'Lista de maestros recuperada.');
    }

    // Crear un maestro (Usuario + Perfil de Maestro)
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|min:8',
            'id_rol'   => 'required|exists:roles,id_rol'
        ]);

        try {
            $maestro = DB::transaction(function () use ($request) {
                // 1. Crear el usuario
                $usuario = User::create([
                    'nombre'   => $request->nombre,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'id_rol'   => $request->id_rol
                ]);

                // 2. Crear el perfil de maestro
                return Maestro::create([
                    'id_usuario' => $usuario->id_usuario
                ])->load('usuario');
            });

            return $this->sendResponse($maestro, 'Maestro creado con éxito.', 201);

        } catch (\Exception $e) {
            return $this->sendError('Error al registrar maestro.', [$e->getMessage()], 500);
        }
    }

    // Ver un maestro con sus grupos y materias
    public function show($id)
    {
        // Buscamos por id_usuario que es la PK en tu tabla maestros
        $maestro = Maestro::with(['usuario', 'grupos.materia'])
                          ->where('id_usuario', $id)
                          ->first();

        if (!$maestro) {
            return $this->sendError('Maestro no encontrado.');
        }

        return $this->sendResponse($maestro, 'Detalles del maestro recuperados.');
    }

    // Actualizar datos del maestro
    public function update(Request $request, $id)
    {
        $usuario = User::find($id);
        if (!$usuario) return $this->sendError('Usuario no encontrado.');

        $usuario->update($request->only(['nombre', 'email']));

        return $this->sendResponse($usuario->load('maestro'), 'Datos actualizados.');
    }

    // Eliminar maestro
    public function destroy($id)
    {
        $usuario = User::find($id);
        if (!$usuario) return $this->sendError('Maestro no existe.');

        // Al borrar el usuario, se borra el maestro por el CASCADE de la DB
        $usuario->delete();

        return $this->sendResponse([], 'Maestro eliminado correctamente.');
    }
}