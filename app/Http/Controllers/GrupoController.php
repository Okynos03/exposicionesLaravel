<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    /**
     * Listar todos los grupos con su materia y maestro
     */
    public function index()
    {
        $grupos = Grupo::with(['materia', 'maestro.usuario'])->get();
        return $this->sendResponse($grupos, 'Grupos recuperados con éxito.');
    }

    /**
     * Crear un nuevo grupo
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_materia' => 'required|exists:materias,id_materia',
            'id_maestro' => 'required|exists:maestros,id_usuario',
            'grupo'      => 'required|string|max:10'
        ]);

        $grupo = Grupo::create($request->all());

        return $this->sendResponse(
            $grupo->load(['materia', 'maestro.usuario']),
            'Grupo creado correctamente.',
            201
        );
    }

    /**
     * Mostrar un grupo específico con sus alumnos inscritos
     */
    public function show($id)
    {
        $grupo = Grupo::with(['materia', 'maestro.usuario', 'alumnos.usuario'])
                      ->find($id);

        if (!$grupo) {
            return $this->sendError('Grupo no encontrado.');
        }

        return $this->sendResponse($grupo, 'Detalles del grupo obtenidos.');
    }

    /**
     * Inscribir o actualizar la lista de alumnos del grupo
     * Se espera un array de IDs de alumnos (id_usuario)
     */
    public function inscribirAlumnos(Request $request, $id)
    {
        $request->validate([
            'alumnos' => 'required|array',
            'alumnos.*' => 'exists:alumnos,id_usuario'
        ]);

        $grupo = Grupo::find($id);
        if (!$grupo) return $this->sendError('Grupo no encontrado.');

        // sync() elimina los que no estén en el array y agrega los nuevos
        $grupo->alumnos()->sync($request->alumnos);

        return $this->sendResponse(
            $grupo->load('alumnos.usuario'),
            'Lista de alumnos actualizada correctamente.'
        );
    }

    /**
     * Eliminar un grupo
     */
    public function destroy($id)
    {
        $grupo = Grupo::find($id);
        if (!$grupo) return $this->sendError('Grupo no encontrado.');

        $grupo->delete();
        return $this->sendResponse([], 'Grupo eliminado exitosamente.');
    }
}