<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Entities\Foundation;
use App\Entities\ValidationException;

final class UpdateFoundation
{
    public function __construct(private readonly FoundationRepositoryInterface $repository)
    {
    }

    /**
     * @throws FoundationNotFoundException
     * @throws ValidationException dados inválidos ou CNPJ de outra fundação
     */
    public function execute(int $id, FoundationDTO $dto): Foundation
    {
        $foundation = $this->repository->findById($id);
        if ($foundation === null) {
            throw new FoundationNotFoundException();
        }

        $foundation->update(
            $dto->name,
            $dto->cnpj,
            $dto->email,
            $dto->phone,
            $dto->supportedInstitution,
        );

        // O CNPJ pode ser alterado, desde que não pertença a outra fundação.
        $foundationWithSameCnpj = $this->repository->findByCnpj($foundation->cnpj());
        if ($foundationWithSameCnpj !== null && $foundationWithSameCnpj->id() !== $id) {
            throw new ValidationException(['cnpj' => 'Já existe uma fundação cadastrada com este CNPJ.']);
        }

        $this->repository->update($foundation);

        return $foundation;
    }
}
