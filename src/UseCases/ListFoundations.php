<?php

declare(strict_types=1);

namespace App\UseCases;

final class ListFoundations
{
    public function __construct(private readonly FoundationRepositoryInterface $repository)
    {
    }

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
