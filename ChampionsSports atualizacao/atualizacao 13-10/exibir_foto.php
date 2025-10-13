<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403); // Forbidden
    exit;
}

$id_usuario = $_SESSION['id_cadastro_usuario'] ?? null;
if (!$id_usuario) {
    http_response_code(400); // Bad Request
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste01;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Busca o BLOB da foto no banco
    $stmt = $pdo->prepare("SELECT foto_perfil FROM cadastro_usuarios WHERE id_cadastro_usuarios = ?");
    $stmt->execute([$id_usuario]);
    $stmt->bindColumn(1, $foto_blob, PDO::PARAM_LOB);
    $stmt->fetch(PDO::FETCH_BOUND);

    if ($foto_blob) {
        header("Content-Type: image/jpeg"); // Assumindo que a maioria será jpeg, o navegador corrige se não for.
        echo $foto_blob;
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
}