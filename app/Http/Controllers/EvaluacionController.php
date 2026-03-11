<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use App\Models\Exposicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
{
    public function index()
    {
        $evaluaciones = Evaluacion::with(['exposicion', 'usuario', 'detalles.criterio'])->get();
        return $this->sendResponse($evaluaciones, 'Evaluaciones recuperadas.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_expo'                      => 'required|exists:exposiciones,id_expo',
            'id_usuario'                   => 'required|exists:usuarios,id_usuario',
            'observaciones'                => 'nullable|string',
            'calificaciones'               => 'required|array',
            'calificaciones.*.id_criterio' => 'required|exists:criterios,id_criterios',
            'calificaciones.*.nota'        => 'required|numeric|min:0|max:10'
        ]);

        $existeEvaluacion = Evaluacion::where('id_expo', $request->id_expo)
                                      ->where('id_usuario', $request->id_usuario)
                                      ->exists();

        if ($existeEvaluacion) {
            return $this->sendError('Ya has evaluado esta exposición.', [], 400);
        }

        $exposicion = Exposicion::with([
            'equipo.grupo.alumnos', 
            'equipo.integrantes', 
            'rubrica.criterios'
        ])->find($request->id_expo);

        $perteneceAlGrupo = $exposicion->equipo->grupo->alumnos->contains('id_usuario', $request->id_usuario);
        
        if (!$perteneceAlGrupo) {
             return $this->sendError('No puedes evaluar una exposición de un grupo al que no perteneces.', [], 403);
        }

        $esParteDelEquipo = $exposicion->equipo->integrantes->contains('id_usuario', $request->id_usuario);
        
        if ($esParteDelEquipo) {
            return $this->sendError('No puedes evaluar a tu propio equipo.', [], 403);
        }

        $criteriosRequeridos = $exposicion->rubrica->criterios->pluck('id_criterios')->toArray();
        
        $criteriosEnviados = collect($request->calificaciones)->pluck('id_criterio')->toArray();

        $faltantes = array_diff($criteriosRequeridos, $criteriosEnviados);
        
        if (count($faltantes) > 0) {
            return $this->sendError('La evaluación debe incluir todos los criterios de la rúbrica.', [], 400);
        }

        try {
            $evaluacion = DB::transaction(function () use ($request) {
                $nuevaEval = Evaluacion::create([
                    'id_expo'       => $request->id_expo,
                    'id_usuario'    => $request->id_usuario,
                    'observaciones' => $request->observaciones,
                    'fecha'         => now()
                ]);

                foreach ($request->calificaciones as $item) {
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

    public function show($id)
    {
        $evaluacion = Evaluacion::with([
            'exposicion.equipo',
            'usuario',
            'detalles.criterio'
        ])->find($id);

        if (!$evaluacion) {
            return $this->sendError('Evaluación no encontrada.');
        }

        return $this->sendResponse($evaluacion, 'Detalle de evaluación obtenido.');
    }
}