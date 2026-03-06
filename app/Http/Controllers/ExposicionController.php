<?php

namespace App\Http\Controllers;

use App\Models\Exposicion;
use Illuminate\Http\Request;

class ExposicionController extends Controller
{
    /**
     * Listar todas las exposiciones programadas
     */
    public function index()
    {
        // Traemos el equipo, la materia del grupo y la rúbrica que se usará
        $exposiciones = Exposicion::with(['equipo.grupo.materia', 'rubrica'])->get();
        return $this->sendResponse($exposiciones, 'Exposiciones recuperadas.');
    }

    /**
     * Programar una nueva exposición
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_equipo'  => 'required|exists:equipos,id_equipo',
            'id_rubrica' => 'required|exists:rubricas,id_rubrica',
            'tema'       => 'required|string|max:255',
            'fecha'      => 'required|date_format:Y-m-d H:i:s'
        ]);

        $exposicion = Exposicion::create($request->all());

        return $this->sendResponse(
            $exposicion->load(['equipo', 'rubrica']),
            'Exposición programada correctamente.',
            201
        );
    }

    /**
     * Ver detalles de una exposición (incluyendo criterios de su rúbrica)
     * Útil para cuando el evaluador abre el formulario de calificación
     */
    public function show($id)
    {
        $exposicion = Exposicion::with([
            'equipo.integrantes.usuario',
            'rubrica.criterios',
            'evaluaciones.usuario'
        ])->find($id);

        if (!$exposicion) {
            return $this->sendError('Exposición no encontrada.');
        }

        return $this->sendResponse($exposicion, 'Detalles de la exposición cargados.');
    }

    /**
     * Actualizar fecha o tema
     */
    public function update(Request $request, $id)
    {
        $exposicion = Exposicion::find($id);
        if (!$exposicion) return $this->sendError('Exposición no encontrada.');

        $exposicion->update($request->only(['tema', 'fecha', 'id_rubrica']));

        return $this->sendResponse($exposicion, 'Exposición actualizada.');
    }

    /**
     * Cancelar una exposición
     */
    public function destroy($id)
    {
        $exposicion = Exposicion::find($id);
        if (!$exposicion) return $this->sendError('Exposición no encontrada.');

        $exposicion->delete();
        return $this->sendResponse([], 'Exposición eliminada.');
    }
}