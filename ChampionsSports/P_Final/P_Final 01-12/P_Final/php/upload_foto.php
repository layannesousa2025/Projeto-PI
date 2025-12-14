<?php
session_start();

// 1. Verificação de login, garantindo que o ID do usuário exista na sessão.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header('Location: Login.html');
    exit;
}


// Inclui o arquivo de conexão com o banco de dados
require_once "conexao.php";

// Habilita o modo de relatório de exceções para mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("❌ Falha na conexão com o banco de dados: " . $e->getMessage());
}
// --- FIM: Bloco de Conexão ---

// 2. Pega o ID do usuário da sessão.
$id_usuario = $_SESSION['id'];

// 3. Validações de segurança do arquivo enviado.
if (isset($_FILES['foto_perfil'])) {
    $file = $_FILES['foto_perfil'];

    // Verifica se houve algum erro no upload.
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Erro no upload da imagem. Código do erro: " . $file['error']);
    }

    // Define o tamanho máximo do arquivo (ex: 5MB).
    $max_file_size = 5 * 1024 * 1024; // 5 MB em bytes.
    if ($file['size'] > $max_file_size) {
        die("Erro: O arquivo é muito grande. O tamanho máximo permitido é 5MB.");
    }

    // Verifica se o tipo de arquivo é uma imagem válida.
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);

    if (!in_array($mime_type, $allowed_types)) {
        die("Erro: Tipo de arquivo inválido. Apenas imagens JPEG, PNG, GIF e WebP são permitidas.");
    }

    // 4. Lê o conteúdo do arquivo e salva no banco de dados.
    $foto_data = file_get_contents($file['tmp_name']);

    if ($foto_data === false) {
        die("Erro ao ler o arquivo da imagem.");
    }

    try {
        // Prepara a query para atualizar a foto do perfil.
        // CORREÇÃO: A tabela correta é 'cadastro_usuario' e a coluna de ID é 'id_cadastro_usuario'.
        // A coluna da foto na tabela é 'foto_usuario', não 'foto_perfil'.
        $stmt = $conn->prepare("UPDATE cadastro_usuario SET foto_usuario = ? WHERE id_cadastro_usuario = ?");

        // O tipo 'b' para BLOB (Binary Large Object) é a forma correta de enviar dados binários.
        $null = NULL; // Variável para bind_param
        $stmt->bind_param("bi", $null, $id_usuario);
        $stmt->send_long_data(0, $foto_data); // Envia os dados binários
        
        $stmt->execute();
        $stmt->close();

        // 5. Redireciona de volta para a página do usuário.
        header("Location: usuario.php?upload=success");
        exit;
    } catch (mysqli_sql_exception $e) {
        die("Erro ao salvar a imagem no banco de dados: " . $e->getMessage());
    }
} else {
    die("Nenhum arquivo foi enviado.");
}
?>