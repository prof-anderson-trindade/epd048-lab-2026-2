<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecadoRequest extends FormRequest
{
    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'texto' => ['required', 'string', 'max:280'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'texto.required' => 'O recado não pode ficar em branco.',
            'texto.max' => 'O recado pode ter no máximo :max caracteres.',
        ];
    }
}
