<?php

declare(strict_types=1);

namespace App\Adapters\Repositories;

use App\Entities\Foundation;
use App\UseCases\FoundationRepositoryInterface;
use PDO;

/**
 * Adaptador que guarda as fundações no SQLite usando PDO.
 * Todas as consultas usam prepared statements.
 */
final class SqliteFoundationRepository implements FoundationRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function save(Foundation $foundation): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO foundations (name, cnpj, email, phone, supported_institution)
             VALUES (:name, :cnpj, :email, :phone, :supported_institution)'
        );
        $statement->execute($this->toParameters($foundation));

        $foundation->setId((int) $this->pdo->lastInsertId());
    }

    public function update(Foundation $foundation): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE foundations
                SET name = :name,
                    cnpj = :cnpj,
                    email = :email,
                    phone = :phone,
                    supported_institution = :supported_institution
              WHERE id = :id'
        );
        $statement->execute($this->toParameters($foundation) + ['id' => $foundation->id()]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM foundations WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function findById(int $id): ?Foundation
    {
        $statement = $this->pdo->prepare('SELECT * FROM foundations WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->toEntity($row);
    }

    public function findByCnpj(string $cnpj): ?Foundation
    {
        $statement = $this->pdo->prepare('SELECT * FROM foundations WHERE cnpj = :cnpj');
        $statement->execute(['cnpj' => $cnpj]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->toEntity($row);
    }

    public function findAll(): array
    {
        $rows = $this->pdo->query('SELECT * FROM foundations ORDER BY name COLLATE NOCASE')->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn (array $row): Foundation => $this->toEntity($row), $rows);
    }

    /**
     * Entidade virqcolunas da tabela.
     */
    private function toParameters(Foundation $foundation): array
    {
        return [
            'name' => $foundation->name(),
            'cnpj' => $foundation->cnpj(),
            'email' => $foundation->email(),
            'phone' => $foundation->phone(),
            'supported_institution' => $foundation->supportedInstitution(),
        ];
    }

    /**
     * Linha da tabela vira entidade.
     */
    private function toEntity(array $row): Foundation
    {
        return new Foundation(
            $row['name'],
            $row['cnpj'],
            $row['email'],
            $row['phone'],
            $row['supported_institution'],
            $row['id'],
        );
    }
}
