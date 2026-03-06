<?php

namespace App\Http\Controllers;

use App\Models\Rubrica;
use App\Models\Criterio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RubricaController extends Controller
{
    /**
     * Listar todas las rúbricas con sus criterios
     */
    public function index()
    {
        $rubricas = Rubrica::with('criterios')->get();
        return $this->sendResponse($rubricas, 'Rúbricas recuperadas.');
    }

    /**
     * Crear una rúbrica junto con sus criterios (Todo en uno)
     */
    public function store(Request $request)
    {
        $request->validate([
            'rubrica' => 'required|string|max:255',
            'criterios' => 'required|array|min:1',
            'criterios.*.descripcion' => 'required|string',
            'criterios.*.porcentaje' => 'required|numeric|min:1|max:100',
        ]);

        try {
            $nuevaRubrica = DB::transaction(function () use ($request) {
                // 1. Crear la cabecera de la rúbrica
                $rubrica = Rubrica::create([
                    'rubrica' => $request->rubrica
                ]);

                // 2. Crear cada criterio asociado
                foreach ($request->criterios as $criterio) {
                    $rubrica->criterios()->create([
                        'descripcion' => $criterio['descripcion'],
                        'porcentaje'  => $criterio['porcentaje']
                    ]);
                }

                return $rubrica->load('criterios');
            });

            return $this->sendResponse($nuevaRubrica, 'Rúbrica y criterios creados con éxito.', 201);

        } catch (\Exception $e) {
            return $this->sendError('Error al crear la rúbrica.', [$e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una rúbrica específica
     */
    public function show($id)
    {
        $rubrica = Rubrica::with('criterios')->find($id);

        if (!$rubrica) {
            return $this->sendError('Rúbrica no encontrada.');
        }

        return $this->sendResponse($rubrica, 'Detalles de la rúbrica obtenidos.');
    }

    /**
     * Eliminar rúbrica (Borrará criterios por cascada)
     */
    public function destroy($id)
    {
        $rubrica = Rubrica::find($id);
        if (!$rubrica) return $this->sendError('Rúbrica no encontrada.');

        $rubrica->delete();
        return $this->sendResponse([], 'Rúbrica eliminada correctamente.');
    }
}