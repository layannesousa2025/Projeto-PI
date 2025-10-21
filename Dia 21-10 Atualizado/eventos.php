<?php
header('Content-Type: application/json; charset=utf-8');

// Conexão com o banco
$host = "localhost";
$dbname = "banco_teste01";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta os eventos ativos
    $stmt = $pdo->prepare("SELECT 
        id_eventos,
        tipo_eventos,
        categorias_esportes,
        localizacao,
        data_eventos,
        informacao,
        link
    FROM eventos
    WHERE situacao = 'A'");

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
        "message" => "Erro: " . $e->getMessage()
    ]);
}


?>