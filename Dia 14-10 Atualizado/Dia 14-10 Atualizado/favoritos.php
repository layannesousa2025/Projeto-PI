<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

// Busca as categorias favoritas do usuário no banco
$favoritos_usuario = [];
try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste01;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Busca o registro de categorias do usuário
    $sql = "SELECT ciclismo, futebol, voleibol, academia, caminhada, natacao, lazer, pcd FROM categoria WHERE id_cadastro_usuarios = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['id_cadastro_usuario']]);
    $categorias = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($categorias) {
        // Filtra apenas as categorias que estão marcadas como 1 (favoritas)
        foreach ($categorias as $nome_categoria => $is_favorito) {
            if ($is_favorito == 1) {
                // Adiciona o nome da categoria (com a primeira letra maiúscula) ao array de favoritos
                $favoritos_usuario[] = ucfirst($nome_categoria);
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
    <link rel="stylesheet" href="Css/categoria.css">
    <!-- Adicionando Font Awesome para o ícone de estrela -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <h1>
        <div class="voltar">
            <a href="./usuario.php"><img src="./img/button (2).png" alt="Botão voltar"></a>
        </div>
        <div class="usuario" float: right;>
            <a href="usuario.php">
                <img src="./img/login2.png" alt="logo2" style="width: 60px; height: 60px; object-fit: contain;">
            </a>
        </div>
    </h1>

    <h2>Suas Categorias Favoritas</h2>

    <div class="container">
        <div class="categories-grid" id="favorites-grid">
            <!-- As categorias favoritas serão inseridas aqui pelo JavaScript -->
        </div>
        <p id="no-favorites-message" style="display: none; color: white; text-align: center; margin-top: 20px;">
            Você ainda não adicionou nenhuma categoria aos favoritos.
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const favoritesGrid = document.getElementById('favorites-grid');
            const noFavoritesMessage = document.getElementById('no-favorites-message');
            const favorites = <?php echo json_encode($favoritos_usuario); ?>;

            const allCategories = {
                'Ciclismo': './img/bike.png',
                'Futebol': './img/jogador.png',
                'Voleibol': './img/volei.png',
                'Academia': './img/musculacao.png',
                'Caminhada': './img/caminhada.png',
                'Natacao': './img/natacao.png',
                'Lazer': './img/lazer.png',
                'Pcd': './img/rodas.png' // Corrigido de 'PCD' para 'Pcd' para corresponder ao PHP
            };

            if (favorites.length === 0) {
                noFavoritesMessage.style.display = 'block';
            } else {
                favorites.forEach(categoryName => {
                    const categoryCard = document.createElement('div');
                    categoryCard.className = 'category-card';
                    categoryCard.dataset.category = categoryName;

                    const imageUrl = allCategories[categoryName];

                    categoryCard.innerHTML = `
                        <i class="fas fa-star favorite-star favorited"></i>
                        <img src="${imageUrl}" alt="Ícone de ${categoryName}" class="category-icon">
                        <span class="category-name">${categoryName}</span>
                    `;

                    // Adiciona evento de clique no card para navegar para os eventos
                    categoryCard.addEventListener('click', () => {
                        window.location.href = `eventos.html?game=${categoryName}`;
                    });

                    // Adiciona evento de clique na estrela para remover o favorito
                    const star = categoryCard.querySelector('.favorite-star');
                    star.addEventListener('click', (event) => {
                        event.stopPropagation(); // Impede que o clique na estrela acione o clique no card
                        event.preventDefault();

                        // Remove o card da tela
                        categoryCard.style.display = 'none'; // Esconde em vez de remover para simplicidade

                        // Remove dos favoritos no array local
                        const index = favorites.indexOf(categoryName);
                        if (index > -1) favorites.splice(index, 1);

                        // Se não houver mais favoritos, mostra a mensagem
                        if (document.querySelectorAll('.category-card[style*="display: none"]').length === favoritesGrid.children.length) {
                            noFavoritesMessage.style.display = 'block';
                        }

                        // Envia a alteração para o servidor (para remover do banco)
                        saveFavoriteState(categoryName, false);
                    });

                    favoritesGrid.appendChild(categoryCard);
                });
            }

            async function saveFavoriteState(category, isFavorited) {
                try {
                    await fetch('salvar_favorito_categoria.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ category: category.toLowerCase(), isFavorited: isFavorited })
                    });
                } catch (error) {
                    console.error('Erro ao remover favorito:', error);
                    alert('Não foi possível remover o favorito. Tente recarregar a página.');
                }
            }
        });
    </script>

</body>

</html>