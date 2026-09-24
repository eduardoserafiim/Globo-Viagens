<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/loginModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: /view/login');
    exit;
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$senha = (string) ($_POST['senha'] ?? '');

if (($usuario = buscar_usuario_por_email($email)) && password_verify($senha, (string) $usuario['senha_hash'])) {
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['usuario_email'] = (string) $usuario['email'];
    $_SESSION['usuario_nome'] = (string) $usuario['nome'];
    $_SESSION['admin_foto'] = (string) ($usuario['foto'] ?: DEFAULT_ADMIN_PHOTO);
}

header('Location: /view/dashboard');
exit;