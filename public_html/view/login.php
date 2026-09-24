<?php
require_once __DIR__ . '/../../private_html/config/database.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';

if (autenticado()) {
    header('Location: dashboard');
    exit;
}
?>
<?php getHeader('Login | Painel', 'Acesso ao painel administrativo da Globo Viagens.'); ?>
<body class="dashboard-body">
    <main class="login-page">
        <div class="login-panel">
            <a href="index" class="login-logo"><img src="<?= asset('images/Logo.png') ?>" alt="Globo Viagens"></a>
            <p class="eyebrow">Acesso interno</p>
            <h1>Bem-vindo<br>de volta.</h1>
            <p>Entre para acompanhar o movimento da Globo Viagens.</p>
            <form method="post" action="../../private_html/controllers/loginController.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <label for="email">E-mail</label>
                <input id="email" name="email" type="email" autocomplete="email" required>
                <label for="senha">Senha</label>
                <input id="senha" name="senha" type="password" autocomplete="current-password" required>
                <button class="button button-dark" type="submit">Entrar no painel <span>↗</span></button>
            </form>
            <a class="back-link" href="index">← Voltar para o site</a>
        </div>
        <div class="login-art"><img src="<?= asset('images/Florianopolis-SC/IMG-20250523-WA0018.jpg') ?>" alt="Destino de viagem"></div>
    </main>
</body>
</html>
