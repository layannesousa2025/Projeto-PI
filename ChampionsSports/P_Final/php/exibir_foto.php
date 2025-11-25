<?php
session_start();

/**
 * Envia uma imagem padrão e encerra o script.
 * @param string $filePath O caminho para o arquivo de imagem padrão.
 */
function serveDefaultImage($filePath = '../img/login2.png') {
    // Garante que o tipo de conteúdo correto seja enviado para a imagem padrão.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($filePath) ?: 'image/png';
    header("Content-Type: " . $mime_type);
    readfile($filePath);
    exit;
}

// 1. Verifica se o usuário está logado e se o ID principal está na sessão.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    serveDefaultImage();
}

// --- INÍCIO: Bloco de Conexão com o Banco de Dados ---
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "champions_sport"; // Corrigido para o banco de dados correto
$port = 3306;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Se a conexão falhar, serve a imagem padrão.
    serveDefaultImage();
}
// --- FIM: Bloco de Conexão ---

try {
    $id_usuario = $_SESSION['id'];

    // Corrigido para usar a tabela 'usuarios' e a coluna 'id_usuarios'
    $stmt = $conn->prepare("SELECT foto_perfil FROM usuarios WHERE id_usuarios = ?");
    $stmt->bind_param("i", $id_usuario); // Assumindo que a sessão 'id' corresponde a 'id_usuarios'
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $foto_data = $row['foto_perfil'] ?? null;

    if ($foto_data) {
        // Determina o tipo MIME da imagem a partir dos dados binários.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->buffer($foto_data);

        // Envia o cabeçalho Content-Type correto antes de enviar a imagem.
        header("Content-Type: " . $mime_type);
        echo $foto_data;
    } else {
        serveDefaultImage(); // Se não houver foto no banco, serve a padrão.
    }
} catch (Exception $e) {
    // Em caso de erro de banco de dados, também serve a imagem padrão.
    serveDefaultImage();
}
?>
