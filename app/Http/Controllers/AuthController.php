<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Inicio de sesión
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Buscamos al usuario con su rol
        $user = User::where('email', $request->email)->with('rol')->first();

        // Verificamos credenciales
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Credenciales incorrectas.', ['error' => 'No autorizado'], 401);
        }

        // Generamos el token de Sanctum
        $token = $user->createToken('API_TOKEN')->plainTextToken;

        $success = [
            'token' => $token,
            'user'  => $user,
        ];

        return $this->sendResponse($success, 'Usuario autenticado con éxito.');
    }

    /**
     * Registro básico (opcional si ya tienes el de Alumno/Maestro)
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|min:8|confirmed',
            'id_rol'   => 'required|exists:roles,id_rol'
        ]);

        $user = User::create([
            'nombre'   => $request->nombre,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'id_rol'   => $request->id_rol,
        ]);

        $success['token'] = $user->createToken('API_TOKEN')->plainTextToken;
        $success['user']  = $user->load('rol');

        return $this->sendResponse($success, 'Usuario registrado correctamente.', 201);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        // Borramos el token actual del usuario
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'Sesión cerrada y token eliminado.');
    }

    public function me(Request $request)
    {

        $user = $request->user()->load(['rol', 'alumno', 'maestro']);

        if ($user->id_rol == 2 && $user->alumno) {
            $user->alumno->load('grupos.materia');
        }
        
        elseif ($user->id_rol == 1 && $user->maestro) {
            $user->maestro->load('grupos.materia');
        }

        return $this->sendResponse($user, 'Datos del usuario logueado.');
    }
}