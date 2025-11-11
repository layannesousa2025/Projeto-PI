<?php
session_start(); // Inicia a sessão

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

// Mapeamento de nomes de coluna para nomes de exibição e imagens
$allCategories = [
    'ciclismo' => ['nome' => 'Ciclismo', 'img' => './img/bike.png'],
    'futebol' => ['nome' => 'Futebol', 'img' => './img/jogador.png'],
    'voleibol' => ['nome' => 'Voleibol', 'img' => './img/volei.png'],
    'academia' => ['nome' => 'Academia', 'img' => './img/musculacao.png'],
    'caminhada' => ['nome' => 'Caminhada', 'img' => './img/caminhada.png'],
    'natacao' => ['nome' => 'Natação', 'img' => './img/natacao.png'],
    'lazer' => ['nome' => 'Lazer', 'img' => './img/lazer.png'],
    'pcd' => ['nome' => 'PCD', 'img' => './img/rodas.png']
];

$favoritos_usuario = [];
try {
    // Correção: Usar 127.0.0.1 e a porta 3306 para consistência com o restante do projeto.
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=champions_sport;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT ciclismo, futebol, voleibol, academia, caminhada, natacao, lazer, pcd FROM categoria WHERE id_cadastro_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['id_cadastro_usuario']]);
    $categorias = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($categorias) {
        foreach ($categorias as $nome_categoria => $is_favorito) {
            if ($is_favorito == 1) {
                $favoritos_usuario[] = $nome_categoria; // Adiciona o nome da coluna (ex: 'futebol')
            }
        }
    }
} catch (Exception $e) {
    // Em caso de erro, a página carrega sem favoritos. Pode-se adicionar um log aqui.
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Favoritos</title>
    <link rel="stylesheet" href="Css/favoritos.css">
    
    <!-- Adicionando a fonte Poppins do Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <!-- Adicionando Font Awesome para o ícone de estrela -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="voltar-container">
        <a href="categorias.php" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>


    <h2 class="favorites-title">Suas Categorias Favoritas</h2>

    <p class="page-subtitle">
        Clique na estrela para remover dos favoritos ou no card para ver os eventos.
    </p>


    
    <div class="container">
        <div class="favorites-grid-flex" id="favorites-grid">
            <?php if (empty($favoritos_usuario)): ?>
                <p id="no-favorites-message" style="color: white; text-align: center; margin-top: 20px;">Você ainda não adicionou nenhuma categoria aos favoritos.</p>
            <?php else: ?>
                <?php foreach ($favoritos_usuario as $db_column): ?>
                    <?php if (isset($allCategories[$db_column])): 
                        $category_info = $allCategories[$db_column]; ?>
                        <div class="category-card" data-category="<?php echo htmlspecialchars($db_column); ?>">
                            <i class="fas fa-star favorite-star favorited"></i>
                            <img src="<?php echo htmlspecialchars($category_info['img']); ?>" alt="Ícone de <?php echo htmlspecialchars($category_info['nome']); ?>" class="category-icon">
                            <span class="category-name"><?php echo htmlspecialchars($category_info['nome']); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const favoritesGrid = document.getElementById('favorites-grid');

            // O PHP já renderiza os cards. O JavaScript precisa apenas adicionar os event listeners.
            // Removemos o bloco de código JavaScript que tentava renderizar os cards novamente.

            // Adiciona um único event listener ao container pai para lidar com cliques nos cards e estrelas
            // (delegação de eventos). Isso funciona para cards já renderizados pelo PHP.
            favoritesGrid.addEventListener('click', async (event) => {
                const card = event.target.closest('.category-card');
                if (!card) return; // Se o clique não foi em um card, ignora

                const categoryDbColumn = card.dataset.category; // Pega o nome da coluna do DB (ex: 'ciclismo')

                // Se o clique foi na estrela
                if (event.target.classList.contains('favorite-star')) {
                    event.stopPropagation(); // Impede que o clique na estrela acione o clique no card
                    
                    // Remove o card da tela
                    card.remove();

                    // Atualiza o banco de dados para remover o favorito
                    await saveFavoriteState(categoryDbColumn, false);

                    // Se não houver mais cards visíveis, recarrega a página para que a mensagem de "sem favoritos" apareça
                    if (favoritesGrid.children.length === 0) {
                        window.location.reload();
                    }
                } else {
                    // Se o clique foi no card (mas não na estrela), navega para a página de eventos
                    const categoryName = card.querySelector('.category-name').textContent; // Pega o nome de exibição (ex: 'Ciclismo')
                    window.location.href = `eventos.php?game=${encodeURIComponent(categoryName)}`;
                }
            });

            // Função para salvar/remover o estado do favorito no banco de dados
            async function saveFavoriteState(category, isFavorited) {
                try {
                    const response = await fetch('salvar_favorito_categoria.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ category: category.toLowerCase(), isFavorited: isFavorited })
                    });
                    if (!response.ok) {
                        const result = await response.json();
                        throw new Error(result.mensagem || 'Falha ao atualizar favorito.');
                    }
                } catch (error) {
                    console.error('Erro ao atualizar favorito:', error);
                    alert('Não foi possível atualizar o favorito. Tente recarregar a página.');
                }
            }
        });
    </script>

</body>

</html>