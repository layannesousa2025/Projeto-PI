<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

$tem_favoritos = false;
if (isset($_SESSION['id_cadastro_usuario'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=banco_teste;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verifica se qualquer uma das colunas de categoria é 1 (favoritada)
        $stmt = $pdo->prepare("SELECT 1 FROM categoria WHERE id_cadastro_usuarios = ? AND (ciclismo=1 OR futebol=1 OR voleibol=1 OR academia=1 OR caminhada=1 OR natacao=1 OR lazer=1 OR pcd=1) LIMIT 1");
        $stmt->execute([$_SESSION['id_cadastro_usuario']]);
        
        if ($stmt->fetchColumn()) {
            $tem_favoritos = true;
        }
    } catch (Exception $e) {
        // Em caso de erro, assume que não há favoritos para exibir o botão
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela-Usuario</title>
    <link rel="stylesheet" href="Css/usuario.css">
</head>

<body>
       

    <div class="perfil-usuario">
        <img src="exibir_foto.php" alt="Foto do usuário" id="foto-perfil">
        <p><?php echo htmlspecialchars($_SESSION['nome']); ?></p>
    </div>

    <!-- Adiciona um input de arquivo escondido e um label para ativá-lo -->
    <form id="form-foto" action="upload_foto.php" method="post" enctype="multipart/form-data" style="text-align: center;">
        <label for="input-foto" class="altera foto">
            <p>Alterar Foto</p>
        </label>
        <input type="file" id="input-foto" name="foto_perfil" accept="image/*" style="display: none;">
        <!-- <button type="submit" style="display: none;">Salvar</button> --> <!-- Botão para salvar no futuro -->
    </form>

    <div class="botoes">
        <a href="cadastro.html">
            <input type="button" id="cadastro" value="Editar Cadastro">
        </a>
        <?php if ($tem_favoritos): ?>
        <a href="favoritos.php">
            <input type="button" id="favorito" value="Meus Favoritos">
        </a>
        <?php endif; ?>
        <a href="contato.html">
            <input type="button" id="contato" value="Adicionar Contato">
        </a>
        <a href="logout.php">
            <input type="button" id="sair" value="Sair">
        </a>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputFoto = document.getElementById('input-foto');
            const formFoto = document.getElementById('form-foto');

            // Quando um arquivo for selecionado no input
            inputFoto.addEventListener('change', function(event) {
                const file = event.target.files[0];

                if (file) {
                    // Se um arquivo foi selecionado, envia o formulário automaticamente.
                    // A página será recarregada pelo 'upload_foto.php' e a nova imagem aparecerá.
                    formFoto.submit();
                }
            });
        });
    </script>
</body>

</html>