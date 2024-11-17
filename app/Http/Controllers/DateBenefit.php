<?php

namespace App\Http\Controllers;

use App\Models\DateBenefit as ModelsDateBenefit;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DateBenefit extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::debug("message");
        //// Validar los datos
        $data = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);
        
        // Obtiene las fechas de la solicitud
        $dateFrom = Carbon::parse($data['date_from']);
        $dateTo = Carbon::parse($data['date_to']);

        // Verifica la diferencia de días
        if ($dateFrom->diffInDays($dateTo) < 364 || $dateFrom->diffInDays($dateTo) > 365) {
            Session::flash('failSave', 'Asegurese de establecer un rango de 365 días válidos.');

            return redirect()->back()->withInput();

        }else {
            try {
                ModelsDateBenefit::create([
                    'benefit_id' => 1,
                    'date_from' => $data['date_from'],
                    'date_to' => $data['date_to'],
                ]);
                Session::flash('successSave', 'Rango de fecha guardado exitosamente.');

                return redirect()->back();
            } catch (QueryException $e) {
                if ($e->getCode() === '23000') { // Código de error para violaciones de restricciones de integridad
                    Session::flash('failSave', 'Rango de fecha ya existente.');
                } else {
                    Session::flash('failSave', 'Error al intentar guardar la información.');
                }
                return redirect()->back()->withInput();
            }
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
