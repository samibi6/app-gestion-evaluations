<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdjournmentStoreRequest extends FormRequest
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
    public function rules()
    {
        return [
            'is_determining' => 'boolean',
            'is_other' => 'boolean',
            
            'adjournment_exam_date' => ['required', 'date', 'before_or_equal:today'], 
            
            'adjournment_blunder_1' => 'boolean',
            'adjournment_blunder_1_justification' => [
                Rule::requiredIf(function () {
                    return $this->input('adjournment_blunder_1') == true;
                }),
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if (!$this->input('adjournment_blunder_1') && $value) {
                        $fail('The checkbox for "Adjournment Blunder 1" must be checked if the justification is filled.');
                    }
                },
            ], 
            
            'adjournment_blunder_2' => 'boolean',
        ];
    }
}


