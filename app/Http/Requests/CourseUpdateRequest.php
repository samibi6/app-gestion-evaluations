<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseUpdateRequest extends FormRequest
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
        $courseId = $this->route('course')->id; 
        return [
            'name' => 'required|max:100',
            'code' => "required|unique:courses,code,{$courseId}|max:25", //pour exclure le code du cours qu'on modifie actuellement de la règle 'unique'
            'year' => 'required|integer|between:1,5',
            'section' => 'required|max:100',
            'user' => 'required|max:100', //faudra ptetre retirer le required, un cours doit sûrement pouvoir être créé sans prof
        ];
    }
}
