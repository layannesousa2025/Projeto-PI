<?php
session_start();

/**
 * Envia uma imagem padrão e encerra o script.
 * @param string $filePath O caminho para o arquivo de imagem padrão.
 */
function serveDefaultImage($filePath = 'img/login2.png') {
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

try {
    // 2. Conecta ao banco de dados.
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste01;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Usa a variável de sessão correta: $_SESSION['id']
    $id_usuario = $_SESSION['id'];

    $stmt = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id_usuarios = ?");
    $stmt->execute([$id_usuario]);
    $foto_data = $stmt->fetchColumn(); // Pega os dados binários da imagem.

    if ($foto_data) {
        // 4. Detecta o tipo MIME da imagem e a exibe.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        header("Content-Type: " . $finfo->buffer($foto_data));
        echo $foto_data;
    } else {
        serveDefaultImage(); // Se não houver foto no banco, serve a padrão.
    }
} catch (Exception $e) {
    // Em caso de erro de banco de dados, também serve a imagem padrão.
    serveDefaultImage();
}
?>
