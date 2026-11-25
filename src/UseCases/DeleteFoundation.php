<?php

declare(strict_types=1);

namespace App\UseCases;

final class DeleteFoundation
{
    public function __construct(private readonly FoundationRepositoryInterface $repository)
    {
    }

    /**
     * @throws FoundationNotFoundException
     */
    public function execute(int $id): void
    {
        if ($this->repository->findById($id) === null) {
            throw new FoundationNotFoundException();
        }

        $this->repository->delete($id);
    }
}
