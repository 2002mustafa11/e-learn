<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $baseRules = $this->baseRules();
        $roleRules = $this->roleSpecificRules($this->get('role', 'student'));

        return array_merge($baseRules, $roleRules);
    }

    protected function baseRules(): array
    {
        return [
            'user.name' => 'nullable|string|max:255',
            'user.email' => 'nullable|email|unique:users,email,' . $this->route('id'),
            'user.phone' => ['nullable', 'regex:/^01[0125][0-9]{8}$/'],
            'user.password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:student,teacher,parent,admin',
        ];
    }

    protected function roleSpecificRules(string $role): array
    {
        return match ($role) {
            'student' => [
                'grade_level' => 'nullable|string|max:50',
                'birth_date' => 'nullable|date',
            ],
            'teacher' => [
                'specialization' => 'nullable|string|max:100',
                'experience_years' => 'nullable|integer|min:0',
            ],
            'parent' => [
                'relation_type' => 'nullable|string|max:50',
                'job' => 'nullable|string|max:100',
            ],
            default => [],
        };
    }

}
