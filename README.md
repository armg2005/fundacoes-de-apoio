# Cadastro de Fundações de Apoio (arquitetura limpa)

Desafio técnico do processo seletivo de Estágio de TI (Conveniar).

Aplicação web em **PHP 8.2+ puro (sem framework)**, orientada a objetos e organizada com
**arquitetura limpa**, para cadastrar, consultar, alterar e excluir Fundações de Apoio.

## Funcionalidades

- Cadastro com nome, CNPJ, e-mail, telefone e instituição apoiada, com mensagem de sucesso.
- Pesquisa pelo CNPJ (com ou sem máscara): o resultado aparece na própria tabela. Se não existir,
  mostra **"Fundação não encontrada"**.
- Listagem de todas as fundações com ações de editar e excluir.
- Não permite duas fundações com o mesmo CNPJ (validado no caso de uso e com `UNIQUE` no banco).
- Validação do CNPJ pelos dígitos verificadores, e-mail e telefone com DDD.
- Máscaras de CNPJ e telefone nos campos e layout responsivo.

## Tecnologias

| | |
| --- | --- |
| Linguagem | PHP 8.2+ |
| Banco de dados | SQLite (relacional, via PDO) |
| Gerenciador de pacotes | Composer (autoload PSR-4) |
| Testes | PHPUnit 11 |
| Front-end | HTML, CSS e JavaScript puros |
| Ambiente | Docker (opcional) |

## Como executar

### Opção 1: Docker

```bash
docker compose up --build
```

Acesse http://localhost:8080. O banco fica num volume do Docker e não se perde ao reiniciar.

### Opção 2: local

Requisitos: PHP 8.2 ou superior com a extensão `pdo_sqlite`, e o Composer.

```bash
composer install
composer start
```

Acesse http://localhost:8080. O banco é criado automaticamente em `database/foundations.sqlite`
na primeira requisição (schema em `database/schema.sql`).

> CNPJs válidos para testar: `11.222.333/0001-81` e `11.444.777/0001-61`.

## Testes

```bash
composer test
```

São testes unitários das regras principais, sem banco de dados:

- `tests/Unit/Entities/CnpjValidatorTest.php`: validação do CNPJ.
- `tests/Unit/UseCases/CreateFoundationTest.php`: cadastro, CNPJ duplicado e dados inválidos.
- `tests/Unit/UseCases/FindFoundationByCnpjTest.php`: pesquisa por CNPJ e "Fundação não encontrada".

Os casos de uso usam um repositório em memória (`tests/Support/InMemoryFoundationRepository.php`).

## Estrutura

```
├── public/              ponto de entrada (index.php), CSS e JS
├── templates/           views em PHP
├── database/schema.sql  criação da tabela
├── src/
│   ├── Entities/        Foundation, CnpjValidator, ValidationException
│   ├── UseCases/        casos de uso, FoundationDTO, FoundationRepositoryInterface
│   └── Adapters/
│       ├── Controllers/     FoundationController
│       ├── Repositories/    SqliteFoundationRepository
│       └── Views/           View
├── tests/
└── docs/ARQUITETURA.md  decisões de arquitetura explicadas
```

As decisões de arquitetura (o que é cada camada, por que a interface do repositório fica nos casos
de uso, como funciona a injeção de dependência etc.) estão em [docs/ARQUITETURA.md](docs/ARQUITETURA.md).

## Rotas

| Método | URL | Ação |
| --- | --- | --- |
| GET | `/?action=list` | lista as fundações (página inicial) |
| GET | `/?action=search&cnpj=...` | pesquisa por CNPJ |
| GET | `/?action=create` | formulário de cadastro |
| POST | `/?action=store` | cadastra |
| GET | `/?action=edit&id=...` | formulário de edição |
| POST | `/?action=update&id=...` | altera |
| POST | `/?action=delete&id=...` | exclui |
