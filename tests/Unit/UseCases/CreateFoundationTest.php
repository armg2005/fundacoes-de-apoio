<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases;

use App\Entities\ValidationException;
use App\UseCases\CreateFoundation;
use App\UseCases\FoundationDTO;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryFoundationRepository;

final class CreateFoundationTest extends TestCase
{
    public function testCreatesFoundation(): void
    {
        $repository = new InMemoryFoundationRepository();
        $createFoundation = new CreateFoundation($repository);

        $createFoundation->execute($this->makeDto('11.222.333/0001-81'));

        $this->assertCount(1, $repository->findAll());
    }

    public function testDoesNotAllowDuplicatedCnpj(): void
    {
        $createFoundation = new CreateFoundation(new InMemoryFoundationRepository());
        $createFoundation->execute($this->makeDto('11.222.333/0001-81'));

        $this->expectException(ValidationException::class);

        $createFoundation->execute($this->makeDto('11222333000181'));
    }

    public function testDoesNotAllowInvalidData(): void
    {
        $createFoundation = new CreateFoundation(new InMemoryFoundationRepository());

        try {
            $createFoundation->execute(new FoundationDTO('', '123', 'email-invalido', '1', ''));
            $this->fail('Deveria ter lançado ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertCount(5, $exception->errors());
        }
    }

    private function makeDto(string $cnpj): FoundationDTO
    {
        return new FoundationDTO(
            'Fundação de Apoio à Pesquisa',
            $cnpj,
            'contato@fundacao.org.br',
            '(61) 3333-4444',
            'Universidade de Brasília',
        );
    }
}
