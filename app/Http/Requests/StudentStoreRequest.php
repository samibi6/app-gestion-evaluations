<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StudentStoreRequest extends FormRequest
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
            'last_name' => 'required|max:100',
            'first_name' => 'required|max:100',
            'email' => 'required|string|lowercase|email|max:255',
            'section' => 'required|max:100',
        ];
    }
}
