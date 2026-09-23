<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

function listar_destinos(bool $apenasAtivos = true): array
{
    $query = 'SELECT * FROM destinos';
    if ($apenasAtivos) {
        $query .= " WHERE status = 'ativo'";
    }
    $query .= ' ORDER BY id DESC';
    return database()->query($query)->fetchAll();
}

function listar_destinos_por_status(?string $status = null): array
{
    $permitidos = ['ativo', 'em_uso', 'inativo'];
    if ($status !== null && in_array($status, $permitidos, true)) {
        $statement = database()->prepare('SELECT * FROM destinos WHERE status = :status ORDER BY id DESC');
        $statement->execute([':status' => $status]);
        return $statement->fetchAll();
    }

    return database()->query('SELECT * FROM destinos ORDER BY id DESC')->fetchAll();
}

function buscar_destino_por_id(int $id): ?array
{
    $statement = database()->prepare('SELECT * FROM destinos WHERE id = :id LIMIT 1');
    $statement->execute([':id' => $id]);
    $destino = $statement->fetch();
    return $destino ?: null;
}

function listar_destinos_paginados(array $filtros, int $pagina, int $porPagina = 20): array
{
    $pagina = max(1, $pagina);
    $porPagina = max(1, min(20, $porPagina));
    $where = ["status = 'ativo'"];
    $parametros = [];

    if (($filtros['busca'] ?? '') !== '') {
        $where[] = '(nome LIKE :busca OR estado LIKE :busca OR localizacao LIKE :busca)';
        $parametros[':busca'] = '%' . trim((string) $filtros['busca']) . '%';
    }
    if (($filtros['estado'] ?? '') !== '') {
        $where[] = 'estado = :estado';
        $parametros[':estado'] = strtoupper(trim((string) $filtros['estado']));
    }
    if (($filtros['tipo'] ?? '') !== '') {
        $where[] = 'tipo = :tipo';
        $parametros[':tipo'] = trim((string) $filtros['tipo']);
    }

    $condicao = implode(' AND ', $where);
    $pdo = database();
    $totalStatement = $pdo->prepare('SELECT COUNT(*) FROM destinos WHERE ' . $condicao);
    $totalStatement->execute($parametros);
    $total = (int) $totalStatement->fetchColumn();

    $offset = ($pagina - 1) * $porPagina;
    $statement = $pdo->prepare('SELECT * FROM destinos WHERE ' . $condicao . ' ORDER BY id DESC LIMIT :limite OFFSET :offset');
    foreach ($parametros as $chave => $valor) {
        $statement->bindValue($chave, $valor, PDO::PARAM_STR);
    }
    $statement->bindValue(':limite', $porPagina, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();

    return ['itens' => $statement->fetchAll(), 'total' => $total, 'pagina' => $pagina, 'por_pagina' => $porPagina];
}

function opcoes_filtro_destinos(): array
{
    $estados = database()->query("SELECT DISTINCT estado FROM destinos WHERE status = 'ativo' ORDER BY estado")->fetchAll(PDO::FETCH_COLUMN);
    $tipos = database()->query("SELECT DISTINCT tipo FROM destinos WHERE status = 'ativo' ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
    return ['estados' => $estados, 'tipos' => $tipos];
}

function criar_destino(array $dados): void
{
    $statement = database()->prepare('INSERT INTO destinos (nome, estado, tipo, preco, imagem, resumo, descricao, localizacao, destaque, galeria, status, ativo) VALUES (:nome, :estado, :tipo, :preco, :imagem, :resumo, :descricao, :localizacao, :destaque, :galeria, :status, :ativo)');
    $statement->execute($dados);
}

function atualizar_destino(int $id, array $dados): void
{
    $dados['id'] = $id;
    $statement = database()->prepare('UPDATE destinos SET nome = :nome, estado = :estado, tipo = :tipo, preco = :preco, imagem = :imagem, resumo = :resumo, descricao = :descricao, localizacao = :localizacao, destaque = :destaque, galeria = :galeria, status = :status, ativo = :ativo WHERE id = :id');
    $statement->execute($dados);
}

function atualizar_status_destino(int $id, string $status): bool
{
    if (!in_array($status, ['ativo', 'em_uso', 'inativo'], true)) {
        return false;
    }

    $statement = database()->prepare('UPDATE destinos SET status = :status, ativo = :ativo WHERE id = :id');
    return $statement->execute([
        ':status' => $status,
        ':ativo' => $status === 'ativo' ? 1 : 0,
        ':id' => $id,
    ]);
}

function estatisticas_destinos(): array
{
    $pdo = database();

    $status = $pdo->query(
        "SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'ativo' THEN 1 ELSE 0 END) AS ativos,
            SUM(CASE WHEN status = 'inativo' THEN 1 ELSE 0 END) AS inativos,
            SUM(CASE WHEN status = 'em_uso' THEN 1 ELSE 0 END) AS em_uso
         FROM destinos"
    )->fetch() ?: [];

    $porDia = $pdo->query(
        "SELECT date(criado_em) AS periodo, COUNT(*) AS total
         FROM destinos
         WHERE date(criado_em) >= date('now', '-13 days')
         GROUP BY date(criado_em)
         ORDER BY periodo"
    )->fetchAll();

    $porMes = $pdo->query(
        "SELECT strftime('%Y-%m', criado_em) AS periodo, COUNT(*) AS total
         FROM destinos
         WHERE date(criado_em) >= date('now', '-5 months', 'start of month')
         GROUP BY strftime('%Y-%m', criado_em)
         ORDER BY periodo"
    )->fetchAll();

    $porEstado = $pdo->query(
        'SELECT estado, COUNT(*) AS total
         FROM destinos
         GROUP BY estado
         ORDER BY total DESC, estado ASC'
    )->fetchAll();

    return [
        'status' => [
            'total' => (int) ($status['total'] ?? 0),
            'ativos' => (int) ($status['ativos'] ?? 0),
            'inativos' => (int) ($status['inativos'] ?? 0),
            'em_uso' => (int) ($status['em_uso'] ?? 0),
        ],
        'por_dia' => array_map(static fn (array $linha): array => [
            'periodo' => (string) $linha['periodo'],
            'total' => (int) $linha['total'],
        ], $porDia),
        'por_mes' => array_map(static fn (array $linha): array => [
            'periodo' => (string) $linha['periodo'],
            'total' => (int) $linha['total'],
        ], $porMes),
        'por_estado' => array_map(static fn (array $linha): array => [
            'estado' => (string) $linha['estado'],
            'total' => (int) $linha['total'],
        ], $porEstado),
    ];
}