# Decisões de arquitetura (arquitetura limpa)

Este documento explica **como** o projeto foi organizado e **por quê**.
A ideia foi aplicar a arquitetura limpa (Clean Architecture) da forma mais simples possível:
o problema é um cadastro, então a estrutura precisa ajudar a ler o código, e não atrapalhar.

## 1. A regra principal: as dependências apontam para dentro

A arquitetura limpa organiza o código em círculos. Os de dentro guardam as regras de negócio;
os de fora guardam os detalhes técnicos (banco, HTTP, HTML).

```
┌─────────────────────────────────────────────────────────┐
│  Frameworks e drivers: public/index.php, templates/,    │
│  database/ (SQLite), CSS e JS                           │
│   ┌─────────────────────────────────────────────────┐   │
│   │  Adapters: Controllers, Repositories, Views     │   │
│   │   ┌─────────────────────────────────────────┐   │   │
│   │   │  UseCases: CreateFoundation, ...        │   │   │
│   │   │   ┌─────────────────────────────────┐   │   │   │
│   │   │   │  Entities: Foundation,          │   │   │   │
│   │   │   │  CnpjValidator                  │   │   │   │
│   │   │   └─────────────────────────────────┘   │   │   │
│   │   └─────────────────────────────────────────┘   │   │
│   └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

**Regra:** uma camada só conhece as camadas de dentro. As `Entities` não sabem que existem casos
de uso; os `UseCases` não sabem que existe SQLite nem HTML.

**Por quê?** As regras de negócio são a parte mais importante e a que menos deve mudar. Se amanhã
o banco virar MySQL ou a tela virar uma API JSON, só as camadas de fora mudam.

## 2. Pastas

```
src/
├── Entities/        regras de negócio da fundação
├── UseCases/        o que o sistema faz (um caso de uso por ação)
└── Adapters/
    ├── Controllers/     HTTP → caso de uso
    ├── Repositories/    caso de uso → SQLite
    └── Views/           dados → HTML
```

Os nomes das pastas são os nomes das camadas, então dá para saber o papel de cada arquivo só
pelo caminho.

## 3. Entities

- **`Foundation`**: os dados da fundação e a validação deles. O construtor chama `update()`, que
  valida todos os campos de uma vez e lança `ValidationException` com os erros por campo. Assim
  uma `Foundation` nunca existe em estado inválido e o formulário mostra todos os erros juntos.
- **`CnpjValidator`**: a regra dos dígitos verificadores do CNPJ. É a regra mais complexa do
  sistema, então ficou numa classe própria, que pode ser lida e testada sozinha.
- **`ValidationException`**: carrega os erros por campo (`['cnpj' => 'CNPJ inválido.', ...]`).

O CNPJ e o telefone são guardados só com dígitos. A formatação (`11.222.333/0001-81`) é
responsabilidade da tela, por isso fica na `View`.

## 4. UseCases

Uma classe por ação, cada uma com um método `execute()`:

| Classe | O que faz |
| --- | --- |
| `CreateFoundation` | cadastra, impedindo CNPJ duplicado |
| `UpdateFoundation` | altera, impedindo usar o CNPJ de outra fundação |
| `DeleteFoundation` | exclui |
| `FindFoundationByCnpj` | pesquisa pelo CNPJ (com ou sem máscara) |
| `FindFoundationById` | carrega para edição |
| `ListFoundations` | lista todas |

Também ficam aqui:

- **`FoundationDTO`**: leva os dados do formulário até o caso de uso, sem depender de `$_POST`.
- **`FoundationRepositoryInterface`**: o que os casos de uso precisam para guardar e buscar
  fundações (ver item 6).
- **`FoundationNotFoundException`**: lançada com a mensagem "Fundação não encontrada".

**Onde fica a regra "não pode CNPJ duplicado"?** No caso de uso, porque ela depende de outras
fundações (uma fundação sozinha não conhece as demais). Além disso, a tabela tem `UNIQUE` no CNPJ
como garantia final no banco.

## 5. Adapters

Adaptadores convertem dados entre o "mundo de fora" e os casos de uso:

- **`FoundationController`**: recebe a requisição, monta o `FoundationDTO`, chama o caso de uso e
  escolhe a página. Captura `ValidationException` (mostra os erros no formulário) e
  `FoundationNotFoundException` (mostra a mensagem). Depois de cadastrar, alterar ou excluir,
  guarda a mensagem de sucesso na sessão e redireciona (padrão Post/Redirect/Get, que evita
  cadastrar de novo ao apertar F5).
- **`SqliteFoundationRepository`**: implementa a interface do repositório com PDO e prepared
  statements. Converte linha da tabela ⇄ entidade.
- **`View`**: renderiza os templates, escapa toda saída com `htmlspecialchars` (proteção contra
  XSS) e formata CNPJ e telefone.

## 6. Por que a interface do repositório fica nos UseCases

O caso de uso precisa salvar dados, mas o SQLite está numa camada de fora. Se o caso de uso
usasse o `SqliteFoundationRepository` direto, a seta apontaria de dentro para fora, quebrando a
regra principal.

A solução é a **inversão de dependência** (o "D" do SOLID): o caso de uso define a interface de
que precisa (`FoundationRepositoryInterface`) e o adaptador de fora a implementa.

```
CreateFoundation ──► FoundationRepositoryInterface ◄── SqliteFoundationRepository
   (UseCases)              (UseCases)                       (Adapters)
```

Agora as duas setas apontam para dentro.

## 7. Injeção de dependência

### O que é

Uma classe **não cria** os objetos de que precisa: ela os **recebe prontos** pelo construtor.

Sem injeção de dependência, o caso de uso criaria o próprio repositório e ficaria preso ao SQLite:

```php
$repository = new SqliteFoundationRepository(new PDO('sqlite:...'));
```

Com injeção de dependência, ele só declara do que precisa, e pede a **interface**:

```php
final class CreateFoundation
{
    public function __construct(private readonly FoundationRepositoryInterface $repository)
    {
    }
}
```

### Como funciona neste projeto

Todo o encaixe acontece em `public/index.php`, o único arquivo que conhece as classes concretas:

```php
$repository = new SqliteFoundationRepository($pdo);

$controller = new FoundationController(
    new View($rootPath . '/templates'),
    new ListFoundations($repository),
    new CreateFoundation($repository),
    // ...
);
```

### Por que isso ajuda

1. **Testes sem banco de dados.** Nos testes, o caso de uso recebe um
   `InMemoryFoundationRepository`, que guarda tudo num array. O `CreateFoundation` funciona igual,
   sem saber que trocaram o repositório.
2. **Trocar o banco mexe em um lugar só.** Para usar MySQL, bastaria criar um
   `MysqlFoundationRepository` que implemente a interface e trocar uma linha no `index.php`.
3. **As dependências ficam visíveis** no construtor de cada classe.

### Por que sem container

Frameworks usam um container que cria e liga os objetos automaticamente. O enunciado proíbe
frameworks e, com poucas classes, montar tudo à mão é mais simples e deixa explícito de onde vem
cada objeto.

## 8. O que ficou de fora (e por quê)

- **Presenters e ViewModels separados**: na arquitetura limpa "completa" o caso de uso entrega os
  dados a um presenter. Aqui o controller recebe a entidade e passa para a `View`, o que é
  suficiente para telas simples.
- **Value Objects e outros conceitos de DDD**: o CNPJ é validado por uma classe simples
  (`CnpjValidator`) e guardado como texto.
- **Router, objetos de Request/Response e token CSRF**: as rotas são um `match` no `index.php`.
  Seriam o próximo passo num sistema maior.
- **Instituição como entidade separada**: o enunciado pede apenas "a instituição que ela apoia";
  um campo de texto atende.
