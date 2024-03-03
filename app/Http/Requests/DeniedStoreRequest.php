<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeniedStoreRequest extends FormRequest
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
            
            'denied_exam_date' => ['required', 'date', 'before_or_equal:today'], 
            
            'denied_blunder_1' => 'boolean',
            'denied_blunder_1_justification' => [
                Rule::requiredIf(function () {
                    return $this->input('denied_blunder_1') == true;
                }),
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if (!$this->input('denied_blunder_1') && $value) {
                        $fail('The checkbox for "Denied Blunder 1" must be checked if the justification is filled.');
                    }
                },
            ], 
            
            'denied_blunder_2' => 'boolean',
            'denied_blunder_2_justification' => [
                Rule::requiredIf(function () {
                    return $this->input('denied_blunder_2') == true;
                }),
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if (!$this->input('denied_blunder_2') && $value) {
                        $fail('The checkbox for "Denied Blunder 2" must be checked if the justification is filled.');
                    }
                },
            ], 
            'denied_blunder_3' => 'boolean',
            
            'denied_blunder_4' => 'boolean',
            
            'denied_blunder_5' => 'boolean',
            
            'denied_justification_global' => 'required','string','max:1000',
        ];
    }
}