<?php
declare(strict_types=1);

session_start();

const APP_ROOT = __DIR__ . '/../../';
const DATABASE_FILE = __DIR__ . '/../data/globo.sqlite';
const ADMIN_PASSWORD_HASH = '$2y$10$usso19DCqeLP6cySeIzsCu8jqv6d40S1GJklmZOE79.E5Nz3/91Se';
const ADMIN_EMAIL = 'admin@globoviagens.com';
const ADMIN_NAME = 'Eduardo Serafim';
const DEFAULT_ADMIN_PHOTO = 'images/Logo.png';

function database(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dataDirectory = dirname(DATABASE_FILE);
    if (!is_dir($dataDirectory)) {
        mkdir($dataDirectory, 0750, true);
    }

    $pdo = new PDO('sqlite:' . DATABASE_FILE, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('CREATE TABLE IF NOT EXISTS destinos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        estado TEXT NOT NULL,
        tipo TEXT NOT NULL,
        preco REAL NOT NULL,
        imagem TEXT NOT NULL,
        resumo TEXT NOT NULL,
        descricao TEXT NOT NULL DEFAULT "",
        localizacao TEXT NOT NULL DEFAULT "",
        destaque TEXT NOT NULL DEFAULT "",
        galeria TEXT NOT NULL DEFAULT "",
        ativo INTEGER NOT NULL DEFAULT 1,
        status TEXT NOT NULL DEFAULT "ativo",
        criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');

    $colunas = array_map(static fn (array $coluna): string => (string) $coluna['name'], $pdo->query('PRAGMA table_info(destinos)')->fetchAll());
    foreach (['descricao', 'localizacao', 'destaque', 'galeria', 'status'] as $coluna) {
        if (!in_array($coluna, $colunas, true)) {
            $padrao = $coluna === 'status' ? 'ativo' : '';
            $pdo->exec('ALTER TABLE destinos ADD COLUMN ' . $coluna . ' TEXT NOT NULL DEFAULT "' . $padrao . '"');
        }
    }

    $pdo->exec("UPDATE destinos SET status = CASE WHEN ativo = 1 THEN 'ativo' ELSE 'inativo' END WHERE status = '' OR status IS NULL");

    $pdo->exec('CREATE TABLE IF NOT EXISTS admin_config (
        id INTEGER PRIMARY KEY CHECK (id = 1),
        senha_hash TEXT NOT NULL,
        atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');
    $adminConfig = $pdo->prepare('INSERT OR IGNORE INTO admin_config (id, senha_hash) VALUES (1, :senha_hash)');
    $adminConfig->execute([':senha_hash' => ADMIN_PASSWORD_HASH]);

    $pdo->exec('CREATE TABLE IF NOT EXISTS videos_relatos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT NOT NULL,
        autor TEXT NOT NULL,
        destino TEXT NOT NULL DEFAULT "",
        descricao TEXT NOT NULL DEFAULT "",
        url TEXT NOT NULL,
        criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');

    $total = (int) $pdo->query('SELECT COUNT(*) FROM destinos')->fetchColumn();
    if ($total === 0) {
        $destinos = [
            ['Porto Seguro', 'BA', 'Beira-mar', 39.90, 'images/PortoSeguro-BA/IMG-20250520-WA0017.jpg', 'Condominio Beira Mar para ate 5 pessoas, com piscina, Wi-Fi e cozinha completa.', 'Vista privilegiada para o mar com uma estrutura confortável para famílias e casais que querem relaxar sem abrir mão da praticidade.', 'Porto Seguro - BA', 'Piscina, varanda, cozinha completa e proximidade com a praia.', 'images/PortoSeguro-BA/IMG-20250520-WA0017.jpg'],
            ['Porto de Galinhas', 'PE', 'Apartamento mobiliado', 49.90, 'images/PortoDeGalinhas-PE/IMG-20250520-WA0031.jpg', 'A 50 metros do mar, com 2 quartos, piscina, garagem e toda a estrutura para sua estadia.', 'Apartamento acolhedor com 2 quartos, área gourmet e acesso rápido ao calçadão e às praias mais lindas da região.', 'Porto de Galinhas - PE', 'Praia, conforto e amenidades em um só lugar.', 'images/PortoDeGalinhas-PE/IMG-20250520-WA0031.jpg'],
            ['Fortaleza', 'CE', 'Apartamento mobiliado', 29.90, 'images/Fortaleza-CE/IMG-20250520-WA0042.jpg', 'Espaco para ate 6 pessoas, com 3 quartos, piscina, ar-condicionado e Wi-Fi.', 'Apartamento moderno e bem localizado para quem quer aproveitar o melhor do litoral cearense com conforto e praticidade.', 'Fortaleza - CE', '3 quartos, piscina e acesso fácil ao mar.', 'images/Fortaleza-CE/IMG-20250520-WA0042.jpg'],
            ['Maceio', 'AL', 'Apartamento mobiliado', 49.90, 'images/Maceio-AL/IMG-20250520-WA0047.jpg', 'Apartamento com 3 quartos, piscina, academia e localizacao privilegiada.', 'Estadia completa para descansar, explorar a cidade e curtir o litoral com estrutura premium.', 'Maceio - AL', 'Academia, piscina e localização estratégica.', 'images/Maceio-AL/IMG-20250520-WA0047.jpg'],
        ];
        $statement = $pdo->prepare('INSERT INTO destinos (nome, estado, tipo, preco, imagem, resumo, descricao, localizacao, destaque, galeria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($destinos as $destino) {
            $statement->execute($destino);
        }
    }

    return $pdo;
}

function asset(string $path): string
{
    return '../' . ltrim($path, '/');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validar_csrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function senha_admin_hash(): string
{
    $hash = database()->query('SELECT senha_hash FROM admin_config WHERE id = 1')->fetchColumn();
    return is_string($hash) && $hash !== '' ? $hash : ADMIN_PASSWORD_HASH;
}

function atualizar_senha_admin(string $senha): void
{
    $statement = database()->prepare('UPDATE admin_config SET senha_hash = :senha_hash, atualizado_em = CURRENT_TIMESTAMP WHERE id = 1');
    $statement->execute([':senha_hash' => password_hash($senha, PASSWORD_DEFAULT)]);
}

function autenticado(): bool
{
    return !empty($_SESSION['admin']) && !empty($_SESSION['admin_email']);
}

function perfil_admin(): array
{
    return [
        'nome' => $_SESSION['admin_nome'] ?? ADMIN_NAME,
        'email' => $_SESSION['admin_email'] ?? ADMIN_EMAIL,
        'foto' => $_SESSION['admin_foto'] ?? DEFAULT_ADMIN_PHOTO,
    ];
}

function atualizar_perfil_admin(array $dados): void
{
    $_SESSION['admin_nome'] = trim((string) ($dados['nome'] ?? ADMIN_NAME));
    $_SESSION['admin_email'] = ADMIN_EMAIL;
    $_SESSION['admin_foto'] = trim((string) ($dados['foto'] ?? DEFAULT_ADMIN_PHOTO));
}

function exigir_autenticacao(): void
{
    if (!autenticado()) {
        header('Location: ../public_html/dashboard.php');
        exit;
    }
}