<?php
session_start(); // Inicia a sessão no topo do arquivo
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
    $senha = $_POST['senha'];

    // Permite login com nome de usuário ou e-mail
    // Consulta corrigida para buscar o usuário pelo nome ou e-mail e juntar com a tabela de cadastro
    $stmt = $conn->prepare("
        SELECT l.id_login, cu.nome, l.senha, cu.id_cadastro_usuarios
        FROM login l
        JOIN cadastro_usuarios cu ON l.id_login = cu.id_login
        WHERE l.usuario = ?
    ");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Pega o hash do banco
        $stmt->bind_result($id, $nome, $senha_hash, $id_cadastro_usuario);
        $stmt->fetch();

        // Verifica se a senha digitada confere com o hash
        if (password_verify($senha, $senha_hash)) {
            // Armazena dados na sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id; // id da tabela usuarios
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
