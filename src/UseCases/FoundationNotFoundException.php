<?php

declare(strict_types=1);

namespace App\UseCases;

use RuntimeException;

final class FoundationNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Fundação não encontrada');
    }
}
