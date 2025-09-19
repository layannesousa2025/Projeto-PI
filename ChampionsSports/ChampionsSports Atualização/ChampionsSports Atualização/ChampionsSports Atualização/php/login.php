<?php
// Dados de conexão com o MySQL
$servername = "localhost";
$username = "root";
$password = "senac";
$dbname = "p_final"; // Substitua pelo nome do seu banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Prepara a consulta SQL para evitar SQL Injection
    $stmt = $conn->prepare("SELECT * FROM login  WHERE usuario = ? and senha = ?");
    $stmt->bind_param("ss", $usuario, $senha);
    $stmt->execute();
    $stmt->store_result();

    // Verifica se encontrou o usuário
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($senha_hash);
        $stmt->fetch();

        // Verifica se a senha está correta
        if (password_verify($senha, $senha_hash)) {
            // Login bem-sucedido
            echo "Login realizado com sucesso!";
            // Aqui você pode iniciar a sessão, redirecionar, etc.
        } else {
            // Senha incorreta
            echo "Usuário ou senha incorretos.";
        }
    } else {
        // Usuário não encontrado
        echo "Usuário ou senha incorretos.";
    }

    $stmt->close();
}

?>