<?php
session_start();

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

// 2. Pega o ID correto da sessão
$id_usuario = $_SESSION['id_usuarios'] ?? $_SESSION['id_cadastro_usuario'] ?? null;
if (!$id_usuario) {
    die("Erro: ID do usuário não encontrado na sessão.");
}

// 3. Verifica se um arquivo foi enviado e se não houve erros
if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
    die("Nenhuma imagem foi enviada ou ocorreu erro no upload.");
}

// 4. Valida o tamanho máximo da imagem (50MB)
$maxSize = 50 * 1024 * 1024;
if ($_FILES['foto_perfil']['size'] > $maxSize) {
    die("Erro: A imagem é muito grande. Máximo 50MB.");
}

// 5. Define o diretório de uploads e cria se não existir
$uploadDir = 'uploads/perfil/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 6. Cria um nome de arquivo único para evitar conflitos
$ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
$newFileName = 'user_' . $id_usuario . '_' . time() . '.' . $ext;
$destPath = $uploadDir . $newFileName;

// 7. Move o arquivo para o diretório de destino
if (!move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $destPath)) {
    die("Erro ao mover o arquivo para o servidor.");
}

// 8. Atualiza o caminho do arquivo no banco de dados
try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste01;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Atualiza a tabela correta 'cadastro_usuarios' com o caminho do arquivo
    $stmt = $pdo->prepare("UPDATE cadastro_usuarios SET foto_perfil = ? WHERE id_cadastro_usuarios = ?");
    $stmt->execute([$destPath, $id_usuario]);

    // Atualiza o caminho da foto na sessão para que apareça em outras páginas
    $_SESSION['foto_perfil'] = $destPath;

    header('Location: usuario.php');
    exit;
} catch (PDOException $e) {
    die("Erro ao salvar no banco: " . $e->getMessage());
}
?>
?>
