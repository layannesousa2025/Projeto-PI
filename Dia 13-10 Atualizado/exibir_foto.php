<?php
session_start();

$id_usuario = $_SESSION['id_usuarios'] ?? $_SESSION['id_cadastro_usuario'] ?? null;
if (!$id_usuario) {
    header("Content-Type: image/png");
    readfile("img/login2.png");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id_usuarios = ?");
    $stmt->execute([$id_usuario]);
    $foto = $stmt->fetchColumn();

    if ($foto) {
        header("Content-Type: image/jpeg");
        echo $foto;
    } else {
        header("Content-Type: image/png");
        readfile("img/login2.png");
    }
} catch (Exception $e) {
    header("Content-Type: image/png");
    readfile("img/login2.png");
}
?>
