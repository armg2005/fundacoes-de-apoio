<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Entities\Foundation;
use App\Entities\ValidationException;

final class CreateFoundation
{
    public function __construct(private readonly FoundationRepositoryInterface $repository)
    {
    }

    /**
     * @throws ValidationException dados inválidos ou CNPJ já cadastrado
     */
    public function execute(FoundationDTO $dto): Foundation
    {
        $foundation = new Foundation(
            $dto->name,
            $dto->cnpj,
            $dto->email,
            $dto->phone,
            $dto->supportedInstitution,
        );

        if ($this->repository->findByCnpj($foundation->cnpj()) !== null) {
            throw new ValidationException(['cnpj' => 'Já existe uma fundação cadastrada com este CNPJ.']);
        }

        $this->repository->save($foundation);

        return $foundation;
    }
}
