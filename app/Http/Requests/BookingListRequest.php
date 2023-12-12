<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
class BookingListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
            'sortBy' => ['nullable', Rule::in(['parking_slot_id'])],
        ];
    }

    public function messages(): array
    {
        return [
            'dateFrom.required' => 'The dateFrom field is required.',
            'dateFrom.date' => 'The dateFrom must be a valid date.',
            'dateTo.required' => 'The dateTo field is required.',
            'dateTo.date' => 'The dateTo must be a valid date.',
            'dateTo.after_or_equal' => 'The dateTo must be a date after or equal to dateFrom.',
            'sortBy.in' => "sortBy parameter accepts only 'parking_slot_id'",
        ];
    }

    
}
