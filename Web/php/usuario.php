<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$nomeUsuario = htmlspecialchars($_SESSION['nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');
$uploadStatus = $_GET['upload'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil - ChampionsSports</title>
    <?php require_once __DIR__ . '/tailwind.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            display: grid;
            min-height: 100vh;
            place-items: center;
            margin: 0;
            padding: 32px 16px;
            background:
                radial-gradient(ellipse at 80% 8%, rgba(110, 0, 255, .2), transparent 38rem),
                var(--page-bg);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .profile-card {
            width: min(100%, 520px);
            padding: clamp(24px, 6vw, 42px);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 24px;
            background: linear-gradient(145deg, rgba(38, 38, 61, .98), rgba(29, 29, 49, .98));
            box-shadow: 0 24px 70px rgba(0, 0, 0, .38);
            text-align: center;
        }

        .back-link {
            display: flex;
            width: fit-content;
            align-items: center;
            gap: 8px;
            margin: 0 0 24px;
            color: var(--muted);
            font-size: .92rem;
            text-decoration: none;
            transition: color .2s ease;
        }

        .back-link:hover { color: #ff8bd5; }

        .profile-photo-wrap {
            width: 148px;
            height: 148px;
            margin: 0 auto 18px;
            padding: 5px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 10px 35px rgba(110, 0, 255, .25);
        }

        #foto-perfil {
            display: block;
            width: 100%;
            height: 100%;
            border: 4px solid var(--surface);
            border-radius: 50%;
            background: var(--surface-soft);
            object-fit: cover;
        }

        .profile-name { margin: 0; font-size: clamp(1.5rem, 5vw, 2rem); overflow-wrap: anywhere; }
        .profile-caption { margin: 8px 0 20px; color: var(--muted); }

        .upload-form { margin: 0 0 26px; }
        .upload-input { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; clip-path: inset(50%); }

        .upload-label {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 18px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            background: rgba(255, 255, 255, .06);
            color: #fff;
            font-size: .92rem;
            font-weight: 650;
            cursor: pointer;
            transition: border-color .2s ease, background .2s ease, transform .2s ease;
        }

        .upload-label:hover { transform: translateY(-1px); border-color: var(--secondary); background: rgba(255, 0, 170, .12); }
        .upload-input:focus-visible + .upload-label { outline: 3px solid rgba(255, 0, 170, .55); outline-offset: 3px; }

        .upload-message {
            margin: -10px 0 20px;
            padding: 11px 14px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 10px;
            color: #e8e8f1;
            font-size: .9rem;
        }

        .upload-message.success { border-color: rgba(74, 222, 128, .35); background: rgba(34, 197, 94, .1); color: #bbf7d0; }
        .upload-message.error { border-color: rgba(248, 113, 113, .35); background: rgba(239, 68, 68, .1); color: #fecaca; }

        .profile-actions { display: grid; gap: 12px; }

        .profile-action {
            display: flex;
            min-height: 50px;
            align-items: center;
            gap: 14px;
            padding: 0 17px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 12px;
            background: rgba(255, 255, 255, .045);
            color: var(--text);
            font-weight: 600;
            text-align: left;
            text-decoration: none;
            transition: transform .2s ease, border-color .2s ease, background .2s ease;
        }

        .profile-action i { width: 20px; color: #ff70cf; text-align: center; }
        .profile-action:hover { transform: translateY(-2px); border-color: rgba(255, 0, 170, .48); background: rgba(255, 255, 255, .08); }
        .profile-action.logout { border-color: rgba(255, 100, 120, .22); }
        .profile-action.logout i { color: #ff8999; }
        .profile-action.logout:hover { border-color: rgba(255, 100, 120, .55); background: rgba(255, 80, 100, .08); }

        @media (max-width: 380px) {
            body { padding: 18px 12px; }
            .profile-card { border-radius: 18px; }
            .profile-photo-wrap { width: 128px; height: 128px; }
        }
    </style>
</head>
<body>
    <main class="profile-card">
        <a href="../index.php" class="back-link">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Voltar à tela principal
        </a>

        <div class="profile-photo-wrap">
            <img src="exibir_foto.php?v=<?= time() ?>" alt="Foto de perfil de <?= $nomeUsuario ?>" id="foto-perfil">
        </div>
        <h1 class="profile-name"><?= $nomeUsuario ?></h1>
        <p class="profile-caption">Seu perfil ChampionsSports</p>

        <form id="form-foto" class="upload-form" action="upload_foto.php" method="post" enctype="multipart/form-data">
            <input type="file" id="input-foto" name="foto_perfil" accept="image/jpeg,image/png,image/gif,image/webp" class="upload-input">
            <label for="input-foto" class="upload-label">
                <i class="fa-solid fa-camera" aria-hidden="true"></i>
                Alterar foto
            </label>
        </form>

        <?php if ($uploadStatus === 'success'): ?>
            <p class="upload-message success" role="status">Sua foto de perfil foi atualizada.</p>
        <?php elseif ($uploadStatus === 'error'): ?>
            <p class="upload-message error" role="alert">Não foi possível atualizar a foto. Verifique se o arquivo é uma imagem válida de até 5 MB e tente novamente.</p>
        <?php endif; ?>

        <nav class="profile-actions" aria-label="Ações do perfil">
            <a href="../index.php" class="profile-action">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                Tela principal
            </a>
            <a href="editar_cadastro.php" class="profile-action">
                <i class="fa-solid fa-user-pen" aria-hidden="true"></i>
                Editar cadastro
            </a>
            <a href="favoritos.php" class="profile-action">
                <i class="fa-solid fa-star" aria-hidden="true"></i>
                Meus favoritos
            </a>
            <a href="contato.php" class="profile-action">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                Contato
            </a>
            <a href="logout.php" class="profile-action logout">
                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                Sair da conta
            </a>
        </nav>
    </main>

    <script>
        document.getElementById('input-foto').addEventListener('change', function () {
            if (this.files.length > 0) {
                document.getElementById('form-foto').requestSubmit();
            }
        });
    </script>
</body>
</html>
