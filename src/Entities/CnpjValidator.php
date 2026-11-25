<?php

declare(strict_types=1);

namespace App\Entities;

/**
 * Regra de validação do CNPJ: 14 dígitos e dígitos verificadores corretos.
 */
final class CnpjValidator
{

    public static function isValid(string $cnpj): bool
    {
        $digits = preg_replace('/\D/', '', $cnpj);

        if (strlen($digits) !== 14) {
            return false;
        }

        // Sequências como 00000000000000 passam no cálculo, mas não são CNPJs reais.
        if (preg_match('/^(\d)\1{13}$/', $digits) === 1) {
            return false;
        }

        $firstCheckDigit = self::calculateCheckDigit(substr($digits, 0, 12));
        $secondCheckDigit = self::calculateCheckDigit(substr($digits, 0, 12) . $firstCheckDigit);

        return $digits[12] === (string) $firstCheckDigit
            && $digits[13] === (string) $secondCheckDigit;
    }

    /**
     * Cálculo do dígito verificador com pesos de 2 a 9,
     * aplicados da direita para a esquerda.
     */
    private static function calculateCheckDigit(string $base): int
    {
        $sum = 0;
        $weight = 2;

        for ($i = strlen($base) - 1; $i >= 0; $i--) {
            $sum += (int) $base[$i] * $weight;

            $weight++;
            if ($weight > 9) {
                $weight = 2;
            }
        }

        $remainder = $sum % 11;

        if ($remainder < 2) {
            return 0;
        }

        return 11 - $remainder;
    }
}
