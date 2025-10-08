<?php
session_start(); // Iniciar a sessão para armazenar o ID do usuário
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

// Habilita o modo de relatório de exceções para mysqli, para que o try...catch funcione corretamente.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $datanascimento_input = $_POST['datanascimento'] ?? null;
    // Coleta os valores dos checkboxes de deficiência
    $deficiencia_visual = isset($_POST['deficiencia_visual']) ? 1 : 0;
    $deficiencia_auditiva = isset($_POST['deficiencia_auditiva']) ? 1 : 0;
    $deficiencia_motora = isset($_POST['deficiencia_motora']) ? 1 : 0;
    $deficiencia_cardiaca = isset($_POST['deficiencia_cardiaca']) ? 1 : 0;
    $deficiencia_intelectual = isset($_POST['deficiencia_intelectual']) ? 1 : 0;
    $deficiencia_neurologica = isset($_POST['deficiencia_neurologica']) ? 1 : 0;
    $deficiencia_psiquica = isset($_POST['deficiencia_psiquica']) ? 1 : 0;
    $deficiencia_outros = isset($_POST['deficiencia_outros']) ? 1 : 0;
    $telefone = $_POST['telefone'];
    $datanascimento = null;

    if ($datanascimento_input) {
        $date = DateTime::createFromFormat('Y-m-d', $datanascimento_input);
        if ($date && $date->format('Y-m-d') === $datanascimento_input) {
            $datanascimento = $datanascimento_input;
        } else {
            die("Formato de data inválido.");
        }
    } else {
        die("Data de nascimento não informada.");
    }

    // Criptografa a senha antes de salvar no banco
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

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
        // E-mail não existe, inicia uma transação
        $conn->begin_transaction();

        try {
            // 1. Insere na tabela 'login' primeiro para obter o id_login
            $insert_login_sql = "INSERT INTO login (usuario, senha) VALUES (?, ?)";
            $insert_login_stmt = $conn->prepare($insert_login_sql);
            $insert_login_stmt->bind_param("ss", $email, $senha_hash); // Usando email como 'usuario'
            $insert_login_stmt->execute();
            $id_login_novo = $conn->insert_id;

            // 2. Insere na tabela 'usuarios' (mantendo para o sistema de login atual)
            $insert_user_sql = "INSERT INTO usuarios (nome, cpf, email, senha, datanascimento, telefone) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_user_stmt = $conn->prepare($insert_user_sql);
            $insert_user_stmt->bind_param("ssssss", $nome, $cpf, $email, $senha_hash, $datanascimento, $telefone);
            $insert_user_stmt->execute();
            $id_usuario_novo = $conn->insert_id;

            // 3. Insere na tabela 'cadastro_usuarios' usando o id_login_novo
            $insert_cad_user_sql = "INSERT INTO cadastro_usuarios (nome, cpf, data_nascimento, telefone, email, id_login) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_cad_user_stmt = $conn->prepare($insert_cad_user_sql);
            $insert_cad_user_stmt->bind_param("sssssi", $nome, $cpf, $datanascimento, $telefone, $email, $id_login_novo);
            $insert_cad_user_stmt->execute();
            $id_cadastro_usuario_novo = $conn->insert_id;

            // 4. Insere na tabela 'acessibilidade'
            $insert_access_sql = "INSERT INTO acessibilidade (id_cadastro_usuarios, deficiencia_visual, deficiencia_auditiva, deficiencia_motora, deficiencia_cardiaca, deficiencia_intelectual, deficiencia_neurologica, deficiencia_psiquica, outros) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_access_stmt = $conn->prepare($insert_access_sql);
            $insert_access_stmt->bind_param("iiiiiiiii", $id_cadastro_usuario_novo, $deficiencia_visual, $deficiencia_auditiva, $deficiencia_motora, $deficiencia_cardiaca, $deficiencia_intelectual, $deficiencia_neurologica, $deficiencia_psiquica, $deficiencia_outros);
            $insert_access_stmt->execute();

            // Se tudo deu certo, confirma a transação
            $conn->commit();

            // LOGAR AUTOMATICAMENTE O USUÁRIO APÓS O CADASTRO
            // Limpa qualquer sessão antiga e inicia uma nova
            session_unset();
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id_usuario_novo; // ID da tabela 'usuarios'
            $_SESSION['nome'] = $nome;
            $_SESSION['id_cadastro_usuario'] = $id_cadastro_usuario_novo; // ID da tabela 'cadastro_usuarios'

            // Redireciona para a página de endereço. Como o usuário já está logado,
            // o ID pode ser pego da sessão principal.
            header("Location: endereco.php"); // Redireciona para o script PHP, não para o HTML
            exit();

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback(); // Desfaz as operações em caso de erro
            die("Erro ao cadastrar: " . $exception->getMessage());
        }
    }
    $check_stmt->close();
}

$conn->close();
?>