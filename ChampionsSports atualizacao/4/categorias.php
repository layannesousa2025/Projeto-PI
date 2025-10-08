<?php
session_start();
header('Content-Type: application/json');

// Verifica login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado. Faça login para salvar favoritos.']);
    exit;
}

// Lê o JSON recebido do fetch()
$data = json_decode(file_get_contents('php://input'), true);
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($data['categoria']) || !isset($data['acao'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
    exit;
}

$categoria = $data['categoria'];
$acao = $data['acao'];
$id_usuario = $_SESSION['id']; // precisa existir na sessão

// Conecta ao banco
$conn = new mysqli("localhost", "root", "", "banco_teste01");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco.']);
    exit;
}

// Mapeia o nome da categoria para a coluna correta
$colunas_validas = [
    'Ciclismo' => 'ciclismo',
    'Futebol' => 'futebol',
    'Voleibol' => 'voleibol',
    'Academia' => 'academia',
    'Caminhada' => 'caminhada',
    'Natação' => 'natacao',
    'Lazer' => 'lazer',
    'PCD' => 'pcd'
];

$coluna_db = $colunas_validas[$categoria] ?? null;

if (!$coluna_db || !in_array($acao, ['add', 'remove'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Categoria ou ação inválida.']);
    exit;
}

// Garante que o registro do usuário existe e atualiza a coluna correta em uma única operação.
// Para isso funcionar, a coluna `id_usuario` na tabela `categoria` deve ser uma chave única (UNIQUE).
$sql = "INSERT INTO categoria (id_usuario, $coluna_db) VALUES (?, ?) ON DUPLICATE KEY UPDATE $coluna_db = VALUES($coluna_db)";

// Define valor da coluna (1 = favoritado, 0 = removido)
$valor = ($acao === 'add') ? 1 : 0;

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $valor);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Favorito atualizado com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar favorito: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
