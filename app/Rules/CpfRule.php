<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfRule implements ValidationRule
{
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {

        $cpf = preg_replace(
            '/[^0-9]/',
            '',
            $value
        );

        if (
            strlen($cpf) !== 11 ||
            preg_match('/(\d)\1{10}/', $cpf)
        ) {
            $fail('CPF inválido.');

            return;
        }

        for ($digit = 9; $digit < 11; $digit++) {

            $sum = 0;

            for ($i = 0; $i < $digit; $i++) {
                $sum +=
                    $cpf[$i]
                    *
                    (($digit + 1) - $i);
            }

            $result =
                (($sum * 10) % 11);

            if ($result === 10) {
                $result = 0;
            }

            if ((int) $cpf[$digit] !== $result) {
                $fail('CPF inválido.');

                return;
            }
        }
    }
}
