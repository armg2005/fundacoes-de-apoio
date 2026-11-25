<?php

declare(strict_types=1);

namespace App\Adapters\Controllers;

use App\Adapters\Views\View;
use App\Entities\Foundation;
use App\Entities\ValidationException;
use App\UseCases\CreateFoundation;
use App\UseCases\DeleteFoundation;
use App\UseCases\FindFoundationByCnpj;
use App\UseCases\FindFoundationById;
use App\UseCases\FoundationDTO;
use App\UseCases\FoundationNotFoundException;
use App\UseCases\ListFoundations;
use App\UseCases\UpdateFoundation;

/**
 * Adaptador entre o HTTP e os casos de uso: recebe a requisição, chama o caso de uso
 */
final class FoundationController
{
    public function __construct(
        private readonly View $view,
        private readonly ListFoundations $listFoundations,
        private readonly FindFoundationByCnpj $findFoundationByCnpj,
        private readonly FindFoundationById $findFoundationById,
        private readonly CreateFoundation $createFoundation,
        private readonly UpdateFoundation $updateFoundation,
        private readonly DeleteFoundation $deleteFoundation,
    ) {
    }

    public function index(): void
    {
        $this->showList($this->listFoundations->execute());
    }

    public function search(string $cnpj): void
    {
        try {
            $foundation = $this->findFoundationByCnpj->execute($cnpj);
        } catch (FoundationNotFoundException $exception) {
            $this->showList($this->listFoundations->execute(), $cnpj, $exception->getMessage());
            return;
        }

        // O resultado da pesquisa aparece na própria tabela, só com a fundação encontrada.
        $this->showList([$foundation], $cnpj);
    }

    public function create(): void
    {
        $this->showCreateForm($this->emptyValues());
    }

    public function store(array $data): void
    {
        $dto = FoundationDTO::fromArray($data);

        try {
            $this->createFoundation->execute($dto);
        } catch (ValidationException $exception) {
            $this->showCreateForm($dto->toArray(), $exception->errors());
            return;
        }

        $this->redirectWithMessage('success', 'Fundação cadastrada com sucesso!');
    }

    public function edit(int $id): void
    {
        try {
            $foundation = $this->findFoundationById->execute($id);
        } catch (FoundationNotFoundException $exception) {
            $this->redirectWithMessage('error', $exception->getMessage());
            return;
        }

        $this->showEditForm($foundation, $this->valuesFrom($foundation));
    }

    public function update(int $id, array $data): void
    {
        $dto = FoundationDTO::fromArray($data);

        try {
            $this->updateFoundation->execute($id, $dto);
        } catch (FoundationNotFoundException $exception) {
            $this->redirectWithMessage('error', $exception->getMessage());
            return;
        } catch (ValidationException $exception) {
            $this->showEditForm($this->findFoundationById->execute($id), $dto->toArray(), $exception->errors());
            return;
        }

        $this->redirectWithMessage('success', 'Fundação atualizada com sucesso!');
    }

    public function delete(int $id): void
    {
        try {
            $this->deleteFoundation->execute($id);
        } catch (FoundationNotFoundException $exception) {
            $this->redirectWithMessage('error', $exception->getMessage());
            return;
        }

        $this->redirectWithMessage('success', 'Fundação excluída com sucesso!');
    }

    public function notFound(): void
    {
        http_response_code(404);
        echo 'Página não encontrada';
    }

    private function showList(array $foundations, string $searchedCnpj = '', ?string $searchError = null): void
    {
        $this->render('list', [
            'title' => 'Fundações de Apoio',
            'foundations' => $foundations,
            'searchedCnpj' => $searchedCnpj,
            'searchError' => $searchError,
        ]);
    }

    private function showCreateForm(array $values, array $errors = []): void
    {
        $this->render('create', [
            'title' => 'Nova fundação',
            'values' => $values,
            'errors' => $errors,
        ]);
    }

    private function showEditForm(Foundation $foundation, array $values, array $errors = []): void
    {
        $this->render('edit', [
            'title' => 'Editar fundação',
            'foundation' => $foundation,
            'values' => $values,
            'errors' => $errors,
        ]);
    }

    private function render(string $template, array $data): void
    {
        $data['flash'] = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        echo $this->view->render($template, $data);
    }

    /**
     * Guarda a mensagem na sessão .
     */
    private function redirectWithMessage(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        header('Location: /');
    }

    private function emptyValues(): array
    {
        return ['name' => '', 'cnpj' => '', 'email' => '', 'phone' => '', 'supportedInstitution' => ''];
    }

    private function valuesFrom(Foundation $foundation): array
    {
        return [
            'name' => $foundation->name(),
            'cnpj' => $this->view->cnpj($foundation->cnpj()),
            'email' => $foundation->email(),
            'phone' => $this->view->phone($foundation->phone()),
            'supportedInstitution' => $foundation->supportedInstitution(),
        ];
    }
}
