<?php
/**
 * @var App\Adapters\Views\View $this
 * @var array $foundations
 * @var string $searchedCnpj
 * @var string|null $searchError
 */
?>
<section class="card">
    <h1 class="card__title">Pesquisar por CNPJ</h1>
    <form method="get" action="/" class="search">
        <input type="hidden" name="action" value="search">
        <input type="text" name="cnpj" value="<?= $this->e($searchedCnpj) ?>"
               placeholder="00.000.000/0000-00" data-mask="cnpj" inputmode="numeric"
               aria-label="CNPJ" required>
        <button type="submit" class="button">Pesquisar</button>
        <?php if ($searchedCnpj !== ''): ?>
            <a href="/" class="button button--secondary">Limpar</a>
        <?php endif; ?>
    </form>
    <?php if ($searchError !== null): ?>
        <div class="alert alert--error" role="alert"><?= $this->e($searchError) ?></div>
    <?php endif; ?>
</section>

<section class="card">
    <div class="card__header">
        <?php if ($searchedCnpj !== '' && $searchError === null): ?>
            <h2 class="card__title">Resultado da pesquisa</h2>
        <?php else: ?>
            <h2 class="card__title">Fundações cadastradas</h2>
        <?php endif; ?>
        <span class="badge"><?= count($foundations) ?></span>
    </div>

    <?php if ($foundations === []): ?>
        <p class="empty">Nenhuma fundação cadastrada ainda. <a href="/?action=create">Cadastre a primeira</a>.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Instituição apoiada</th>
                    <th class="table__actions">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($foundations as $foundation): ?>
                    <tr>
                        <td data-label="Nome"><?= $this->e($foundation->name()) ?></td>
                        <td data-label="CNPJ" class="nowrap"><?= $this->e($this->cnpj($foundation->cnpj())) ?></td>
                        <td data-label="E-mail"><?= $this->e($foundation->email()) ?></td>
                        <td data-label="Telefone" class="nowrap"><?= $this->e($this->phone($foundation->phone())) ?></td>
                        <td data-label="Instituição"><?= $this->e($foundation->supportedInstitution()) ?></td>
                        <td class="table__actions">
                            <a href="/?action=edit&amp;id=<?= $foundation->id() ?>" class="button button--small button--secondary">Editar</a>
                            <form method="post" action="/?action=delete&amp;id=<?= $foundation->id() ?>"
                                  class="inline" data-confirm="Excluir a fundação &quot;<?= $this->e($foundation->name()) ?>&quot;?">
                                <button type="submit" class="button button--small button--danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
