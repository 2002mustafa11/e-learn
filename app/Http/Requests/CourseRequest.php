<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
            // 'user_id'       => 'required|uuid|exists:users,id',
            'title'         => 'required|string|max:255',
            'duration'      => 'required|string|max:100',
            'enrolled'      => 'nullable|integer|min:0',
            'lectures'      => 'nullable|integer|min:0',
            'skill_level'   => 'nullable|string',
            'language'      => 'nullable|string',
            'fee'           => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'learning_skill'=> 'nullable|string',
            'categories_id'     => 'nullable|array',
            'categories_id.*'   => 'exists:categories,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ];
    }
}
