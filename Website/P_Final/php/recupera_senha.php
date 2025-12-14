<?php
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para recuperação de senha (a ser implementada)
    // Por enquanto, apenas exibimos uma mensagem de sucesso.
    $email = $_POST['email'] ?? '';
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Se um e-mail correspondente for encontrado, um link de recuperação será enviado para " . htmlspecialchars($email);
    } else {
        $mensagem = "Por favor, insira um e-mail válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - ChampionsSports</title>
    <link rel="stylesheet" href="../Css/editar_cadastro.css">
    <style>
        body {
            background-image: url('../img/Imagem-Champions02.png');
            background-size: cover;
            background-position: center;
        }
        .form-container {
            background-color: rgba(42, 42, 74, 0.95);
        }
        .mensagem {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            background-color: #007BFF;
            color: #fff;
        }
    </style>
</head>
<body>
    

    <form action="recupera_senha.php" method="POST" class="form-container">
        <h1>Recuperar Senha</h1>
        <p style="margin-bottom: 20px;">Digite seu e-mail para receber as instruções de recuperação.</p>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>
        </div>

        <button type="submit" class="submit-btn">Enviar</button>
    </form>
</body>
</html>
