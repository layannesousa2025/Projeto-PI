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
    $cep = $_POST['cep'];
        $uf = $_POST['uf'];
        $rua = $_POST['rua'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $complemento = $_POST['complemento'];
    

    // Insere os dados do endereço na tabela
    $insert_sql = "INSERT INTO endereco (cep, uf, rua, bairro, cidade, complemento,id_usuarios) VALUES (?, ?, ?, ?, ?, ?,(SELECT id_usuarios FROM usuarios ORDER BY id_usuarios DESC LIMIT 1))";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssssss", $cep, $uf, $rua, $bairro, $cidade, $complemento);
    
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