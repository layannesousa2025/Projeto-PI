<?php
session_start(); // Iniciar a sessão para armazenar o ID do usuário

// Dados de conexão com o banco de dados
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "banco_teste01";
$port = 3309; // Porta usada no XAMPP

header('Content-Type: application/json; charset=utf-8');

try {
    // Cria a conexão usando PDO
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta os eventos ativos (situacao = 'A')
    $stmt = $pdo->prepare("
        SELECT 
            id_eventos,
            tipo_eventos,
            categorias_esportes,
            localizacao,
            data_eventos,
            informacao,
            link
        FROM eventos
        WHERE situacao = 'A'
    ");

    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $eventos
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erro ao conectar ou consultar o banco: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
