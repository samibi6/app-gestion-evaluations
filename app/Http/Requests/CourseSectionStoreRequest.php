<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CourseSectionStoreRequest extends FormRequest
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
        $data = $this['course'];
        $data = array_filter($data);
        $this['course'] = $data;
        return [
            'course' => 'required|array',
            'course.*' => 'boolean',
            'section' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'course.required' => 'Vous devez sélectionner au moins 1 cours à ajouter à la section.',
            'section.required' => 'Vous devez sélectionner une section.'
        ];
    }
}
