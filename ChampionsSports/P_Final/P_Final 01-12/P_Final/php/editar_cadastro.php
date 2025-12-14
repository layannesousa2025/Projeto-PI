<?php
session_start();
 
// 1. VERIFICAÇÃO DE LOGIN
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header('Location: Login.html');
    exit;
}

// 2. CONEXÃO COM O BANCO DE DADOS (PDO)
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=champions_sport;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em caso de falha na conexão, exibe uma mensagem amigável e encerra.
    // Para produção, considere logar o erro e exibir uma mensagem genérica.
    http_response_code(500);
    echo "❌ Falha na conexão com o banco de dados. Por favor, tente novamente mais tarde.";
    exit;
}

$id_usuario = $_SESSION['id'];
$mensagem_sucesso = '';
$mensagem_erro = '';

// 3. PROCESSAMENTO DO FORMULÁRIO (QUANDO ENVIADO)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e sanitiza os dados do formulário
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $datanascimento = trim($_POST['datanascimento'] ?? '');
    $nova_senha = trim($_POST['nova_senha'] ?? '');

    // Validação básica dos campos obrigatórios
    if (empty($nome) || empty($email) || empty($telefone) || empty($datanascimento)) {
        $mensagem_erro = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        try {
            $pdo->beginTransaction(); // Inicia uma transação para garantir a atomicidade das operações

            // 3.1. Atualiza os dados na tabela 'cadastro_usuario'
            $stmt_update_cadastro = $pdo->prepare("
                UPDATE cadastro_usuario 
                SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :datanascimento
                WHERE id_cadastro_usuario = :id_cadastro_usuario
            ");
            $stmt_update_cadastro->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':telefone' => $telefone,
                ':datanascimento' => $datanascimento,
                ':id_cadastro_usuario' => $id_usuario
            ]);

            // 3.2. Se uma nova senha foi fornecida, atualiza na tabela 'login'
            if (!empty($nova_senha)) {
                if (strlen($nova_senha) < 6) {
                    throw new Exception("A nova senha deve ter pelo menos 6 caracteres.");
                }
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

                // Primeiro, precisamos obter o id_login associado a este id_cadastro_usuario
                $stmt_get_id_login = $pdo->prepare("SELECT id_login FROM cadastro_usuario WHERE id_cadastro_usuario = :id_cadastro_usuario");
                $stmt_get_id_login->execute([':id_cadastro_usuario' => $id_usuario]);
                $id_login = $stmt_get_id_login->fetchColumn();

                if ($id_login) {
                    $stmt_update_login = $pdo->prepare("UPDATE login SET senha = :senha WHERE id_login = :id_login");
                    $stmt_update_login->execute([
                        ':senha' => $senha_hash,
                        ':id_login' => $id_login
                    ]);
                } else {
                    throw new Exception("Não foi possível encontrar o registro de login associado.");
                }
            }

            $pdo->commit(); // Confirma as alterações no banco de dados
            
            $_SESSION['nome'] = $nome; // Atualiza o nome na sessão, se foi alterado
            $mensagem_sucesso = 'Cadastro atualizado com sucesso!';
            // Redireciona para a página de usuário após um curto período
            header("Refresh: 2; url=usuario.php");
        } catch (Exception $e) {
            // Captura erros de validação ou do banco de dados
            $mensagem_erro = 'Erro ao atualizar o cadastro: ' . $e->getMessage();
        }
    }
}

// 4. BUSCA OS DADOS ATUAIS DO USUÁRIO PARA EXIBIR NO FORMULÁRIO
// CORREÇÃO: A tabela correta é 'cadastro_usuario' e a coluna da data é 'data_nascimento'.
try {
    $stmt = $pdo->prepare("SELECT nome, email, cpf, data_nascimento, telefone FROM cadastro_usuario WHERE id_cadastro_usuario = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // Se o usuário não for encontrado por algum motivo, desloga e redireciona
        // CORREÇÃO: Usar http_response_code para indicar o erro antes de redirecionar.
        http_response_code(404); // Not Found
        header('Location: logout.php');
        exit;
    }
    // CORREÇÃO: Ajustar o nome da chave para 'datanascimento' para o HTML, se necessário.
} catch (PDOException $e) {
    die("Erro ao buscar dados do usuário: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cadastro</title>
    <link rel="stylesheet" href="../Css/editar_cadastro.css">
</head>
<body>
    <div class="voltar" onclick="window.location.href='../index.php'">
        <a href="../index.php"><img src="../img/button (2).png" alt="Botão voltar"></a>
    </div>

    <form action="editar_cadastro.php" method="POST" class="form-container">
        <h1>Editar Cadastro</h1>

        <?php if ($mensagem_sucesso): ?>
            <div class="mensagem sucesso"><?php echo htmlspecialchars($mensagem_sucesso); ?></div>
        <?php endif; ?>
        <?php if ($mensagem_erro): ?>
            <div class="mensagem erro"><?php echo htmlspecialchars($mensagem_erro); ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="cpf">CPF (não pode ser alterado)</label>
            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($usuario['cpf']); ?>" disabled>
        </div>

        <div class="form-group">
            <label for="datanascimento">Data de Nascimento</label>
            <input type="date" id="datanascimento" name="datanascimento" value="<?php echo htmlspecialchars($usuario['data_nascimento']); ?>" required>
        </div>

        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>" required>
        </div>

        <div class="form-group">
            <label for="nova_senha">Nova Senha (deixe em branco para não alterar)</label>
            <input type="password" id="nova_senha" name="nova_senha" placeholder="Mínimo 6 caracteres">
        </div>

        <button type="submit" class="submit-btn">Salvar Alterações</button>
    </form>

</body>
</html>