<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambiar si necesitas lógica de autorización
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|integer',
            'request_year' => 'required|integer',
            'request_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'days_requested' => 'required|integer|max:15',
            'vacation_type_id' => 'required|integer',
            'comments' => 'required|string|max:255',
        ];
    }
}
