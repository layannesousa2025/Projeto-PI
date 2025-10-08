<?php
session_start();
 
// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

// Busca as categorias favoritas do usuário no banco para marcar as estrelas
$favoritos_usuario = [];
try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco_teste;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT ciclismo, futebol, voleibol, academia, caminhada, natacao, lazer, pcd FROM categoria WHERE id_cadastro_usuarios = ?");
    $stmt->execute([$_SESSION['id_cadastro_usuario']]);
    $favoritos_usuario = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    // Ignora o erro, a página carrega sem os favoritos marcados
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias Esportivas</title>
    <link rel="stylesheet" href="Css/categoria.css">
    <!-- Adicionando Font Awesome para o ícone de estrela -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <h1>
        <div class="voltar">
            <a href="./telaPrincipal.php"><img src="./img/button (2).png" alt="Botão voltar"></a>
        </div>

        <div class="usuario" float: right;>
            <a href="usuario.php">
                <img src="./img/login2.png" alt="logo2" style="width: 60px; height: 60px; object-fit: contain;">
            </a>

        </div>

    </h1>

    <h2>Busque Suas Categorias</h2>

    <p style="text-align: center; color: #ccc; margin-top: -10px; margin-bottom: 20px;">
        Clique na estrela ★ para adicionar uma categoria aos seus favoritos!
    </p>
    <!-- Adicionando um local para exibir mensagens de status -->
    <p id="status-message" style="text-align: center; color: lightgreen; height: 20px; transition: opacity 0.5s;"></p>


    <div class="container">


        <div class="search-container">
            <input type="text" class="search-bar" placeholder="Buscar categoria...">
            <img src="./img/img-pesquisa (1).png" alt="Buscar" class="search-icon">
        </div>

        <div class="categories-grid">
            <div class="category-card" data-category="Ciclismo">
                <i class="fas fa-star favorite-star"></i>
                <img src="./img/bike.png" alt="Ícone de Ciclismo" class="category-icon">
                <span class="category-name">Ciclismo</span>
            </div>
            <div class="category-card" data-category="Futebol">
                <i class="fas fa-star favorite-star"></i>
                <img src="./img/jogador.png" alt="Ícone de Futebol" class="category-icon">
                <span class="category-name">Futebol</span>
            </div>
            <div class="category-card" data-category="Voleibol">
                <i class="fas fa-star favorite-star"></i>
                <img src="./img/volei.png" alt="Ícone de Voleibol" class="category-icon">
                <span class="category-name">Voleibol</span>
            </div>
            <div class="category-card" data-category="Academia">
                <i class="fas fa-star favorite-star"></i>
                <img src="./img/musculacao.png" alt="Ícone de Musculação" class="category-icon">
                <span class="category-name">Academia</span>
            </div>
            <div class="category-card" data-category="Caminhada">
                <i class="fas fa-star favorite-star"></i>
                <img src="./img/caminhada.png" alt="Ícone de Caminhada" class="category-icon">
                <span class="category-name">Caminhada</span>
            </div>
            <div class="category-card" data-category="Natacao">
                <i class="fas fa-star favorite-star"></i>
                <img src="./img/natacao.png" alt="Ícone de Natação" class="category-icon">
                <span class="category-name">Natação</span>
            </div>
            <div class="category-card" data-category="Lazer">
                <i class="fas fa-star favorite-star"></i>
                <img src="./img/lazer.png" alt="Ícone de Lazer" class="category-icon">
                <span class="category-name">Lazer</span>
            </div>
            <div class="category-card" data-category="PCD">
                <i class="fas fa-star favorite-star"></i>
                <img src="./img/rodas.png" alt="Ícone de Esportes Adaptados (PCD)" class="category-icon">
                <span class="category-name">PCD</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const categoryCards = document.querySelectorAll('.category-card');
            const statusMessage = document.getElementById('status-message');

            // Pega os favoritos do usuário (injetados pelo PHP)
            const favorites = <?php echo json_encode($favoritos_usuario); ?>;

            function updateStars() {
                categoryCards.forEach(card => {
                    const category = card.dataset.category;
                    const star = card.querySelector('.favorite-star');
                    // A chave no objeto `favorites` é o nome da categoria em minúsculas
                    if (favorites[category.toLowerCase()] == 1) {
                        star.classList.add('favorited');
                    }
                });
            }

            categoryCards.forEach(card => {
                const category = card.dataset.category;
                const star = card.querySelector('.favorite-star');

                star.addEventListener('click', (event) => {
                    event.stopPropagation();
                    event.preventDefault();

                    // Verifica se a categoria já está favoritada e inverte o estado
                    const isFavorited = star.classList.toggle('favorited');

                    // Envia a alteração para o servidor
                    saveFavoriteState(category, isFavorited);
                });

                card.addEventListener('click', () => {
                    window.location.href = `eventos.html?game=${category}`;
                });
            });

            async function saveFavoriteState(category, isFavorited) {
                try {
                    const response = await fetch('salvar_favorito_categoria.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ category: category.toLowerCase(), isFavorited: isFavorited })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.mensagem || 'Erro de servidor.');
                    }

                    const result = await response.json();

                    // Exibe a mensagem de sucesso retornada pelo PHP
                    statusMessage.textContent = result.mensagem;
                    statusMessage.style.color = (result.status === 'sucesso') ? 'lightgreen' : 'salmon';
                    statusMessage.style.opacity = 1;

                    // Faz a mensagem desaparecer após 3 segundos
                    setTimeout(() => {
                        statusMessage.style.opacity = 0;
                    }, 3000);

                } catch (error) {
                    console.error('Erro ao salvar favorito:', error);
                    alert('Não foi possível salvar o favorito: ' + error.message);
                }
            }

            // Marca as estrelas corretas quando a página carrega
            updateStars();
        });
    </script>
</body>

</html>