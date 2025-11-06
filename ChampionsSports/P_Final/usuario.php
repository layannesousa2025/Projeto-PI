<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

// --- INÍCIO: Bloco de Conexão com o Banco de Dados ---
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "banco_teste01";
$port = 3309; // Porta consistente com o restante do projeto

// Habilita o modo de relatório de exceções para mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("❌ Falha na conexão com o banco de dados: " . $e->getMessage());
}
// --- FIM: Bloco de Conexão ---

$tem_favoritos = false;
if (isset($_SESSION['id_cadastro_usuario'])) {
    try {
        // Verifica se qualquer uma das colunas de categoria é 1 (favoritada)
        $stmt = $conn->prepare("SELECT 1 FROM categoria WHERE id_cadastro_usuarios = ? AND (ciclismo=1 OR futebol=1 OR voleibol=1 OR academia=1 OR caminhada=1 OR natacao=1 OR lazer=1 OR pcd=1) LIMIT 1");
        $stmt->bind_param("i", $_SESSION['id_cadastro_usuario']);
        $stmt->execute();
        
        if ($stmt->get_result()->fetch_assoc()) {
            $tem_favoritos = true;
        }
    } catch (mysqli_sql_exception $e) {
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
        <a href="cadastro.html" class="btn-usuario" id="cadastro">Editar Cadastro</a>
        <?php if ($tem_favoritos): ?>
            <a href="favoritos.php" class="btn-usuario" id="favorito">Meus Favoritos</a>
        <?php endif; ?>
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