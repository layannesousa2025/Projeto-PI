<?php
session_start(); // Inicia a sessão no topo do arquivo
// Dados de conexão com o MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_teste01"; // Substitua pelo nome do seu banco de dados

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
    // Juntamos as tabelas usuarios e cadastro_usuarios para pegar ambos os IDs
    $stmt = $conn->prepare("
        SELECT u.id_usuarios, u.nome, u.senha, cu.id_cadastro_usuarios 
        FROM usuarios u
        LEFT JOIN cadastro_usuarios cu ON u.email = cu.email
        WHERE u.nome = ? OR u.email = ?
    ");
    $stmt->bind_param("ss", $usuario, $usuario);
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
