<?php

namespace App\Http\Requests\Admin;

use App\Rules\CpfRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateManualRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([

            'cpf' => preg_replace(
                '/\D/',
                '',
                $this->cpf ?? ''
            ),

            'phone' => preg_replace(
                '/\D/',
                '',
                $this->phone ?? ''
            ),

            'email' => strtolower(
                trim(
                    $this->email ?? ''
                )
            ),

        ]);
    }

    public function rules(): array
    {
        return [

            'full_name' => [
                'required',
                'string',
                'min:5',
                'max:150',
            ],

            'cpf' => [
                'required',
                'string',
                new CpfRule,
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'regex:/^\d{10,11}$/',
            ],

            'city' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'parish' => [
                'required',
                'string',
                'min:2',
                'max:150',
            ],

            'emergency_contact' => [
                'nullable',
                'string',
                'max:255',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'full_name.required' => 'Informe o nome completo.',

            'full_name.min' => 'O nome deve ter ao menos 5 caracteres.',

            'full_name.max' => 'O nome deve ter no máximo 150 caracteres.',

            'cpf.required' => 'Informe o CPF.',

            'cpf.cpf' => 'CPF inválido.',

            'email.required' => 'Informe o email.',

            'email.email' => 'Email inválido.',

            'email.max' => 'Email muito grande.',

            'phone.required' => 'Informe o telefone.',

            'phone.regex' => 'Telefone inválido.',

            'city.required' => 'Informe a cidade.',

            'city.max' => 'Cidade muito grande.',

            'parish.required' => 'Informe a paróquia.',

            'parish.max' => 'Paróquia muito grande.',
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
