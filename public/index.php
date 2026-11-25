<?php

declare(strict_types=1);

use App\Adapters\Controllers\FoundationController;
use App\Adapters\Repositories\SqliteFoundationRepository;
use App\Adapters\Views\View;
use App\UseCases\CreateFoundation;
use App\UseCases\DeleteFoundation;
use App\UseCases\FindFoundationByCnpj;
use App\UseCases\FindFoundationById;
use App\UseCases\ListFoundations;
use App\UseCases\UpdateFoundation;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$rootPath = dirname(__DIR__);

// Banco de dados: cria o arquivo SQLite e a tabela na primeira execução.
$databasePath = getenv('DATABASE_PATH');
if ($databasePath === false || $databasePath === '') {
    $databasePath = $rootPath . '/database/foundations.sqlite';
}

$pdo = new PDO('sqlite:' . $databasePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec(file_get_contents($rootPath . '/database/schema.sql'));

// Injeção de dependência: este é o único lugar que cria as classes concretas e liga
// umas às outras. Cada classe recebe o que precisa pelo construtor, em vez de criar
// sozinha. Explicação completa em docs/ARQUITETURA.md.
$repository = new SqliteFoundationRepository($pdo);

$controller = new FoundationController(
    new View($rootPath . '/templates'),
    new ListFoundations($repository),
    new FindFoundationByCnpj($repository),
    new FindFoundationById($repository),
    new CreateFoundation($repository),
    new UpdateFoundation($repository),
    new DeleteFoundation($repository),
);

// Rotas: método HTTP + parâmetro ?action= da URL.
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

match ("{$method} {$action}") {
    'GET list' => $controller->index(),
    'GET search' => $controller->search(trim($_GET['cnpj'] ?? '')),
    'GET create' => $controller->create(),
    'POST store' => $controller->store($_POST),
    'GET edit' => $controller->edit($id),
    'POST update' => $controller->update($id, $_POST),
    'POST delete' => $controller->delete($id),
    default => $controller->notFound(),
};
