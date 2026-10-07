<?php
session_start();

if (empty($_SESSION['csrf_recuperacao'])) {
    $_SESSION['csrf_recuperacao'] = bin2hex(random_bytes(32));
}

$erro = '';
$mensagem = '';
$email = '';
$issuedTokenHash = null;
$issuedUserId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $csrf = $_POST['csrf'] ?? '';

    if (!is_string($csrf) || !hash_equals($_SESSION['csrf_recuperacao'], $csrf)) {
        $erro = 'A solicitação expirou. Atualize a página e tente novamente.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 40) {
        $erro = 'Digite um endereço de e-mail válido.';
    } else {
        $smtpUsername = getenv('SMTP_USERNAME');
        $smtpPassword = getenv('SMTP_PASSWORD');
        $baseUrl = rtrim(getenv('APP_BASE_URL') ?: 'http://localhost:8000', '/');
        $baseUrlParts = parse_url($baseUrl);

        if (
            !extension_loaded('curl') ||
            !$smtpUsername ||
            !$smtpPassword ||
            !filter_var($smtpUsername, FILTER_VALIDATE_EMAIL) ||
            !is_array($baseUrlParts) ||
            !in_array($baseUrlParts['scheme'] ?? '', ['http', 'https'], true) ||
            empty($baseUrlParts['host']) ||
            isset($baseUrlParts['user']) ||
            isset($baseUrlParts['pass'])
        ) {
            $erro = 'O serviço de recuperação de senha está em manutenção no momento. Tente novamente mais tarde.';
        } else {
            try {
                $pdo = new PDO(
                    'mysql:host=127.0.0.1;port=3306;dbname=champions_sport;charset=utf8mb4',
                    'root',
                    '',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                $stmtUser = $pdo->prepare(
                    'SELECT cu.id_cadastro_usuario, cu.nome, cu.email
                     FROM cadastro_usuario cu
                     INNER JOIN login l ON l.id_login = cu.id_login
                     WHERE LOWER(cu.email) = LOWER(?)
                       AND cu.situacao = ? AND l.situacao = ?
                     LIMIT 1'
                );
                $stmtUser->execute([$email, 'A', 'A']);
                $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $stmtRecent = $pdo->prepare(
                        'SELECT COUNT(*) FROM recuperacao_senha
                         WHERE id_cadastro_usuario = ?
                           AND criado_em >= DATE_SUB(NOW(), INTERVAL 1 HOUR)'
                    );
                    $stmtRecent->execute([$user['id_cadastro_usuario']]);
                    $requestsLastHour = (int) $stmtRecent->fetchColumn();

                    if ($requestsLastHour < 5) {
                        $token = bin2hex(random_bytes(32));
                        $tokenHash = hash('sha256', $token);
                        $resetUrl = $baseUrl . '/php/redefinir_senha.php?token=' . urlencode($token);

                        $pdo->beginTransaction();
                        $stmtRevoke = $pdo->prepare(
                            'UPDATE recuperacao_senha
                             SET usado_em = NOW()
                             WHERE id_cadastro_usuario = ? AND usado_em IS NULL'
                        );
                        $stmtRevoke->execute([$user['id_cadastro_usuario']]);

                        $stmtToken = $pdo->prepare(
                            'INSERT INTO recuperacao_senha (id_cadastro_usuario, token_hash, expira_em)
                             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))'
                        );
                        $stmtToken->execute([$user['id_cadastro_usuario'], $tokenHash]);
                        $pdo->commit();
                        $issuedTokenHash = $tokenHash;
                        $issuedUserId = $user['id_cadastro_usuario'];

                        $body = "Olá, {$user['nome']}!\r\n\r\n"
                            . "Recebemos uma solicitação para redefinir a senha da sua conta ChampionsSports.\r\n"
                            . "Acesse o link abaixo para criar uma nova senha (válido por 30 minutos):\r\n\r\n"
                            . "{$resetUrl}\r\n\r\n"
                            . "Se você não solicitou a redefinição, ignore esta mensagem.\r\n";
                        $subject = '=?UTF-8?B?' . base64_encode('Redefinição de senha - ChampionsSports') . '?=';
                        $mail = "Date: " . date(DATE_RFC2822) . "\r\n"
                            . "To: <{$user['email']}>\r\n"
                            . "From: ChampionsSports <{$smtpUsername}>\r\n"
                            . "Subject: {$subject}\r\n"
                            . "MIME-Version: 1.0\r\n"
                            . "Content-Type: text/plain; charset=UTF-8\r\n"
                            . "Content-Transfer-Encoding: 8bit\r\n\r\n"
                            . $body;
                        $mailStream = fopen('php://temp', 'r+');
                        if ($mailStream === false) {
                            throw new RuntimeException('Não foi possível preparar a mensagem de e-mail.');
                        }
                        fwrite($mailStream, $mail);
                        rewind($mailStream);

                        $curl = curl_init('smtps://smtp.gmail.com:465');
                        if ($curl === false) {
                            fclose($mailStream);
                            throw new RuntimeException('Não foi possível iniciar o cliente SMTP.');
                        }

                        curl_setopt_array($curl, [
                            CURLOPT_USERNAME => $smtpUsername,
                            CURLOPT_PASSWORD => $smtpPassword,
                            CURLOPT_MAIL_FROM => $smtpUsername,
                            CURLOPT_MAIL_RCPT => [$user['email']],
                            CURLOPT_UPLOAD => true,
                            CURLOPT_INFILE => $mailStream,
                            CURLOPT_INFILESIZE => strlen($mail),
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT => 25,
                            CURLOPT_SSL_VERIFYPEER => true,
                            CURLOPT_SSL_VERIFYHOST => 2
                        ]);

                        $sent = curl_exec($curl);
                        $smtpError = curl_error($curl);
                        $smtpStatus = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
                        curl_close($curl);
                        fclose($mailStream);

                        if ($sent === false || $smtpStatus < 200 || $smtpStatus >= 300) {
                            error_log('Falha ao enviar e-mail de recuperação via Gmail SMTP: ' . $smtpError . ' (SMTP ' . $smtpStatus . ')');
                            throw new RuntimeException('Não foi possível enviar o e-mail agora. Tente novamente mais tarde.');
                        }
                    }
                }

                $mensagem = 'Se o endereço estiver associado a uma conta ativa, enviaremos um link de redefinição. Verifique sua caixa de entrada e a pasta de spam.';
                $_SESSION['csrf_recuperacao'] = bin2hex(random_bytes(32));
            } catch (Throwable $exception) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                if (isset($pdo) && $issuedTokenHash !== null && $issuedUserId !== null) {
                    try {
                        $stmtInvalidate = $pdo->prepare(
                            'UPDATE recuperacao_senha SET usado_em = NOW()
                             WHERE id_cadastro_usuario = ? AND token_hash = ? AND usado_em IS NULL'
                        );
                        $stmtInvalidate->execute([$issuedUserId, $issuedTokenHash]);
                    } catch (PDOException $cleanupException) {
                        error_log('Não foi possível invalidar token após falha de envio: ' . $cleanupException->getMessage());
                    }
                }
                error_log('Erro na solicitação de redefinição de senha: ' . $exception->getMessage());
                $erro = 'Não foi possível processar sua solicitação agora. Tente novamente mais tarde.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar senha - ChampionsSports</title>
    <?php require_once __DIR__ . '/tailwind.php'; ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#1a1a2e] via-[#2d2d46] to-[#6e00ff] flex items-center justify-center p-4 text-white">
    <main class="w-full max-w-md">
        <a href="login.php" class="mb-6 inline-flex items-center gap-2 text-white/80 transition hover:text-white">
            <span aria-hidden="true" class="text-2xl">&larr;</span>
            <span>Voltar ao login</span>
        </a>

        <section class="rounded-2xl border border-white/10 bg-[#1a1a2e]/90 p-8 shadow-2xl backdrop-blur">
            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-[#ff00aa]/15 text-2xl text-[#ff8bd5]" aria-hidden="true">
                ?
            </div>
            <h1 class="mb-2 text-3xl font-bold">Recuperar senha</h1>
            <p class="mb-6 text-white/70">Informe o e-mail associado à sua conta. Enviaremos um link para definir uma nova senha.</p>

            <?php if ($erro !== ''): ?>
                <p class="mb-5 rounded-lg border border-red-400/40 bg-red-500/15 p-3 text-sm text-red-100" role="alert">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php elseif ($mensagem !== ''): ?>
                <p class="mb-5 rounded-lg border border-green-400/40 bg-green-500/15 p-3 text-sm text-green-100" role="status">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form action="recupera_senha.php" method="POST" class="space-y-5">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_recuperacao'], ENT_QUOTES, 'UTF-8') ?>">
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium">E-mail</label>
                    <input type="email" id="email" name="email" required maxlength="40" autocomplete="email"
                           value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="voce@exemplo.com"
                           class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none transition placeholder:text-white/40 focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                </div>

                <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-[#6e00ff] to-[#ff00aa] px-4 py-3 font-bold transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-white/70">
                    Enviar link de recuperação
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-white/60">
                Lembrou sua senha? <a href="login.php" class="font-semibold text-[#ff8bd5] hover:underline">Entrar</a>
            </p>
        </section>
    </main>
</body>
</html>
