<?php

declare(strict_types=1);

namespace App\Entities;

/**
 * É a camada mais interna
 */
final class Foundation
{
    private ?int $id;
    private string $name;
    private string $cnpj;
    private string $email;
    private string $phone;
    private string $supportedInstitution;

    /**
     * O id fica vazio (null) até a fundação ser salva no banco.
     *
     * @throws ValidationException
     */
    public function __construct(
        string $name,
        string $cnpj,
        string $email,
        string $phone,
        string $supportedInstitution,
        ?int $id = null,
    ) {
        $this->id = $id;
        $this->update($name, $cnpj, $email, $phone, $supportedInstitution);
    }

    /**
     * Valida todos os campos de uma vez, para o formulário mostrar todos os erros juntos.
     * Só altera os dados se estiver tudo certo.
     *
     * @throws ValidationException
     */
    public function update(
        string $name,
        string $cnpj,
        string $email,
        string $phone,
        string $supportedInstitution,
    ): void {
        $name = trim($name);
        $cnpj = preg_replace('/\D/', '', $cnpj);
        $email = trim($email);
        $phone = preg_replace('/\D/', '', $phone);
        $supportedInstitution = trim($supportedInstitution);

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Informe o nome da fundação.';
        }

        if (!CnpjValidator::isValid($cnpj)) {
            $errors['cnpj'] = 'CNPJ inválido.';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Informe um e-mail válido.';
        }

        // Telefone fixo (10 dígitos) ou celular (11 dígitos), sempre com DDD.
        if (strlen($phone) < 10 || strlen($phone) > 11) {
            $errors['phone'] = 'Informe um telefone válido com DDD.';
        }

        if ($supportedInstitution === '') {
            $errors['supportedInstitution'] = 'Informe a instituição apoiada.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $this->name = $name;
        $this->cnpj = $cnpj;
        $this->email = $email;
        $this->phone = $phone;
        $this->supportedInstitution = $supportedInstitution;
    }

    /**
     * Chamado pelo repositório depois do INSERT, quando o banco gera o id.
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * CNPJ apenas com dígitos.
     */
    public function cnpj(): string
    {
        return $this->cnpj;
    }

    public function email(): string
    {
        return $this->email;
    }

    /**
     * Telefone apenas com dígitos (DDD + número).
     */
    public function phone(): string
    {
        return $this->phone;
    }

    public function supportedInstitution(): string
    {
        return $this->supportedInstitution;
    }
}
