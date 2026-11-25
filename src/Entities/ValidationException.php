<?php

declare(strict_types=1);

namespace App\Entities;

use DomainException;

/**
 * Lançada quando os dados da fundação não são válidos.
 * Carrega os erros por campo para o formulário exibir cada um no lugar certo.
 */
final class ValidationException extends DomainException
{
    public function __construct(private readonly array $errors)
    {
        parent::__construct('Os dados da fundação são inválidos.');
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
