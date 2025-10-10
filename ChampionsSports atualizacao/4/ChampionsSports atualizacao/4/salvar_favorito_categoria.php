<?php
session_start(); // A sessão deve ser iniciada ANTES de qualquer saída de dados.

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

try {
    // 1. VERIFICAÇÃO DE AUTENTICAÇÃO
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        http_response_code(401); // Não autorizado
        throw new Exception('Usuário não autenticado.');
    }

    // A variável de sessão correta é 'id_cadastro_usuario' (singular)
    if (!isset($_SESSION['id_cadastro_usuario'])) {
        http_response_code(403); // Proibido
        throw new Exception('ID do usuário não encontrado na sessão.');
    }
    $idUsuario = $_SESSION['id_cadastro_usuario'];

    // 2. LEITURA E VALIDAÇÃO DOS DADOS DE ENTRADA
    $data = json_decode(file_get_contents("php://input"), true);
    $categoria = $data['category'] ?? null;
    $isFavorited = (bool)($data['isFavorited'] ?? false);

    // Lista de colunas permitidas para evitar SQL Injection
    $colunasValidas = ['ciclismo', 'futebol', 'voleibol', 'academia', 'caminhada', 'natacao', 'lazer', 'pcd'];
    if (!$categoria || !in_array($categoria, $colunasValidas)) {
        http_response_code(400); // Requisição inválida
        throw new Exception('Categoria inválida.');
    }

    // 3. OPERAÇÃO NO BANCO DE DADOS
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste01;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o usuário já possui um registro
    $stmtCheck = $pdo->prepare("SELECT id_categoria FROM categoria WHERE id_cadastro_usuarios = ?");
    $stmtCheck->execute([$idUsuario]);

    if ($stmtCheck->fetchColumn()) {
        // Se o registro JÁ EXISTE, apenas atualiza a coluna da categoria específica.
        // A validação anterior garante que $categoria é seguro para ser usado no nome da coluna.
        $stmtUpdate = $pdo->prepare("UPDATE categoria SET `$categoria` = :isFavorited WHERE id_cadastro_usuarios = :idUsuario");
        $stmtUpdate->execute([
            ':isFavorited' => $isFavorited ? 1 : 0,
            ':idUsuario' => $idUsuario
        ]);
    } else if ($isFavorited) {
        // Se o registro NÃO EXISTE e a ação é para FAVORITAR (isFavorited = true),
        // cria um novo registro já com a categoria correta marcada como 1.
        // As outras colunas de esporte assumirão o valor padrão do banco (0).
        $stmtInsert = $pdo->prepare("INSERT INTO categoria (id_cadastro_usuarios, `$categoria`) VALUES (?, 1)");
        $stmtInsert->execute([$idUsuario]);
    }
    // Se o registro não existe e a ação é para desfavoritar, não fazemos nada,
    // pois não há o que remover.

    // 4. PREPARA A RESPOSTA DE SUCESSO
    $acao = $isFavorited ? 'adicionado aos' : 'removido dos';
    $mensagem = ucfirst($categoria) . " " . $acao . " favoritos!";
    echo json_encode(['status' => 'sucesso', 'mensagem' => $mensagem]);

} catch (PDOException $e) {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Captura outras exceções (autenticação, dados inválidos)
    // O código de status HTTP já foi definido onde a exceção foi lançada.
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>
