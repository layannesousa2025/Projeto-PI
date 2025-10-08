<?php
session_start();

// 1. Verificar se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado. Faça o login para salvar favoritos.']);
    exit;
}

// 2. Validar a requisição
$data = json_decode(file_get_contents('php://input'), true);
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($data['categoria']) || !isset($data['acao'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
    exit;
}

// 3. Conexão com o banco
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_teste01";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados.']);
    exit;
}

// 4. Preparar dados
$id_usuario = $_SESSION['id'];
$categoria = $data['categoria'];
$acao = $data['acao'];

// Mapeia nome → coluna
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

if ($coluna_db && ($acao === 'add' || $acao === 'remove')) {
    // Garante que a linha do usuário exista antes de fazer o UPDATE.
    // Isso é mais seguro que apenas INSERT IGNORE.
    $check_sql = "SELECT id_categoria FROM categoria WHERE id_usuario = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id_usuario);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows === 0) {
        $conn->query("INSERT INTO categoria (id_usuario) VALUES ($id_usuario)");
    }

    $valor = ($acao === 'add') ? 1 : 0;
    $sql = "UPDATE categoria SET $coluna_db = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $valor, $id_usuario);
} else {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ação ou categoria inválida.']);
    exit;
}

header('Content-Type: application/json');
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Favorito atualizado com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar favorito: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
