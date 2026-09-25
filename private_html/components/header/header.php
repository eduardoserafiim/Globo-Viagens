<?php
function getHeader(string $titulo, string $descricao = ''): void
{
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php if ($descricao !== ''): ?><meta name="description" content="<?= htmlspecialchars($descricao) ?>"><?php endif; ?>
        <title><?= htmlspecialchars($titulo) ?> | Globo Viagens</title>
        <link rel="icon" href="<?= asset('images/LogoGlobo.png') ?>">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <link rel="stylesheet" href="../styles/styles.css?v=20260925-2">
    </head>
    <?php
}
