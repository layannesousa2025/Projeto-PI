<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

$tem_favoritos = false;
// O ID principal do usuário agora é 'id', que corresponde a 'id_usuarios'
if (isset($_SESSION['id'])) {
    try {
        // Conexão consistente com o restante do projeto
        $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=champions_sport;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verifica se qualquer uma das colunas de categoria é 1 (favoritada)
        // CORREÇÃO: A coluna é 'id_cadastro_usuario' (singular) e usamos o ID da sessão.
        // Usar sempre $_SESSION['id'] que é o ID principal da tabela 'usuarios'.
        $stmt = $pdo->prepare("SELECT 1 FROM categoria WHERE id_cadastro_usuario = ? AND (ciclismo=1 OR futebol=1 OR voleibol=1 OR academia=1 OR caminhada=1 OR natacao=1 OR lazer=1 OR pcd=1) LIMIT 1");
        $stmt->execute([$_SESSION['id']]);
        
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
    <div class="voltar">
        <a href="telaPrincipal.php"><img src="./img/button (2).png" alt="Botão voltar"></a>
    </div>


    <div class="perfil-usuario">
        <!-- Adiciona um parâmetro 'v' com o timestamp atual para evitar cache do navegador -->
        <img src="exibir_foto.php?v=<?php echo time(); ?>" alt="Foto do usuário" id="foto-perfil">
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
        <!-- Botões atualizados para usar a classe CSS e serem consistentes -->
        <a href="telaPrincipal.php" class="btn-usuario" id="tela">Tela Principal</a>
        <!-- O ideal é ter uma página de edição separada, não a de cadastro -->
        <a href="editar_cadastro.php" class="btn-usuario" id="cadastro">Editar Cadastro</a>
        <!-- O botão agora é exibido permanentemente -->
        <a href="favoritos.php" class="btn-usuario" id="favorito">Meus Favoritos</a>

        <a href="contato.php" class="btn-usuario" id="contato">Adicionar Contato</a>
        <a href="logout.php" class="btn-usuario" id="sair">Sair</a>
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