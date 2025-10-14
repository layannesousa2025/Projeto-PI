<?php
session_start();

// 1. Verificação de login mais robusta, garantindo que o ID principal exista.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header('Location: Login.html');
    exit;
}

// 2. Usa a variável de sessão correta para obter o ID do usuário.
$id_usuario = $_SESSION['id'];

if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $foto_tmp = $_FILES['foto_perfil']['tmp_name'];
    $foto_data = file_get_contents($foto_tmp); // Lê o binário da imagem

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=banco_teste01;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id_usuarios = ?");
        $stmt->bindParam(1, $foto_data, PDO::PARAM_LOB);
        $stmt->bindParam(2, $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: usuario.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao salvar imagem: " . $e->getMessage());
    }
} else {
    die("Erro no upload da imagem.");
}
?>
