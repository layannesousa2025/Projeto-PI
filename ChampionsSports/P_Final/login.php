<?php
session_start(); // Inicia a sessão para armazenar o ID do usuário

// === CONFIGURAÇÃO DE CONEXÃO AO BANCO DE DADOS ===
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "banco_teste01";
$port       = 3309;

// Habilita o modo de relatório de exceções para o mysqli (permite usar try...catch)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Cria a conexão com o MySQL (incluindo a porta correta)
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn->set_charset("utf8mb4"); // Evita problemas com acentuação
} catch (mysqli_sql_exception $e) {
    // Trata erro de servidor desligado ou inacessível
    if (str_contains($e->getMessage(), 'actively refused it') || $e->getCode() === 2002) {
        http_response_code(500);
        die("❌ Falha na conexão: O servidor MySQL parece estar desligado. 
            Por favor, inicie-o no painel do XAMPP e tente novamente.");
    }
    // Outros erros genéricos de conexão
    http_response_code(500);
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}

// === PROCESSAMENTO DO LOGIN ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Captura os dados do formulário
    $usuario = trim($_POST['nome'] ?? '');
    $senha   = trim($_POST['senha'] ?? '');

    // Verifica se os campos foram preenchidos
    if (empty($usuario) || empty($senha)) {
        die("⚠️ Por favor, preencha todos os campos.");
    }

    // Prepara a consulta segura
    $stmt = $conn->prepare("
        SELECT 
            l.id_login, 
            u.id_usuarios, 
            cu.nome, 
            l.senha, 
            cu.id_cadastro_usuarios
        FROM login l
        JOIN usuarios u 
            ON l.usuario = u.email
        JOIN cadastro_usuarios cu 
            ON l.id_login = cu.id_login
        WHERE l.usuario = ? OR cu.nome = ?
        LIMIT 1
    ");
    
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $stmt->store_result();

    // Se encontrou um registro correspondente
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_login, $id_usuarios, $nome, $senha_hash, $id_cadastro_usuario);
        $stmt->fetch();

        // Verifica a senha
        if (password_verify($senha, $senha_hash)) {
            // Armazena dados importantes na sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id_usuarios;
            $_SESSION['nome'] = $nome;
            $_SESSION['id_cadastro_usuario'] = $id_cadastro_usuario;

            // Redireciona para a tela principal
            header("Location: telaPrincipal.php");
            exit;
        } else {
            echo "❌ Usuário ou senha incorretos.";
        }
    } else {
        echo "❌ Usuário ou senha incorretos.";
    }

    $stmt->close();
}

$conn->close();
?>