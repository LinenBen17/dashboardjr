<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use App\Models\Installments;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use MoonShine\MoonShineRequest;
use MoonShine\Http\Controllers\MoonShineController;
use Symfony\Component\HttpFoundation\Response;

final class LoanController extends MoonShineController
{
    public function store(MoonShineRequest $request): Response
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|numeric',
            'start_date' => 'required|date',
            'amount_loan' => 'required|numeric',
            'no_share' => 'required|numeric',
            'amount_share' => 'required|numeric',
            'comments' => 'required',
        ]);

        if($validator->fails()){
            Session::flash('failSave', 'Verifica que todos los campos estén correctamente llenos.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $loan = Loan::create([
                'employee_id' => $request->employee_id,
                'start_date' => $request->start_date,
                'amount_loan' => $request->amount_loan,
                'no_share' => $request->no_share,
                'amount_share' => $request->amount_share,
                'comments' => $request->comments,
            ]);

            $loan_id = $loan->id;

            // Fecha de ingreso del préstamo
            $startDate = Carbon::create($request->start_date);

            // Iteramos para crear las cuotas
            for ($i = 1; $i <= $request->no_share; $i++) {
                $billingDate = $startDate;

                if ($billingDate->day <= 15) {
                    Installments::create([
                        'loan_id' => $loan_id,
                        'no_installment' => $i,
                        'amount' => $request->amount_loan / $request->no_share,
                        'billing_date' => $billingDate->addDays(15 - $billingDate->day),
                    ]);
                }elseif ($billingDate->day > 15) {
                    Installments::create([
                        'loan_id' => $loan_id,
                        'no_installment' => $i,
                        'amount' => $request->amount_loan / $request->no_share,
                        'billing_date' => $billingDate->endOfMonth(),
                    ]);
                }

                $billingDate->addDays(1);
            }

            Session::flash('successSave', 'Registro guardado con éxito. Número de Prestámos ' . $loan_id);
            
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Código de error para violaciones de restricciones de integridad
                Session::flash('failSave', 'Verifica que todos los campos estén correctamente llenos.');
            } else {
                Session::flash('failSave', 'Error al intentar guardar la información. ' . $e);
            }
            return redirect()->back()->withInput();
        }

        return back();
    }
    public function delete(MoonShineRequest $request, $id): Response
    {
        
        try {
            $loan = Loan::find($id);
            
            if ($loan) {
                // Eliminar las cuotas asociadas
                Installments::where('loan_id', $loan->id)->delete();

                // Eliminar el préstamo
                $loan->delete();
                
                Session::flash('success', 'Préstamo eliminado con éxito.');
            } else {
                Session::flash('fail', 'Préstamo no encontrado.');
            }
        } catch (\Exception $e) {
            Session::flash('fail', 'Error al intentar eliminar el préstamo: ' . $e->getMessage());
        }
        return back(); // Redirigir a la lista de préstamos
    }
}
