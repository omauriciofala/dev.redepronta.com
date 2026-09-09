<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfCnpjRule implements ValidationRule
{
    public function __construct(
        protected ?string $personType = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $clean = preg_replace('/\D/', '', (string) $value);
        $len = strlen($clean);

        if ($len === 11) {
            if ($this->personType === 'legal') {
                $fail('Um CNPJ com 14 dígitos é esperado para Pessoa Jurídica.');
                return;
            }
            if (! $this->isValidCpf($clean)) {
                $fail('O CPF informado é inválido.');
            }
            return;
        }

        if ($len === 14) {
            if ($this->personType === 'individual') {
                $fail('Um CPF com 11 dígitos é esperado para Pessoa Física.');
                return;
            }
            if (! $this->isValidCnpj($clean)) {
                $fail('O CNPJ informado é inválido.');
            }
            return;
        }

        $fail('O documento deve ser um CPF válido (11 dígitos) ou CNPJ válido (14 dígitos).');
    }

    protected function isValidCpf(string $cpf): bool
    {
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ((int) $cpf[$c] !== $d) {
                return false;
            }
        }

        return true;
    }

    protected function isValidCnpj(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum1 = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum1 += (int) $cnpj[$i] * $weights1[$i];
        }
        $rest1 = $sum1 % 11;
        $d1 = ($rest1 < 2) ? 0 : 11 - $rest1;

        if ((int) $cnpj[12] !== $d1) {
            return false;
        }

        $sum2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum2 += (int) $cnpj[$i] * $weights2[$i];
        }
        $rest2 = $sum2 % 11;
        $d2 = ($rest2 < 2) ? 0 : 11 - $rest2;

        return (int) $cnpj[13] === $d2;
    }
}
