<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
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
            'course_id'     => 'required|exists:courses,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'duration'      => 'required|integer|min:0',
            'order'         => 'nullable|integer|min:0',
            'is_free'       => 'boolean',
            'is_published'  => 'boolean',
            'content_type'  => 'nullable|string',
        ];
    }
}
