<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function listar_videos_relato(): array
{
    return database()->query('SELECT * FROM videos_relatos ORDER BY id DESC')->fetchAll();
}

function criar_video_relato(array $dados): void
{
    $statement = database()->prepare('INSERT INTO videos_relatos (titulo, autor, destino, descricao, url) VALUES (:titulo, :autor, :destino, :descricao, :url)');
    $statement->execute($dados);
}

function excluir_video_relato(int $id): void
{
    $statement = database()->prepare('DELETE FROM videos_relatos WHERE id = :id');
    $statement->execute([':id' => $id]);
}

