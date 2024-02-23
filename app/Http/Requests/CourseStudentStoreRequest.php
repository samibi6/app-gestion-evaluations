<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CourseStudentStoreRequest extends FormRequest
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
        $data = $this['student'];
        $data = array_filter($data);
        $this['student'] = $data;
        return [
            'student' => 'required|array',
            'student.*' => 'boolean',
            'section' => 'required|integer',
            'course' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'student.required' => 'Vous devez sélectionner au moins 1 étudiant à ajouter au cours.',
            'course.required' => 'Vous devez sélectionner un cours.'
        ];
    }
}
