<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function buscar_usuario_por_email(string $email): ?array
{
	$statement = database()->prepare('SELECT * FROM usuarios WHERE email = :email AND ativo = 1 LIMIT 1');
	$statement->execute([':email' => strtolower(trim($email))]);
	$usuario = $statement->fetch();
	return $usuario ?: null;
}

function atualizar_usuario(int $id, string $nome, string $foto): void
{
	$statement = database()->prepare('UPDATE usuarios SET nome = :nome, foto = :foto, atualizado_em = CURRENT_TIMESTAMP WHERE id = :id');
	$statement->execute([':id' => $id, ':nome' => trim($nome), ':foto' => trim($foto)]);
}

function atualizar_senha_usuario(int $id, string $senha): void
{
	$statement = database()->prepare('UPDATE usuarios SET senha_hash = :senha_hash, atualizado_em = CURRENT_TIMESTAMP WHERE id = :id');
	$statement->execute([':id' => $id, ':senha_hash' => password_hash($senha, PASSWORD_DEFAULT)]);
}
