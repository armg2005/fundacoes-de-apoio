<?php

declare(strict_types=1);

namespace App\Adapters\Views;

/**
 * Renderiza os templates PHP dentro do layout padrão.
 * Os templates acessam os helpers abaixo através de $this.
 */
final class View
{
    public function __construct(private readonly string $templatesPath)
    {
    }

    public function render(string $template, array $data = []): string
    {
        $data['content'] = $this->renderFile($template, $data);

        return $this->renderFile('layout', $data);
    }

    /**
     * Escapa texto para HTML. Toda saída de dados nos templates passa por aqui.
     */
    public function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Formata o CNPJ salvo só com dígitos: 11.222.333/0001-81.
     */
    public function cnpj(string $digits): string
    {
        return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $digits);
    }

    /**
     * Formata o telefone salvo só com dígitos: (61) 3333-4444 ou (61) 99999-8888.
     */
    public function phone(string $digits): string
    {
        $pattern = '/^(\d{2})(\d{4})(\d{4})$/';
        if (strlen($digits) === 11) {
            $pattern = '/^(\d{2})(\d{5})(\d{4})$/';
        }

        return preg_replace($pattern, '($1) $2-$3', $digits);
    }

    private function renderFile(string $template, array $data): string
    {
        extract($data);
        ob_start();
        require $this->templatesPath . '/' . $template . '.php';

        return ob_get_clean();
    }
}
