<?php
/**
 * @var App\Adapters\Views\View $this
 * @var string $title
 * @var string $content
 * @var array|null $flash
 */
$pageTitle = 'Fundações de Apoio';
if ($title !== $pageTitle) {
    $pageTitle = $title . ' · Fundações de Apoio';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($pageTitle) ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar__inner">
        <a href="/?action=list" class="brand">
            Fundações de Apoio
        </a>
        <a href="/?action=create" class="button button--light">+ Nova fundação</a>
    </div>
</header>

<main class="container">
    <?php if ($flash !== null): ?>
        <div class="alert alert--<?= $this->e($flash['type']) ?>" role="status"><?= $this->e($flash['message']) ?></div>
    <?php endif; ?>

    <?= $content ?>
</main>

<script src="/js/app.js"></script>
</body>
</html>
