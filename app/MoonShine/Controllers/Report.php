<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use MoonShine\MoonShineRequest;
use MoonShine\Http\Controllers\MoonShineController;
use MoonShine\Pages\ViewPage; // Import the correct ViewPage
use Symfony\Component\HttpFoundation\Response;

final class Report extends MoonShineController
{
    public function __invoke(MoonShineRequest $request): Response // Cambiar el tipo de retorno a Response
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $validator->after(function ($validator) use ($request) {
            try {
                $from = Carbon::parse($request->from);
                $to = Carbon::parse($request->to);
                $ultimoDiaMes = $from->copy()->endOfMonth()->day;

                $rango1 = $from->day === 1 && $to->day === 15 && $from->isSameMonth($to);
                $rango2 = $from->day === 16 && $to->day === $ultimoDiaMes && $from->isSameMonth($to);

                if (!($rango1 || $rango2)) {
                    $validator->errors()->add('from', 'Las fechas deben estar en los rangos 1-15 o 16-último día del mes.');
                    $validator->errors()->add('to', 'Las fechas deben estar en los rangos 1-15 o 16-último día del mes.');
                }

            } catch (\Exception $e) {
                $validator->errors()->add('from', 'Formato de fecha inválido.');
                $validator->errors()->add('to', 'Formato de fecha inválido.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput(); // Esto ahora es válido
        }

        $from = Carbon::parse($request->from);
        $from = $from->format('d-m-Y');
        
        $to = Carbon::parse($request->to);
        $to = $to->format('d-m-Y');

        // Devuelve un ViewPage
        return response()->view('reports.payroll', [
            "from" => $from,
            "to" => $to,
        ]);
    }
}