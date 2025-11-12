<?php
session_start(); // Inicia a sessão para armazenar o ID do usuário

// === CONFIGURAÇÃO DE CONEXÃO AO BANCO DE DADOS ===
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "champions_sport";
$port       = 3306;

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
            cu.id_cadastro_usuario,
            cu.nome,
            l.senha,
            l.tipo_usuario,
            l.situacao
        FROM login l
        JOIN cadastro_usuario cu 
            ON l.id_login = cu.id_login
        WHERE l.usuario = ? OR cu.nome = ?
        LIMIT 1
    ");
    
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $stmt->store_result();

    // Se encontrou um registro correspondente
    if ($stmt->num_rows > 0) {
        // A ordem do bind_result deve corresponder exatamente à ordem dos campos no SELECT
        $stmt->bind_result($id_login, $id_cadastro_usuario, $nome, $senha_hash, $tipo_usuario, $situacao);
        $stmt->fetch();

        // Verifica a senha
        // Garante que $senha_hash seja uma string não-vazia antes de chamar password_verify
        if (!is_string($senha_hash) || $senha_hash === '' || $situacao !== 'A') {
            // Hash ausente/Inválido: trata como credenciais inválidas
            $stmt->close();
            header("Location: login.html?error=invalid_credentials");
            exit;
        }

        if (password_verify($senha, $senha_hash)) { // A verificação de situação já foi feita acima
            // Prevenção contra Session Fixation: Regenera o ID da sessão
            session_regenerate_id(true);

            // Armazena dados importantes na sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id_cadastro_usuario; // Usar o ID de cadastro_usuario que é mais consistente
            $_SESSION['nome'] = $nome;
            $_SESSION['id_cadastro_usuario'] = $id_cadastro_usuario;
            $_SESSION['tipo_usuario'] = $tipo_usuario;

            $stmt->close();
            // Redireciona para a tela principal
            header("Location: telaPrincipal.php");
            exit;
        } else {
            // Senha incorreta: redireciona de volta para o login com erro
            $stmt->close();
            header("Location: login.html?error=invalid_credentials");
            exit;
        }
    } else {
        // Usuário não encontrado: redireciona de volta para o login com erro
        header("Location: login.html?error=invalid_credentials");
        exit;
    }
}

$conn->close();
?>