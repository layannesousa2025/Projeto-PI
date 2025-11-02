<?php
session_start(); // Inicia a sessão

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401); // Não autorizado
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Por favor, faça o login.']);
    exit;
}

// Dados de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_teste";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    // Em caso de erro, retorna uma resposta de erro em JSON
    http_response_code(500);
    echo json_encode(['error' => 'Falha na conexão com o banco de dados: ' . $conn->connect_error]);
    exit;
}

// Prepara e executa a consulta para buscar os eventos
// Usamos aliases (AS) para renomear as colunas do banco para os nomes que o JavaScript espera (title, date, game, etc.)
// Adicionamos as colunas 'informacao' e 'link' que o JS também espera.
$sql = "SELECT 
            id_eventos, 
            tipo_eventos, 
            localizacao, 
            data_eventos, 
            categorias_esportes,
            informacao, link
        FROM eventos 
        WHERE situacao = 'A'";
$result = $conn->query($sql);

$events = [];
if ($result->num_rows > 0) {
    // Coleta os dados de cada linha
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Fecha a conexão e retorna os eventos em formato JSON
$conn->close();
echo json_encode([
    "success" => true,
    "data" => $events
]);
?>