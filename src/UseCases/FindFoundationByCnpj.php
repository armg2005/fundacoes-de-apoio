<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Entities\Foundation;

final class FindFoundationByCnpj
{
    public function __construct(private readonly FoundationRepositoryInterface $repository)
    {
    }

    /**
     * Aceita o CNPJ com ou sem máscara.
     *
     * @throws FoundationNotFoundException
     */
    public function execute(string $cnpj): Foundation
    {
        $foundation = $this->repository->findByCnpj(preg_replace('/\D/', '', $cnpj));

        if ($foundation === null) {
            throw new FoundationNotFoundException();
        }

        return $foundation;
    }
}
