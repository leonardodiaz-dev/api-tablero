<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
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
            'title'=>'required|max:255',
            'description'=>'nullable',
            'status'=>'required',
            'column_id' => 'required|integer|exists:columns,id',
            'subtasks'=>'nullable|array',
            'subtasks.*.title'=>'required|max:255',
            'subtasks.*.isCompleted'=>'required'
        ];
    }
      protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Datos inválidos.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
