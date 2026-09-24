<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function listar_planos(): array
{
    return database()->query('SELECT * FROM planos ORDER BY id ASC')->fetchAll();
}

function buscar_plano_por_id(int $id): ?array
{
    $statement = database()->prepare('SELECT * FROM planos WHERE id = :id LIMIT 1');
    $statement->execute([':id' => $id]);
    $plano = $statement->fetch();
    return $plano ?: null;
}

function criar_plano(array $dados): void
{
    $statement = database()->prepare('INSERT INTO planos (nome, preco, categoria, classe, descricao, icone) VALUES (:nome, :preco, :categoria, :classe, :descricao, :icone)');
    $statement->execute($dados);
}

function atualizar_plano(int $id, array $dados): void
{
    $dados['id'] = $id;
    $statement = database()->prepare('UPDATE planos SET nome = :nome, preco = :preco, categoria = :categoria, classe = :classe, descricao = :descricao, icone = :icone WHERE id = :id');
    $statement->execute($dados);
}

function excluir_plano(int $id): void
{
    $statement = database()->prepare('DELETE FROM planos WHERE id = :id');
    $statement->execute([':id' => $id]);
}
