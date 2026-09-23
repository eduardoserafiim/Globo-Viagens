<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: ../../public_html/dashboard.php');
    exit;
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$senha = (string) ($_POST['senha'] ?? '');

if ($email === strtolower(ADMIN_EMAIL) && password_verify($senha, senha_admin_hash())) {
    session_regenerate_id(true);
    $_SESSION['admin'] = true;
    $_SESSION['admin_email'] = $email;
    $_SESSION['admin_nome'] = ADMIN_NAME;
    $_SESSION['admin_foto'] = DEFAULT_ADMIN_PHOTO;
}

header('Location: ../../public_html/dashboard.php');
exit;