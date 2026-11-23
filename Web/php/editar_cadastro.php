<?php
session_start();
 
// 1. VERIFICAÇÃO DE LOGIN
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// 2. CONEXÃO COM O BANCO DE DADOS (PDO)
try {
    require_once __DIR__ . '/database.php';
    $pdo = champions_pdo();
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
    <?php require_once __DIR__ . '/tailwind.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar cadastro - ChampionsSports</title>
    <style>
        :root {
            color-scheme: dark;
            --page-bg: #11111f;
            --surface: #1d1d31;
            --surface-soft: #26263d;
            --text: #f8f9fa;
            --muted: #b8b8c9;
            --primary: #6e00ff;
            --secondary: #ff00aa;
        }

        * { box-sizing: border-box; }
        html { min-width: 320px; }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 34px 16px;
            background:
                radial-gradient(ellipse at 80% 8%, rgba(110, 0, 255, .2), transparent 38rem),
                var(--page-bg);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-wrap { width: min(100%, 620px); margin: 0 auto; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 20px 4px;
            color: var(--muted);
            font-size: .92rem;
            text-decoration: none;
            transition: color .2s ease;
        }

        .back-link:hover { color: #ff8bd5; }

        .form-container {
            padding: clamp(24px, 6vw, 42px);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 22px;
            background: linear-gradient(145deg, rgba(38, 38, 61, .98), rgba(29, 29, 49, .98));
            box-shadow: 0 24px 70px rgba(0, 0, 0, .36);
        }

        .form-container h1 {
            margin: 0 0 8px;
            font-size: clamp(1.8rem, 5vw, 2.3rem);
            line-height: 1.2;
            letter-spacing: -.035em;
        }

        .form-intro { margin: 0 0 26px; color: var(--muted); line-height: 1.6; }
        .form-group { display: grid; gap: 8px; margin-bottom: 19px; }

        .form-group label { color: #e8e8f1; font-size: .92rem; font-weight: 600; }

        .form-group input {
            width: 100%;
            min-height: 48px;
            padding: 11px 14px;
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 10px;
            outline: none;
            background: rgba(255, 255, 255, .07);
            color: var(--text);
            font: inherit;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .form-group input:focus { border-color: var(--secondary); background: rgba(255, 255, 255, .1); box-shadow: 0 0 0 3px rgba(255, 0, 170, .16); }
        .form-group input:disabled { border-color: rgba(255, 255, 255, .08); background: rgba(255, 255, 255, .035); color: #a7a7b8; cursor: not-allowed; }
        .form-group input::placeholder { color: #9999ae; }

        .form-hint { margin: -12px 0 19px; color: var(--muted); font-size: .84rem; line-height: 1.5; }

        .mensagem {
            margin: 0 0 20px;
            padding: 13px 15px;
            border: 1px solid transparent;
            border-radius: 10px;
            font-size: .92rem;
            line-height: 1.5;
        }

        .mensagem.sucesso { border-color: rgba(74, 222, 128, .35); background: rgba(34, 197, 94, .1); color: #bbf7d0; }
        .mensagem.erro { border-color: rgba(248, 113, 113, .35); background: rgba(239, 68, 68, .1); color: #fecaca; }

        .submit-btn {
            display: block;
            width: 100%;
            min-height: 50px;
            margin-top: 6px;
            padding: 12px 18px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(110deg, var(--primary), var(--secondary));
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: filter .2s ease, transform .2s ease;
        }

        .submit-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .submit-btn:focus-visible, .back-link:focus-visible { outline: 3px solid rgba(255, 0, 170, .55); outline-offset: 3px; }

        @media (max-width: 420px) {
            body { padding: 22px 12px; }
            .form-container { border-radius: 17px; }
        }
    </style>
</head>
<body>
    <main class="page-wrap">
        <a href="usuario.php" class="back-link">
            <span aria-hidden="true">&larr;</span>
            Voltar ao perfil
        </a>

    <form action="editar_cadastro.php" method="POST" class="form-container">
        <h1>Editar Cadastro</h1>
        <p class="form-intro">Atualize seus dados. Deixe a senha em branco se não quiser alterá-la.</p>

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
            <input type="password" id="nova_senha" name="nova_senha" placeholder="Mínimo 6 caracteres" minlength="6" autocomplete="new-password">
        </div>

        <button type="submit" class="submit-btn">Salvar Alterações</button>
    </form>
    </main>

</body>
</html>