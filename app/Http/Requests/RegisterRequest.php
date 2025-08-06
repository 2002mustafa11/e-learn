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
        $role = $this->get('role', 'student'); 

        return array_merge($this->baseRules(), $this->roleSpecificRules($role));
    }

    protected function baseRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'regex:/^01[0125][0-9]{8}$/'],
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:student,teacher,parent,admin',
        ];
    }

    protected function roleSpecificRules(string $role): array
    {
        return match ($role) {
            'student' => [
                'grade_level' => 'required|string|max:50',
                'birth_date' => 'nullable|date',
            ],
            'teacher' => [
                'specialization' => 'required|string|max:100',
                'experience_years' => 'nullable|integer|min:0',
            ],
            'parent' => [
                'relation_type' => 'required|string|max:50',
                'job' => 'nullable|string|max:100',
            ],
            default => [],
        };
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
