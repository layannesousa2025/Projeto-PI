<?php

session_start(); // Inicia a sessão para armazenar o ID do usuário

// === CONFIGURAÇÃO DE CONEXÃO AO BANCO DE DADOS ===

// Inclui o arquivo de conexão com o banco de dados
require_once "conexao.php";

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
        // Redireciona com erro em vez de usar die() para manter a consistência da UI
        header("Location: ../html/login.html?error=empty_fields");
        exit;
    }

    // Prepara a consulta segura
    $stmt = $conn->prepare("
        SELECT 
            l.id_login,
            u.id_usuarios,
            cu.id_cadastro_usuario,
            cu.nome,
            l.senha,
            l.tipo_usuario,
            l.situacao
        FROM login l
        JOIN cadastro_usuario cu ON l.id_login = cu.id_login
        JOIN usuarios u ON cu.email = u.email -- Junta com a tabela usuarios pelo email
        WHERE l.usuario = ? OR cu.email = ?
        LIMIT 1
    ");
    
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $stmt->store_result();

    // Se encontrou um registro correspondente
    if ($stmt->num_rows > 0) {
        // A ordem do bind_result deve corresponder exatamente à ordem dos campos no SELECT
        $stmt->bind_result($id_login, $id_usuarios, $id_cadastro_usuario, $nome, $senha_hash, $tipo_usuario, $situacao);
        $stmt->fetch();

        // Verifica se a senha corresponde E se o usuário está ativo ('A')
        // A função password_verify lida com segurança com hashes inválidos ou vazios.
        if (password_verify($senha, $senha_hash ?? '') && $situacao === 'A') {
            // Prevenção contra Session Fixation: Regenera o ID da sessão
            session_regenerate_id(true);

            // Armazena dados importantes na sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id_usuarios; // CORRIGIDO: Usar o ID da tabela 'usuarios' como principal
            $_SESSION['nome'] = $nome;
            $_SESSION['id_cadastro_usuario'] = $id_cadastro_usuario;
            $_SESSION['tipo_usuario'] = $tipo_usuario;

            $stmt->close();
            // Redireciona para a tela principal (movido para fora do if-POST)
            header("Location: ../index.php");
            exit;
        } else {
            // Senha incorreta: redireciona de volta para o login com erro
            $stmt->close();
            header("Location: ../html/login.html?error=invalid_credentials");
            exit;
        }
    } else {
        // Usuário não encontrado: redireciona de volta para o login com erro
        header("Location: ../html/login.html?error=invalid_credentials");
        exit;
    }
}

// Se o usuário já estiver logado, redireciona para a página principal.
// Isso evita que um usuário logado veja a página de login novamente.
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../index.php");
    exit;
}

$conn->close();
?>