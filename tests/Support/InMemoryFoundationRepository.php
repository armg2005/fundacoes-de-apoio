<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Entities\Foundation;
use App\UseCases\FoundationRepositoryInterface;

/**
 * Repositório em memória para testar os casos de uso sem banco de dados.
 * Só é possível porque os casos de uso dependem da interface, não do SQLite.
 */
final class InMemoryFoundationRepository implements FoundationRepositoryInterface
{
    private array $foundations = [];
    private int $nextId = 1;

    public function save(Foundation $foundation): void
    {
        $foundation->setId($this->nextId++);
        $this->foundations[$foundation->id()] = $foundation;
    }

    public function update(Foundation $foundation): void
    {
        $this->foundations[$foundation->id()] = $foundation;
    }

    public function delete(int $id): void
    {
        unset($this->foundations[$id]);
    }

    public function findById(int $id): ?Foundation
    {
        return $this->foundations[$id] ?? null;
    }

    public function findByCnpj(string $cnpj): ?Foundation
    {
        foreach ($this->foundations as $foundation) {
            if ($foundation->cnpj() === $cnpj) {
                return $foundation;
            }
        }

        return null;
    }

    public function findAll(): array
    {
        return array_values($this->foundations);
    }
}
