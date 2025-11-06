<?php
session_start(); // Iniciar a sessão para armazenar o ID do usuário

// Dados de conexão com o banco de dados
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "banco_teste01";
$port = 3309; // Porta correta do MySQL no XAMPP

// Habilita o modo de relatório de exceções para mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Cria a conexão com o banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
} catch (mysqli_sql_exception $e) {
    // Verifica se o erro é de recusa de conexão e exibe uma mensagem amigável
    if (str_contains($e->getMessage(), 'actively refused it') || $e->getCode() === 2002) {
        die("❌ Falha na conexão: O servidor de banco de dados (MySQL) parece estar desligado. Por favor, inicie-o no painel do XAMPP e tente novamente.");
    }
    // Para outros erros de conexão, exibe a mensagem padrão
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}

// Verifica se o ID do usuário recém-cadastrado está na sessão
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Se não estiver logado, redireciona para a página de login
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
    $insert_sql = "INSERT INTO endereco (cep, uf, rua, bairro, cidade, complemento, id_usuarios) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
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