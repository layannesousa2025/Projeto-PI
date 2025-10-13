<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

$id_usuario = $_SESSION['id_usuarios'] ?? $_SESSION['id_cadastro_usuario'] ?? null;
if (!$id_usuario) {
    die("Erro: ID do usuário não encontrado.");
}

if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $foto_tmp = $_FILES['foto_perfil']['tmp_name'];
    $foto_data = file_get_contents($foto_tmp); // Lê o binário da imagem

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=banco_teste;charset=utf8", "root", "");
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
