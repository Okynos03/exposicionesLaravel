<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    // Listar todos los alumnos
    public function index()
    {
        $alumnos = Alumno::with('usuario')->get();
        // Usamos el método del padre
        return $this->sendResponse($alumnos, 'Lista de alumnos recuperada.');
    }

    // Crear un nuevo alumno con transacción
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|min:8',
            'num_ctrl' => 'required|unique:alumnos,num_ctrl',
            'id_rol'   => 'required|exists:roles,id_rol'
        ]);

        try {
            $alumno = DB::transaction(function () use ($request) {
                $usuario = User::create([
                    'nombre'   => $request->nombre,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'id_rol'   => $request->id_rol
                ]);

                return Alumno::create([
                    'id_usuario' => $usuario->id_usuario,
                    'num_ctrl'   => $request->num_ctrl
                ])->load('usuario');
            });

            return $this->sendResponse($alumno, 'Alumno creado con éxito.', 201);

        } catch (\Exception $e) {
            return $this->sendError('Error al procesar el registro', [$e->getMessage()], 500);
        }
    }

    // Mostrar un alumno específico
    public function show($id)
    {
        $alumno = Alumno::with(['usuario', 'grupos', 'equipos'])
                        ->where('id_usuario', $id)
                        ->first();

        if (!$alumno) {
            return $this->sendError('Alumno no encontrado.');
        }

        return $this->sendResponse($alumno, 'Datos del alumno obtenidos.');
    }

    // Actualizar datos
    public function update(Request $request, $id)
    {
        $alumno = Alumno::where('id_usuario', $id)->first();
        if (!$alumno) return $this->sendError('Alumno no encontrado.');

        $usuario = User::find($id);

        $usuario->update($request->only(['nombre', 'email']));
        $alumno->update($request->only(['num_ctrl']));

        return $this->sendResponse($alumno->load('usuario'), 'Alumno actualizado.');
    }

    // Eliminar
    public function destroy($id)
    {
        $usuario = User::find($id);
        if (!$usuario) return $this->sendError('Usuario no existe.');

        $usuario->delete(); // Gracias al ON DELETE CASCADE, borra al Alumno también.

        return $this->sendResponse([], 'Alumno eliminado correctamente.');
    }
}