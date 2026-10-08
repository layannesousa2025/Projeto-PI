<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['nome'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($usuario === '' || $senha === '') {
        header("Location: login.php?error=empty_fields");
        exit;
    }

    require_once __DIR__ . "/conexao.php";

    $stmt = $conn->prepare("
        SELECT
            l.id_login,
            cu.id_cadastro_usuario,
            cu.nome,
            l.senha,
            l.tipo_usuario,
            l.situacao,
            l.usuario AS login_usuario
        FROM login l
        LEFT JOIN cadastro_usuario cu ON l.id_login = cu.id_login
        WHERE l.usuario = ? OR cu.nome = ?
        LIMIT 1
    ");
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result(
            $id_login,
            $id_cadastro_usuario,
            $nome_cadastro,
            $senha_hash,
            $tipo_usuario,
            $situacao,
            $login_usuario
        );
        $stmt->fetch();

        if (password_verify($senha, $senha_hash ?? '') && $situacao === 'A') {
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            $_SESSION['tipo_usuario'] = $tipo_usuario;

            if ($tipo_usuario === 'Admin') {
                $_SESSION['id'] = $id_login;
                $_SESSION['nome'] = $login_usuario;
            } else {
                $_SESSION['id'] = $id_cadastro_usuario;
                $_SESSION['nome'] = $nome_cadastro;
                $_SESSION['id_cadastro_usuario'] = $id_cadastro_usuario;
            }

            $stmt->close();
            $conn->close();
            header("Location: ../index.php");
            exit;
        }
    }

    $stmt->close();
    $conn->close();
    header("Location: login.php?error=invalid_credentials");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ChampionsSports</title>
    <?php require_once __DIR__ . '/tailwind.php'; ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#1a1a2e] via-[#2d2d46] to-[#6e00ff] flex items-center justify-center p-4 text-white">
    <main class="w-full max-w-md">
        <a href="../index.php" class="mb-6 inline-flex items-center gap-2 text-white/80 transition hover:text-white" aria-label="Voltar para a página inicial">
            <span aria-hidden="true" class="text-2xl">&larr;</span>
            <span>Voltar ao início</span>
        </a>

        <section class="rounded-2xl border border-white/10 bg-[#1a1a2e]/90 p-8 shadow-2xl backdrop-blur">
            <h1 class="mb-2 text-3xl font-bold">Login de usuário</h1>
            <p class="mb-6 text-white/70">Acesse sua conta ChampionsSports.</p>

            <?php
            $mensagens = [
                'invalid_credentials' => 'Usuário ou senha incorretos. Verifique os dados e tente novamente.',
                'empty_fields' => 'Preencha o usuário e a senha para continuar.',
                'inactive_user' => 'Esta conta está desativada. Entre em contato com o suporte.'
            ];
            $erro = $_GET['error'] ?? '';
            if (isset($mensagens[$erro])):
            ?>
                <p class="mb-5 rounded-lg border border-red-400/40 bg-red-500/15 p-3 text-sm text-red-100" role="alert">
                    <?= htmlspecialchars($mensagens[$erro], ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-5">
                <div>
                    <label for="nome" class="mb-2 block text-sm font-medium">Nome de usuário ou e-mail</label>
                    <input type="text" name="nome" id="nome" required autocomplete="username"
                           class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none transition placeholder:text-white/40 focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                </div>

                <div>
                    <label for="senha" class="mb-2 block text-sm font-medium">Senha</label>
                    <input type="password" name="senha" id="senha" required autocomplete="current-password"
                           class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none transition placeholder:text-white/40 focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                </div>

                <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-[#6e00ff] to-[#ff00aa] px-4 py-3 font-bold transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-white/70">
                    Entrar
                </button>
            </form>

            <div class="mt-6 flex flex-col gap-3 text-sm">
                <a href="cadastro_usuario.php" class="text-[#ff8bd5] hover:underline">Criar conta</a>
                <a href="recupera_senha.php" class="text-[#ff8bd5] hover:underline">Esqueceu a senha?</a>
            </div>
        </section>
    </main>
</body>
</html>