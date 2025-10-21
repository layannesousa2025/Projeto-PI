<?php
// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Dados de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_teste01";

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
// Isso evita ter que reescrever o JavaScript.
$sql = "SELECT 
            id_eventos AS id, 
            tipo_eventos AS title, 
            localizacao AS venue, 
            data_eventos AS date, 
            categorias_esportes AS game 
        FROM eventos 
        WHERE situacao = 'A'";
$result = $conn->query($sql);

$events = [];
if ($result->num_rows > 0) {
    // Coleta os dados de cada linha
    while($row = $result->fetch_assoc()) {
        // Adicionamos colunas que o JS espera mas não existem no banco
        $row['lugar'] = $row['venue']; // Usamos 'venue' (localizacao) como 'lugar'
        $row['cidade'] = $row['venue']; // Usamos 'venue' (localizacao) como 'cidade' para a busca
        $row['lat'] = null; // Geocodificação seria necessária para obter lat/lon da localização
        $row['lon'] = null;
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