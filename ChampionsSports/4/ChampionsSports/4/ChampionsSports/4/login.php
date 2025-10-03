<?php
// Dados de conexão com o MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_teste"; // Substitua pelo nome do seu banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['nome'];
    $senha   = $_POST['senha'];

    // Busca apenas pelo nome
    $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE nome = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Pega o hash do banco
        $stmt->bind_result($senha_hash);
        $stmt->fetch();

        // Verifica se a senha digitada confere com o hash
        if (password_verify($senha, $senha_hash)) {
            echo "✅ Login realizado com sucesso!";
            // Aqui você pode iniciar sessão, redirecionar etc.
            header("Location: telaprincipal.html");
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
