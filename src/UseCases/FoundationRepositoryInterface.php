<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Entities\Foundation;

/**
 * A interface fica aqui, na camada de casos de uso, e a implementação com SQLite
 * fica na camada de adaptadores (SqliteFoundationRepository).
 */
interface FoundationRepositoryInterface
{
    /**
     * Insere uma nova fundação e preenche o id gerado.
     */
    public function save(Foundation $foundation): void;

    public function update(Foundation $foundation): void;

    public function delete(int $id): void;

    public function findById(int $id): ?Foundation;

    /**
     * Recebe o CNPJ só com dígitos.
     */
    public function findByCnpj(string $cnpj): ?Foundation;

    public function findAll(): array;
}
