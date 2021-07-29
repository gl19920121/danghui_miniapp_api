<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListJobGet extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['nullable', 'numeric'],
            'location' => ['nullable', 'numeric'],
            'salary' => ['nullable', 'numeric'],
            'duration' => ['nullable', 'numeric'],
            'experience' => ['nullable', 'numeric'],
            'education' => ['nullable', 'numeric'],
        ];
    }
}
