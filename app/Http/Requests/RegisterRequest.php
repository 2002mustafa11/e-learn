<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
        $role = $this->input('role', 'student');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone'    => ['required', 'regex:/^01[0125][0-9]{8}$/'],
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:student,teacher,parent,admin',
        ];

        switch ($role) {
            case 'student':
                $rules['grade_level'] = 'required|string|max:50';
                $rules['birth_date'] = 'nullable|date';
                break;

            case 'teacher':
                $rules['specialization'] = 'required|string|max:100';
                $rules['experience_years'] = 'nullable|integer|min:0';
                break;

            case 'parent':
                $rules['relation_type'] = 'required|string|max:50';
                $rules['job'] = 'nullable|string|max:100';
                break;
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
