<?php
session_start(); // Inicia a sessão para armazenar o ID do usuário

// Dados de conexão com o banco de dados (ajuste conforme necessário)
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "banco_teste01";
$port       = 3309; // Porta correta do seu servidor MySQL

// Habilita o modo de relatório de exceções para o mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Cria a conexão dentro do bloco try
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
} catch (mysqli_sql_exception $e) {
    // Trata erros específicos de conexão recusada ou servidor desligado
    if (str_contains($e->getMessage(), 'actively refused it') || $e->getCode() === 2002) {
        http_response_code(500);
        die(json_encode([
            "error" => "❌ Falha na conexão: O servidor de banco de dados (MySQL) parece estar desligado. 
                         Por favor, inicie-o no painel do XAMPP e tente novamente."
        ]));
    }
    // Outros erros genéricos
    http_response_code(500);
    die(json_encode(["error" => "Falha na conexão com o banco de dados: " . $e->getMessage()]));
}

// Se chegou até aqui, a conexão foi bem-sucedida ✅

// Prepara a consulta
$sql = "
    SELECT 
        id_eventos AS id, 
        tipo_eventos AS title, 
        localizacao AS venue, 
        data_eventos AS date, 
        categorias_esportes AS game 
    FROM eventos 
    WHERE situacao = 'A'
";

$result = $conn->query($sql);
$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Adiciona campos extras esperados pelo JavaScript
        $row['lugar']  = $row['venue'];
        $row['cidade'] = $row['venue'];
        $row['lat']    = null; // Lat/Lon poderiam ser obtidos via API de geolocalização
        $row['lon']    = null;
        $events[] = $row;
    }
}

// Fecha a conexão
$conn->close();

// Retorna a resposta JSON
echo json_encode([
    "success" => true,
    "data"    => $events
]);
?>
