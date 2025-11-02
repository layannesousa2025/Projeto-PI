<?php
session_start(); // Inicia a sessão no topo do arquivo
// Dados de conexão com o MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_teste"; // Substitua pelo nome do seu banco de dados

// Habilita o modo de relatório de exceções para mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
 
try {
    // Criar conexão
    $conn = new mysqli($servername, $username, $password, $dbname);
} catch (mysqli_sql_exception $e) {
    // Verifica se o erro é de recusa de conexão e exibe uma mensagem amigável
    if (str_contains($e->getMessage(), 'actively refused it') || $e->getCode() === 2002) {
        die("❌ Falha na conexão: O servidor de banco de dados (MySQL) parece estar desligado. Por favor, inicie-o no painel do XAMPP e tente novamente.");
    }
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['nome'];
    $senha = $_POST['senha'];

    // Permite login com nome de usuário ou e-mail
    // Consulta corrigida para buscar o usuário pelo nome ou e-mail e juntar com a tabela de cadastro
    $stmt = $conn->prepare("
        SELECT l.id_login, u.id_usuarios, cu.nome, l.senha, cu.id_cadastro_usuarios
        FROM login l
        JOIN usuarios u ON l.usuario = u.email
        JOIN cadastro_usuarios cu ON l.id_login = cu.id_login -- Junta com cadastro_usuarios para pegar o nome
        WHERE l.usuario = ? OR cu.nome = ? -- Permite login com email (l.usuario) ou nome de usuário (cu.nome)
    ");
    $stmt->bind_param("ss", $usuario, $usuario); // Passa o mesmo input para os dois placeholders
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Pega o hash do banco
        $stmt->bind_result($id_login, $id_usuarios, $nome, $senha_hash, $id_cadastro_usuario);
        $stmt->fetch();

        // Verifica se a senha digitada confere com o hash
        if (password_verify($senha, $senha_hash)) {
            // Armazena dados na sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id_usuarios; // id da tabela usuarios
            $_SESSION['nome'] = $nome;
            
            // Armazena o id_cadastro_usuarios para usar na tabela 'categoria'
            $_SESSION['id_cadastro_usuario'] = $id_cadastro_usuario;

            // Redireciona para a página principal
            header("Location: telaPrincipal.php");
            exit; // sempre use exit depois do header
        } else {
            echo "❌ Usuário ou senha incorretos.";
        }
    } else {
        echo "❌ Usuário ou senha incorretos.";
        
    }

    $stmt->close();
}
?>




<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tela de Login</title>
  <link rel="stylesheet" href="Css/login.css">
</head>

<body>
  <div class="voltar">
    <a href="telaPrincipal.php"><img src="./img/button (2).png" alt="Botão voltar"></a>
  </div>

  <form onsubmit="return verificarLogin(event)" action="login.php" method="POST">
    <h1>Login De Usuário</h1>

    <div class="form-group">
      <label for="nome">Nome de Usuario ou E-mail:</label>
      <input type="text" name="nome" id="nome" required>
    </div>

    <div class="form-group">
      <label for="senha">Senha:</label>
      <input type="password" name="senha" id="senha" required>
    </div>

    <label class="remember-me">
      <input type="checkbox" id="lembrar-me" name="lembrar-me"> lembrar-me
    </label>

    <br>
    <div class="form-group" style="text-align: center;">
      <button type="submit">Entrar</button>
    </div>

    <br>
    <div id="recuperar-senha">
      <a href="cadastro.html">Criar Conta</a>
    </div>

    <br>
    <div id="Criar-Conta">
      <a href="recuperacao.html">Esqueceu a Senha?</a>
    </div>
  </form>



</body>


</html>