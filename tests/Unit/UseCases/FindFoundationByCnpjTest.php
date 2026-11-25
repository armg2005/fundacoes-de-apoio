<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases;

use App\UseCases\CreateFoundation;
use App\UseCases\FindFoundationByCnpj;
use App\UseCases\FoundationDTO;
use App\UseCases\FoundationNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryFoundationRepository;

final class FindFoundationByCnpjTest extends TestCase
{
    public function testFindsFoundationByCnpj(): void
    {
        $repository = new InMemoryFoundationRepository();
        (new CreateFoundation($repository))->execute(new FoundationDTO(
            'Fundação de Apoio à Pesquisa',
            '11.222.333/0001-81',
            'contato@fundacao.org.br',
            '(61) 3333-4444',
            'Universidade de Brasília',
        ));

        $foundation = (new FindFoundationByCnpj($repository))->execute('11222333000181');

        $this->assertSame('Fundação de Apoio à Pesquisa', $foundation->name());
    }

    public function testShowsMessageWhenFoundationIsNotFound(): void
    {
        $findFoundationByCnpj = new FindFoundationByCnpj(new InMemoryFoundationRepository());

        $this->expectException(FoundationNotFoundException::class);
        $this->expectExceptionMessage('Fundação não encontrada');

        $findFoundationByCnpj->execute('11.222.333/0001-81');
    }
}
