<?php
// No futuro, você pode adicionar aqui a lógica PHP para buscar os favoritos do banco de dados
// quando o usuário estiver logado.
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
            <a href="./usuario.html"><img src="./img/button (2).png" alt="Botão voltar"></a>
        </div>
        <div class="usuario" style="float: right;">
            <a href="usuario.html">
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
        document.addEventListener('DOMContentLoaded', function () {
            const favoritesGrid = document.getElementById('favorites-grid');
            const noFavoritesMessage = document.getElementById('no-favorites-message');
            const favorites = JSON.parse(localStorage.getItem('favoriteCategories')) || [];

            const allCategories = {
                'Ciclismo': './img/bike.png',
                'Futebol': './img/jogador.png',
                'Voleibol': './img/volei.png',
                'Academia': './img/musculacao.png',
                'Caminhada': './img/caminhada.png',
                'Natação': './img/natacao.png',
                'Lazer': './img/lazer.png',
                'PCD': './img/rodas.png'
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

                    // Redireciona para a página de eventos ao clicar
                    categoryCard.addEventListener('click', () => {
                        window.location.href = `eventos.html?game=${categoryName}`;
                    });

                    favoritesGrid.appendChild(categoryCard);
                });
            }
        });
    </script>

</body>

</html>
