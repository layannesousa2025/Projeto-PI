<?php
session_start(); // Inicia a sessão para obter o ID do usuário
// Dados de conexão com o banco de dados (substitua com os seus)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_teste"; // O nome do seu banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o ID do usuário recém-cadastrado está na sessão
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Se não estiver, redireciona para a página de cadastro, pois algo deu errado.
    header("Location: login.html");
    exit;
}
// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $cep = $_POST['cep'];
        $uf = $_POST['uf'];
        $rua = $_POST['rua'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $complemento = $_POST['complemento'];
    $id_usuario = $_SESSION['id']; // Pega o ID principal do usuário da sessão logada

    // Insere os dados do endereço na tabela
    $insert_sql = "INSERT INTO endereco (cep, uf, rua, bairro, cidade, complemento, id_usuarios) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssssssi", $cep, $uf, $rua, $bairro, $cidade, $complemento, $id_usuario);
    
    if ($insert_stmt->execute()) {
        header("Location: telaPrincipal.php");
        exit;
    } else {
        echo "Erro: " . $insert_stmt->error;
    }
    $insert_stmt->close();
}
$conn->close();
?>