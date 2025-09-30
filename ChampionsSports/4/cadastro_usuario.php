<?php
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

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
   $datanascimento_input = $_POST['datanascimento'] ?? null;

if ($datanascimento_input) {
    $date = DateTime::createFromFormat('Y-m-d', $datanascimento_input);
    if ($date) {
        $datanascimento = $date->format('Y-m-d');
    } else {
        die("Formato de data inválido.");
    }
} else {
    die("Data de nascimento não informada.");
}// Converte para 2025-09-29
    $telefone = $_POST['telefone'];


    // Criptografa a senha antes de salvar no banco
   // $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o e-mail já existe no banco de dados
    $check_sql = "SELECT email FROM usuarios WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // E-mail já existe, mostra uma mensagem de erro
        echo "Este e-mail já está cadastrado!";
    } else {
        // E-mail não existe, insere o novo usuário
        $insert_sql = "INSERT INTO usuarios (nome, email, senha,datanascimento,telefone) VALUES ( ?, ?, ?,?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssss", $nome, $email, $senha, $datanascimento, $telefone);

        if ($insert_stmt->execute()) {
            echo "Usuário cadastrado com sucesso!";
        } else {
            echo "Erro: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}

$conn->close();
?>