<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Entities\Foundation;

final class FindFoundationById
{
    public function __construct(private readonly FoundationRepositoryInterface $repository)
    {
    }

    /**
     * @throws FoundationNotFoundException
     */
    public function execute(int $id): Foundation
    {
        $foundation = $this->repository->findById($id);

        if ($foundation === null) {
            throw new FoundationNotFoundException();
        }

        return $foundation;
    }
}
