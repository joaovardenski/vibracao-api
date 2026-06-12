<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(
                trim($this->email ?? '')
            ),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:150',
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',

                Rule::unique('admins', 'email')
                    ->ignore(
                        $this->route('admin')?->id
                    ),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => 'Informe o nome.',

            'name.min' => 'O nome deve possuir ao menos 3 caracteres.',

            'email.required' => 'Informe o email.',

            'email.email' => 'Email inválido.',

            'email.unique' => 'Já existe um administrador com este email.',

            'password.min' => 'A senha deve possuir ao menos 8 caracteres.',
        ];
    }

    protected function failedValidation(
        Validator $validator
    ): void {

        throw new HttpResponseException(
            response()->json([

                'success' => false,

                'message' => 'Erro de validação.',

                'errors' => $validator->errors(),

            ], 422)

        );
    }
}
