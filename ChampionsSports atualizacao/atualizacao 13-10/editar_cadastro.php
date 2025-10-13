<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

$id_usuario = $_SESSION['id_cadastro_usuario'] ?? null;
if (!$id_usuario) {
    die("Erro: ID do usuário não encontrado.");
}

$usuario = [];
$acessibilidade = [];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste01;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Busca dados do usuário
    $stmt_user = $pdo->prepare("SELECT * FROM cadastro_usuarios WHERE id_cadastro_usuarios = ?");
    $stmt_user->execute([$id_usuario]);
    $usuario = $stmt_user->fetch(PDO::FETCH_ASSOC);

    // Busca dados de acessibilidade
    $stmt_access = $pdo->prepare("SELECT * FROM acessibilidade WHERE id_cadastro_usuarios = ?");
    $stmt_access->execute([$id_usuario]);
    $acessibilidade = $stmt_access->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Erro ao buscar dados do usuário: " . $e->getMessage());
}

if (!$usuario) {
    die("Usuário não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cadastro</title>
    <link rel="stylesheet" href="Css/cadastro.css">
</head>
<style>
    .deficiencia-opcoes { display: none; }
    .deficiencia-opcoes.show { display: block; }
</style>

<body>
    <div class="voltar">
        <a href="usuario.php"><img src="./img/button (2).png" alt="Botão voltar"></a>
    </div>

    <!-- O action aponta para um novo script que irá processar a atualização -->
    <form action="atualizar_cadastro.php" id="cadastroForm" method="POST">
        <h1>Editar Cadastro</h1>
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" required placeholder="Digite seu nome completo" minlength="2" value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" required placeholder="Digite seu CPF" value="<?php echo htmlspecialchars($usuario['cpf'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="datanascimento">Data de Nascimento</label>
            <input type="date" id="datanascimento" name="datanascimento" required value="<?php echo htmlspecialchars($usuario['data_nascimento'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" required placeholder="(xx) xxxxx-xxxx" value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required placeholder="Digite seu e-mail" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="senha">Nova Senha (deixe em branco para não alterar)</label>
            <input type="password" id="senha" name="senha" placeholder="Digite a nova senha" minlength="6">
        </div>

        <div class="form-group">
            <label for="confirmarSenha">Confirmar Nova Senha</label>
            <input type="password" id="confirmarSenha" name="confirmarSenha" placeholder="Confirme a nova senha">
        </div>

        <?php $tem_deficiencia = !empty(array_filter(array_slice($acessibilidade, 2))); ?>
        <div class="checkbox-group">
            <p>Possui alguma deficiência? *</p>
            <div class="option">
                <input type="radio" id="deficiencia_sim" name="deficiencia" value="sim" required <?php echo $tem_deficiencia ? 'checked' : ''; ?>>
                <label for="deficiencia_sim">Sim</label>
            </div>
            <div class="option">
                <input type="radio" id="deficiencia_nao" name="deficiencia" value="nao" <?php echo !$tem_deficiencia ? 'checked' : ''; ?>>
                <label for="deficiencia_nao">Não</label>
            </div>
        </div>

        <div class="deficiencia-opcoes <?php echo $tem_deficiencia ? 'show' : ''; ?>" id="deficienciaOpcoesContainer">
            <p>Selecione o(s) tipo(s) de deficiência:</p>
            <div class="checkbox-group">
                <input type="checkbox" id="deficiencia_visual" name="deficiencia_visual" <?php echo ($acessibilidade['deficiencia_visual'] ?? 0) ? 'checked' : ''; ?>>
                <label for="deficiencia_visual">Deficiência Visual</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="deficiencia_auditiva" name="deficiencia_auditiva" <?php echo ($acessibilidade['deficiencia_auditiva'] ?? 0) ? 'checked' : ''; ?>>
                <label for="deficiencia_auditiva">Deficiência Auditiva</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="deficiencia_motora" name="deficiencia_motora" <?php echo ($acessibilidade['deficiencia_motora'] ?? 0) ? 'checked' : ''; ?>>
                <label for="deficiencia_motora">Deficiência Motora</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="deficiencia_cardiaca" name="deficiencia_cardiaca" <?php echo ($acessibilidade['deficiencia_cardiaca'] ?? 0) ? 'checked' : ''; ?>>
                <label for="deficiencia_cardiaca">Deficiência Cardíaca</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="deficiencia_intelectual" name="deficiencia_intelectual" <?php echo ($acessibilidade['deficiencia_intelectual'] ?? 0) ? 'checked' : ''; ?>>
                <label for="deficiencia_intelectual">Deficiência Intelectual</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="deficiencia_neurologica" name="deficiencia_neurologica" <?php echo ($acessibilidade['deficiencia_neurologica'] ?? 0) ? 'checked' : ''; ?>>
                <label for="deficiencia_neurologica">Deficiência Neurológica</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="deficiencia_psiquica" name="deficiencia_psiquica" <?php echo ($acessibilidade['deficiencia_psiquica'] ?? 0) ? 'checked' : ''; ?>>
                <label for="deficiencia_psiquica">Deficiência Psíquica</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="deficiencia_outros" name="deficiencia_outros" <?php echo ($acessibilidade['outros'] ?? 0) ? 'checked' : ''; ?>>
                <label for="deficiencia_outros">Outros</label>
            </div>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
            <p>Salvar Alterações</p>
        </button>
    </form>

    <script>
        const simRadio = document.getElementById('deficiencia_sim');
        const naoRadio = document.getElementById('deficiencia_nao');
        const deficienciaOpcoesContainer = document.getElementById('deficienciaOpcoesContainer');

        simRadio.addEventListener('change', function () {
            if (this.checked) {
                deficienciaOpcoesContainer.classList.add('show');
            }
        });

        naoRadio.addEventListener('change', function () {
            if (this.checked) {
                deficienciaOpcoesContainer.classList.remove('show');
            }
        });
    </script>
</body>

</html>