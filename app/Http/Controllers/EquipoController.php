<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipoController extends Controller
{
    // Listar todos los equipos con sus integrantes y grupo
    public function index()
    {
        $equipos = Equipo::with(['grupo.materia', 'integrantes.usuario'])->get();
        return $this->sendResponse($equipos, 'Equipos recuperados exitosamente.');
    }

    // Crear un equipo
    public function store(Request $request)
    {
        $request->validate([
            'id_grupo' => 'required|exists:grupos,id_grupo',
            'equipo'   => 'required|string|max:100',
            'alumnos'  => 'required|array', // Array de IDs de alumnos
            'alumnos.*'=> 'exists:alumnos,id_usuario'
        ]);

        try {
            $nuevoEquipo = DB::transaction(function () use ($request) {
                // 1. Crear el equipo
                $equipo = Equipo::create([
                    'id_grupo' => $request->id_grupo,
                    'equipo'   => $request->equipo,
                    'active'   => true
                ]);

                // 2. Sincronizar los integrantes en la tabla equipo_integrantes
                $equipo->integrantes()->attach($request->alumnos);

                return $equipo->load('integrantes.usuario');
            });

            return $this->sendResponse($nuevoEquipo, 'Equipo creado e integrantes asignados.', 201);

        } catch (\Exception $e) {
            return $this->sendError('Error al crear el equipo.', [$e->getMessage()], 500);
        }
    }

    // Ver un equipo específico
    public function show($id)
    {
        $equipo = Equipo::with(['grupo.materia', 'integrantes.usuario', 'exposiciones'])
                        ->find($id);

        if (!$equipo) {
            return $this->sendError('Equipo no encontrado.');
        }

        return $this->sendResponse($equipo, 'Detalles del equipo recuperados.');
    }

    // Actualizar integrantes del equipo
    public function updateIntegrantes(Request $request, $id)
    {
        $request->validate([
            'alumnos' => 'required|array',
            'alumnos.*' => 'exists:alumnos,id_usuario'
        ]);

        $equipo = Equipo::find($id);
        if (!$equipo) return $this->sendError('Equipo no encontrado.');

        // sync() es perfecto aquí: borra los que ya no están y agrega los nuevos
        $equipo->integrantes()->sync($request->alumnos);

        return $this->sendResponse($equipo->load('integrantes.usuario'), 'Integrantes actualizados.');
    }

    // Eliminar equipo
    public function destroy($id)
    {
        $equipo = Equipo::find($id);
        if (!$equipo) return $this->sendError('Equipo no encontrado.');

        $equipo->delete();
        return $this->sendResponse([], 'Equipo eliminado correctamente.');
    }
}