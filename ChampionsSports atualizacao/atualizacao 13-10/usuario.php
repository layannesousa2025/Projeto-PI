<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

// ID do usuário
$id_usuario = $_SESSION['id_usuarios'] ?? $_SESSION['id_cadastro_usuario'] ?? null;
if (!$id_usuario) {
    die("Erro: ID do usuário não encontrado na sessão.");
}

$tem_favoritos = false;
$foto_perfil = 'img/login2.png'; // Define a imagem de perfil como a padrão

try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste01;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o usuário tem favoritos
    $stmt = $pdo->prepare("SELECT 1 FROM categoria WHERE id_cadastro_usuarios = ? AND 
    (ciclismo=1 OR futebol=1 OR voleibol=1 OR academia=1 OR caminhada=1 OR natacao=1 OR lazer=1 OR pcd=1) LIMIT 1");
    $stmt->execute([$id_usuario]);
    if ($stmt->fetchColumn()) {
        $tem_favoritos = true;
    }

    // (REATIVADO) Busca a foto do usuário no banco de dados
    $stmt_foto = $pdo->prepare("SELECT foto_perfil FROM cadastro_usuarios WHERE id_cadastro_usuarios = ?");
    $stmt_foto->execute([$id_usuario]);
    $resultado_foto = $stmt_foto->fetchColumn();
    // Verifica se há um caminho de foto e se o arquivo existe
    if ($resultado_foto && file_exists($resultado_foto)) {
        $foto_perfil = $resultado_foto;
    }

} catch (Exception $e) {
    // Caso ocorra erro, mantém imagem padrão e sem favoritos
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="Css/usuario.css">
</head>
<body>

<div class="perfil-usuario">
    <img src="<?php echo htmlspecialchars($foto_perfil); ?>" 
         alt="Foto do usuário" id="foto-perfil">
    <p><?php echo htmlspecialchars($_SESSION['nome']); ?></p>
</div>

<!-- (REATIVADO) Formulário para alterar a foto -->
<form id="form-foto" action="upload_foto.php" method="post" enctype="multipart/form-data">
   <label for="input-foto" class="altera foto">
       <p>Alterar Foto</p>
   </label>
   <input type="file" id="input-foto" name="foto_perfil" accept="image/*" style="display: none;">
</form>

<div class="botoes">
    <a href="editar_cadastro.php"><input type="button" id="cadastro" value="Editar Cadastro"></a>
    <?php if ($tem_favoritos): ?>
    <a href="favoritos.php"><input type="button" id="favorito" value="Meus Favoritos"></a>
    <?php endif; ?>
    <a href="contato.html"><input type="button" id="contato" value="Adicionar Contato"></a>
    <a href="logout.php"><input type="button" id="sair" value="Sair"></a>
</div>

<!-- (REATIVADO) Script para upload automático -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputFoto = document.getElementById('input-foto');
    const imgPerfil = document.getElementById('foto-perfil');

    // Ao selecionar uma imagem, atualiza a foto na tela e envia o formulário
    inputFoto.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            imgPerfil.src = URL.createObjectURL(file); // Mostra um preview da imagem
            document.getElementById('form-foto').submit(); // Envia para o servidor
        }
    });
});
</script>

</body>
</html>
