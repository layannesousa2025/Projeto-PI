<?php
session_start();

// 1️⃣ Verificação de login robusta
if (
    !isset($_SESSION['loggedin']) || 
    $_SESSION['loggedin'] !== true || 
    !isset($_SESSION['id'])
) {
    header('Location: Login.html');
    exit;
}

// 2️⃣ Obtém o ID do usuário autenticado
$id_usuario = (int) $_SESSION['id'];

// 3️⃣ Verifica se o arquivo foi enviado corretamente
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {

    $foto_tmp = $_FILES['foto_perfil']['tmp_name'];
    $foto_tamanho = $_FILES['foto_perfil']['size'];
    $foto_tipo = mime_content_type($foto_tmp);

    // 4️⃣ Opcional: validação extra de segurança (tipo e tamanho)
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($foto_tipo, $tipos_permitidos)) {
        die("❌ Tipo de arquivo não permitido. Envie uma imagem JPG, PNG ou GIF.");
    }
    if ($foto_tamanho > 2 * 1024 * 1024) { // 2 MB
        die("❌ O arquivo é muito grande. Máximo permitido: 2 MB.");
    }

    $foto_data = file_get_contents($foto_tmp);

    try {
        // 5️⃣ Conexão com o banco (ajustada)
        $host = "127.0.0.1";
        $dbname = "banco_teste01";
        $port = 3309;
        $user = "root";
        $pass = "";

        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", 
            $user, 
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // 6️⃣ Atualiza a imagem do usuário
        $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = :foto WHERE id_usuarios = :id");
        $stmt->bindParam(':foto', $foto_data, PDO::PARAM_LOB);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // 7️⃣ Redireciona o usuário de volta
        header("Location: usuario.php");
        exit;

    } catch (PDOException $e) {
        // 8️⃣ Tratamento de erros mais informativo
        if ($e->getCode() === 2002) {
            die("❌ Falha na conexão: o servidor MySQL parece estar desligado. Inicie-o no XAMPP e tente novamente.");
        }
        die("❌ Erro ao salvar a imagem: " . htmlspecialchars($e->getMessage()));
    }

} else {
    // 9️⃣ Mostra o erro de upload específico
    $erro = $_FILES['foto_perfil']['error'] ?? 'Desconhecido';
    die("❌ Erro no upload da imagem. Código do erro: $erro");
}
?>
