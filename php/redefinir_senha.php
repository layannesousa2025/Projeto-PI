<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex, nofollow');

$token = $_POST['token'] ?? $_GET['token'] ?? '';
$tokenValidoNoFormato = is_string($token) && preg_match('/\A[a-f0-9]{64}\z/', $token) === 1;
$erro = '';
$sucesso = false;
$registroToken = null;

if (empty($_SESSION['csrf_redefinir'])) {
    $_SESSION['csrf_redefinir'] = bin2hex(random_bytes(32));
}

if ($tokenValidoNoFormato) {
    try {
        require_once __DIR__ . '/database.php';
        $pdo = champions_pdo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirmarSenha = $_POST['confirmar_senha'] ?? '';

            if (!is_string($csrf) || !hash_equals($_SESSION['csrf_redefinir'], $csrf)) {
                $erro = 'A solicitação expirou. Abra novamente o link recebido por e-mail.';
            } elseif (!is_string($senha) || strlen($senha) < 6) {
                $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
            } elseif ($senha !== $confirmarSenha) {
                $erro = 'As senhas não coincidem.';
            } else {
                $pdo->beginTransaction();
                $stmtToken = $pdo->prepare(
                    'SELECT id_recuperacao, id_cadastro_usuario
                     FROM recuperacao_senha
                     WHERE token_hash = ? AND usado_em IS NULL AND expira_em > NOW()
                     LIMIT 1 FOR UPDATE'
                );
                $stmtToken->execute([hash('sha256', $token)]);
                $registroToken = $stmtToken->fetch(PDO::FETCH_ASSOC);

                if (!$registroToken) {
                    $pdo->rollBack();
                    $erro = 'Este link de redefinição é inválido, expirou ou já foi utilizado. Solicite um novo link.';
                } else {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmtSenha = $pdo->prepare(
                        'UPDATE login l
                         INNER JOIN cadastro_usuario cu ON cu.id_login = l.id_login
                         SET l.senha = ?
                         WHERE cu.id_cadastro_usuario = ? AND cu.situacao = ? AND l.situacao = ?'
                    );
                    $stmtSenha->execute([$senhaHash, $registroToken['id_cadastro_usuario'], 'A', 'A']);

                    if ($stmtSenha->rowCount() !== 1) {
                        throw new RuntimeException('Não foi possível atualizar a senha da conta.');
                    }

                    $stmtConsumir = $pdo->prepare(
                        'UPDATE recuperacao_senha SET usado_em = NOW()
                         WHERE id_recuperacao = ? AND usado_em IS NULL'
                    );
                    $stmtConsumir->execute([$registroToken['id_recuperacao']]);
                    if ($stmtConsumir->rowCount() !== 1) {
                        throw new RuntimeException('Não foi possível concluir a redefinição da senha.');
                    }

                    $pdo->commit();
                    $sucesso = true;
                    $_SESSION['csrf_redefinir'] = bin2hex(random_bytes(32));
                }
            }
        } else {
            $stmtToken = $pdo->prepare(
                'SELECT id_recuperacao
                 FROM recuperacao_senha
                 WHERE token_hash = ? AND usado_em IS NULL AND expira_em > NOW()
                 LIMIT 1'
            );
            $stmtToken->execute([hash('sha256', $token)]);
            if ($stmtToken->fetchColumn() === false) {
                $erro = 'Este link de redefinição é inválido, expirou ou já foi utilizado. Solicite um novo link.';
            }
        }
    } catch (Throwable $exception) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Erro ao redefinir senha: ' . $exception->getMessage());
        $erro = 'Não foi possível redefinir a senha agora. Tente novamente mais tarde.';
    }
} else {
    $erro = 'O link de redefinição está inválido. Solicite um novo link.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir nova senha - ChampionsSports</title>
    <?php require_once __DIR__ . '/tailwind.php'; ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#1a1a2e] via-[#2d2d46] to-[#6e00ff] flex items-center justify-center p-4 text-white">
    <main class="w-full max-w-md">
        <section class="rounded-2xl border border-white/10 bg-[#1a1a2e]/90 p-8 shadow-2xl backdrop-blur">
            <h1 class="mb-2 text-3xl font-bold">Definir nova senha</h1>

            <?php if ($sucesso): ?>
                <p class="my-6 rounded-lg border border-green-400/40 bg-green-500/15 p-4 text-sm text-green-100" role="status">
                    Sua senha foi atualizada. Agora você já pode entrar com a nova senha.
                </p>
                <a href="login.php" class="block w-full rounded-lg bg-gradient-to-r from-[#6e00ff] to-[#ff00aa] px-4 py-3 text-center font-bold text-white no-underline transition hover:brightness-110">
                    Ir para o login
                </a>
            <?php else: ?>
                <p class="mb-6 text-white/70">Escolha uma nova senha com pelo menos 6 caracteres.</p>

                <?php if ($erro !== ''): ?>
                    <p class="mb-5 rounded-lg border border-red-400/40 bg-red-500/15 p-3 text-sm text-red-100" role="alert">
                        <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <?php if ($tokenValidoNoFormato && ($erro === '' || str_starts_with($erro, 'A nova senha') || str_starts_with($erro, 'As senhas'))): ?>
                    <form action="redefinir_senha.php" method="POST" class="space-y-5">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_redefinir'], ENT_QUOTES, 'UTF-8') ?>">
                        <div>
                            <label for="senha" class="mb-2 block text-sm font-medium">Nova senha</label>
                            <input type="password" id="senha" name="senha" required minlength="6" autocomplete="new-password"
                                   class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                        </div>
                        <div>
                            <label for="confirmar_senha" class="mb-2 block text-sm font-medium">Confirme a nova senha</label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6" autocomplete="new-password"
                                   class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-[#6e00ff] to-[#ff00aa] px-4 py-3 font-bold transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-white/70">
                            Salvar nova senha
                        </button>
                    </form>
                <?php else: ?>
                    <a href="recupera_senha.php" class="font-semibold text-[#ff8bd5] hover:underline">Solicitar outro link</a>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
