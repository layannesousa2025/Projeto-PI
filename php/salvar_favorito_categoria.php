<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

function responder(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    responder(401, ['status' => 'erro', 'mensagem' => 'Usuário não autenticado.']);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    responder(400, ['status' => 'erro', 'mensagem' => 'Dados da solicitação inválidos.']);
}

$categoria = $data['category'] ?? null;
$isFavorited = $data['isFavorited'] ?? null;
$colunasValidas = ['ciclismo', 'futebol', 'voleibol', 'academia', 'caminhada', 'natacao', 'lazer', 'pcd'];

if (!is_string($categoria) || !in_array($categoria, $colunasValidas, true) || !is_bool($isFavorited)) {
    responder(400, ['status' => 'erro', 'mensagem' => 'Categoria ou estado de favorito inválido.']);
}

try {
    require_once __DIR__ . '/database.php';
    $pdo = champions_pdo();
    $idUsuario = $_SESSION['id_cadastro_usuario'] ?? (
        ($_SESSION['tipo_usuario'] ?? '') === 'Admin'
            ? null
            : ($_SESSION['id'] ?? null)
    );
    if ($idUsuario === null && ($_SESSION['tipo_usuario'] ?? '') === 'Admin') {
        $stmtUser = $pdo->prepare('SELECT id_cadastro_usuario FROM cadastro_usuario WHERE id_login = ? LIMIT 1');
        $stmtUser->execute([$_SESSION['id'] ?? null]);
        $idUsuario = $stmtUser->fetchColumn() ?: null;
    }
    if ($idUsuario === null) {
        responder(403, ['status' => 'erro', 'mensagem' => 'Não há cadastro de usuário associado a esta conta para salvar favoritos.']);
    }

    $stmtCheck = $pdo->prepare('SELECT id_categoria FROM categoria WHERE id_cadastro_usuario = ? LIMIT 1');
    $stmtCheck->execute([$idUsuario]);
    if ($stmtCheck->fetchColumn() === false) {
        $stmtInsert = $pdo->prepare('INSERT INTO categoria (id_cadastro_usuario) VALUES (?)');
        $stmtInsert->execute([$idUsuario]);
    }

    $stmtUpdate = $pdo->prepare("UPDATE categoria SET `$categoria` = ? WHERE id_cadastro_usuario = ?");
    $stmtUpdate->execute([$isFavorited ? 1 : 0, $idUsuario]);

    responder(200, ['status' => 'sucesso', 'mensagem' => 'Favorito atualizado.']);
} catch (PDOException $e) {
    error_log('Erro ao salvar favorito: ' . $e->getMessage());
    responder(500, ['status' => 'erro', 'mensagem' => 'Não foi possível salvar o favorito.']);
}
