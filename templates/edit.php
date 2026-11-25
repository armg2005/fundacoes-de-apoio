<?php
/**
 * @var App\Adapters\Views\View $this
 * @var App\Entities\Foundation $foundation
 * @var array $values
 * @var array $errors
 */
?>
<section class="card card--narrow">
    <h1 class="card__title">Editar fundação</h1>

    <?php if ($errors !== []): ?>
        <div class="alert alert--error" role="alert">Corrija os campos destacados abaixo.</div>
    <?php endif; ?>

    <form method="post" action="/?action=update&amp;id=<?= $foundation->id() ?>" class="form" novalidate>
        <div class="field<?php if (isset($errors['name'])): ?> field--invalid<?php endif; ?>">
            <label for="name">Nome da fundação</label>
            <input id="name" name="name" type="text" maxlength="255" required autofocus
                   value="<?= $this->e($values['name']) ?>">
            <?php if (isset($errors['name'])): ?>
                <small class="field__error"><?= $this->e($errors['name']) ?></small>
            <?php endif; ?>
        </div>

        <div class="field<?php if (isset($errors['cnpj'])): ?> field--invalid<?php endif; ?>">
            <label for="cnpj">CNPJ</label>
            <input id="cnpj" name="cnpj" type="text" required data-mask="cnpj" inputmode="numeric"
                   placeholder="00.000.000/0000-00" value="<?= $this->e($values['cnpj']) ?>">
            <?php if (isset($errors['cnpj'])): ?>
                <small class="field__error"><?= $this->e($errors['cnpj']) ?></small>
            <?php endif; ?>
        </div>

        <div class="field<?php if (isset($errors['email'])): ?> field--invalid<?php endif; ?>">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" required
                   placeholder="contato@fundacao.org.br" value="<?= $this->e($values['email']) ?>">
            <?php if (isset($errors['email'])): ?>
                <small class="field__error"><?= $this->e($errors['email']) ?></small>
            <?php endif; ?>
        </div>

        <div class="field<?php if (isset($errors['phone'])): ?> field--invalid<?php endif; ?>">
            <label for="phone">Telefone</label>
            <input id="phone" name="phone" type="tel" required data-mask="phone" inputmode="numeric"
                   placeholder="(00) 00000-0000" value="<?= $this->e($values['phone']) ?>">
            <?php if (isset($errors['phone'])): ?>
                <small class="field__error"><?= $this->e($errors['phone']) ?></small>
            <?php endif; ?>
        </div>

        <div class="field<?php if (isset($errors['supportedInstitution'])): ?> field--invalid<?php endif; ?>">
            <label for="supportedInstitution">Instituição apoiada</label>
            <input id="supportedInstitution" name="supportedInstitution" type="text" maxlength="255" required
                   placeholder="Ex.: Universidade de Brasília" value="<?= $this->e($values['supportedInstitution']) ?>">
            <?php if (isset($errors['supportedInstitution'])): ?>
                <small class="field__error"><?= $this->e($errors['supportedInstitution']) ?></small>
            <?php endif; ?>
        </div>

        <div class="form__actions">
            <a href="/?action=list" class="button button--secondary">Cancelar</a>
            <button type="submit" class="button">Salvar alterações</button>
        </div>
    </form>
</section>
