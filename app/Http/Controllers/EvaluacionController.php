<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
{
    /**
     * Listar evaluaciones con sus detalles
     */
    public function index()
    {
        $evaluaciones = Evaluacion::with(['exposicion', 'usuario', 'detalles.criterio'])->get();
        return $this->sendResponse($evaluaciones, 'Evaluaciones recuperadas.');
    }

    /**
     * Guardar una nueva evaluación
     * Se espera un JSON con id_expo, id_usuario y un array de notas por criterio
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_expo'      => 'required|exists:exposiciones,id_expo',
            'id_usuario'   => 'required|exists:usuarios,id_usuario',
            'observaciones'=> 'nullable|string',
            'calificaciones' => 'required|array', // Array de [id_criterio => nota]
            'calificaciones.*.id_criterio' => 'required|exists:criterios,id_criterios',
            'calificaciones.*.nota'        => 'required|numeric|min:0|max:10'
        ]);

        try {
            $evaluacion = DB::transaction(function () use ($request) {
                // 1. Crear la cabecera de la evaluación
                $nuevaEval = Evaluacion::create([
                    'id_expo'      => $request->id_expo,
                    'id_usuario'   => $request->id_usuario,
                    'observaciones'=> $request->observaciones,
                    'fecha'        => now()
                ]);

                // 2. Guardar el detalle de cada criterio
                foreach ($request->calificaciones as $item) {
                    // Si usas el modelo EvaluacionDetalle:
                    $nuevaEval->detalles()->create([
                        'id_criterios' => $item['id_criterio'],
                        'calificacion' => $item['nota']
                    ]);
                }

                return $nuevaEval->load('detalles.criterio');
            });

            return $this->sendResponse($evaluacion, 'Evaluación registrada correctamente.', 201);

        } catch (\Exception $e) {
            return $this->sendError('Error al registrar evaluación.', [$e->getMessage()], 500);
        }
    }

    /**
     * Ver el resultado de una evaluación específica
     */
    public function show($id)
    {
        $evaluacion = Evaluacion::with([
            'exposicion.equipo',
            'usuario',
            'detalles.criterio'
        ])->find($id);

        if (!$evaluacion) return $this->sendError('Evaluación no encontrada.');

        return $this->sendResponse($evaluacion, 'Detalle de evaluación obtenido.');
    }
}