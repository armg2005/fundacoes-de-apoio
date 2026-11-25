<?php

declare(strict_types=1);

namespace App\UseCases;


final class FoundationDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $cnpj,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $supportedInstitution,
    ) {
    }


    public static function fromArray(array $data): self
    {
        return new self(
            trim($data['name'] ?? ''),
            trim($data['cnpj'] ?? ''),
            trim($data['email'] ?? ''),
            trim($data['phone'] ?? ''),
            trim($data['supportedInstitution'] ?? ''),
        );
    }

    /**
     * Usado para devolver ao formulário o que o usuário digitou quando há erro.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'cnpj' => $this->cnpj,
            'email' => $this->email,
            'phone' => $this->phone,
            'supportedInstitution' => $this->supportedInstitution,
        ];
    }
}
