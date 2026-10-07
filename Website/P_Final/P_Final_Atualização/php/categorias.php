<?php
session_start(); // Inicia a sessão

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { 
    header('Location: ../html/login.html'); // Caminho corrigido e nome do arquivo em minúsculo
    exit; 
}
?>

<?php

// Busca as categorias favoritas do usuário no banco para marcar as estrelas
$favoritos_usuario = [];
try {
    // Usar 127.0.0.1 e a porta 3306 para consistência com outras conexões no projeto
    // CORREÇÃO: Ajustada a string de conexão para usar os parâmetros corretos (host, porta, dbname).
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=champions_sport;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $id_usuario = $_SESSION['id_cadastro_usuario'] ?? (
        ($_SESSION['tipo_usuario'] ?? '') === 'Admin' ? null : ($_SESSION['id'] ?? null)
    );
    if ($id_usuario === null && ($_SESSION['tipo_usuario'] ?? '') === 'Admin') {
        $stmtUsuario = $pdo->prepare("SELECT id_cadastro_usuario FROM cadastro_usuario WHERE id_login = ? LIMIT 1");
        $stmtUsuario->execute([$_SESSION['id'] ?? null]);
        $id_usuario = $stmtUsuario->fetchColumn() ?: null;
    }
    $stmt = $pdo->prepare("SELECT MAX(ciclismo) AS ciclismo, MAX(futebol) AS futebol, MAX(voleibol) AS voleibol, MAX(academia) AS academia, MAX(caminhada) AS caminhada, MAX(natacao) AS natacao, MAX(lazer) AS lazer, MAX(pcd) AS pcd FROM categoria WHERE id_cadastro_usuario = ?");
    $stmt->execute([$id_usuario]);
    $favoritos_usuario = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    // Melhor tratamento de erro: informa o usuário sobre o problema no banco de dados.
    error_log("Erro ao buscar favoritos em categorias.php: " . $e->getMessage());
    if (str_contains($e->getMessage(), 'refused it') || $e->getCode() === 2002) {
        http_response_code(503); // Service Unavailable
        die("❌ Falha na conexão: O servidor MySQL parece estar desligado ou inacessível na porta 3306. 
            Por favor, inicie-o no painel do XAMPP e tente novamente.");
    } else {
        http_response_code(500);
        die("Erro ao conectar ou consultar o banco de dados para categorias: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela-Categoria</title>
    <!-- CSS -->
    <link rel="stylesheet" href="../Css/categoria.css">
    <link rel="stylesheet" href="../Css/footer.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <!-- ======== CABEÇALHO ======== -->
    <header class="header">
        <input type="checkbox" id="menu-toggle" class="menu-toggle">

        <a href="../index.php" class="logo">
            <img src="../img/Logo.png" alt="Logo ChampionsSports" width="50" height="50">
            <span>ChampionsSports</span>
        </a>

        <label for="menu-toggle" class="menu-icon">
            <span class="hamburguer"></span>
        </label>

        <nav class="nav">
            <ul>
                <li><a href="../index.php">Início</a></li>
                <li><a href="./categorias.php" class="active">Categorias</a></li>
                <li><a href="./eventos.php">Eventos</a></li>
                <li><a href="./sobre.php">Sobre</a></li>
                <li><a href="./contato.php">Contato</a></li>
            </ul>
        </nav>

        <div class="usuario">
            <a href="../php/usuario.php" aria-label="Acessar seu perfil, <?php echo htmlspecialchars($_SESSION['nome']); ?>">
                <!-- Carrega a foto de perfil do usuário dinamicamente -->
                <img src="exibir_foto.php?v=<?php echo time(); ?>" alt="Foto de perfil do usuário" width="60" height="60">
            </a>
        </div>
    </header>

    <!-- ======== CONTEÚDO PRINCIPAL ======== -->
    <main>
        <section class="categories-section">
            <h1>Busque Suas Categorias</h1>
            <p class="subtitle">
                Clique na estrela ★ para adicionar uma categoria aos seus favoritos!
            </p>

            <!-- ======== BARRA DE BUSCA ======== -->
            <div class="container">
                <div class="search-container">
                    <input type="text" class="search-bar" placeholder="Buscar categoria..."
                        aria-label="Buscar categoria">
                    <img src="../img/img-pesquisa (1).png" alt="" class="search-icon" aria-hidden="true">
                </div>

                <!-- ======== GRID DE CATEGORIAS ======== -->
                <div class="categories-grid">
                    <div class="category-card" data-category="Ciclismo">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['ciclismo'] ?? 0) ? 'favorited' : ''; ?>" aria-label="Adicionar aos favoritos"></i>
                        <img src="../img/bike.png" alt="Ciclismo" class="category-icon">
                        <span class="category-name">Ciclismo</span>
                    </div>
                    <div class="category-card" data-category="Futebol">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['futebol'] ?? 0) ? 'favorited' : ''; ?>" aria-label="Adicionar aos favoritos"></i>
                        <img src="../img/jogador.png" alt="Futebol" class="category-icon">
                        <span class="category-name">Futebol</span>
                    </div>
                    <div class="category-card" data-category="Voleibol">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['voleibol'] ?? 0) ? 'favorited' : ''; ?>" aria-label="Adicionar aos favoritos"></i>
                        <img src="../img/volei.png" alt="Voleibol" class="category-icon">
                        <span class="category-name">Voleibol</span>
                    </div>
                    <div class="category-card" data-category="Caminhada">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['caminhada'] ?? 0) ? 'favorited' : ''; ?>" aria-label="Adicionar aos favoritos"></i>
                        <img src="../img/caminhada.png" alt="Caminhada" class="category-icon">
                        <span class="category-name">Caminhada</span>
                    </div>
                    <div class="category-card" data-category="Academia">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['academia'] ?? 0) ? 'favorited' : ''; ?>" aria-label="Adicionar aos favoritos"></i>
                        <img src="../img/musculacao.png" alt="Academia" class="category-icon">
                        <span class="category-name">Academia</span>
                    </div>
                    <div class="category-card" data-category="Natacao">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['natacao'] ?? 0) ? 'favorited' : ''; ?>" aria-label="Adicionar aos favoritos"></i>
                        <img src="../img/natacao.png" alt="Natação" class="category-icon">
                        <span class="category-name">Natação</span>
                    </div>
                    <div class="category-card" data-category="Lazer">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['lazer'] ?? 0) ? 'favorited' : ''; ?>" aria-label="Adicionar aos favoritos"></i>
                        <img src="../img/lazer.png" alt="Lazer" class="category-icon">
                        <span class="category-name">Lazer</span>
                    </div>
                    <div class="category-card" data-category="pcd">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['pcd'] ?? 0) ? 'favorited' : ''; ?>" aria-label="Adicionar aos favoritos"></i>
                        <img src="../img/rodas.png" alt="Esportes para PCD" class="category-icon">
                        <span class="category-name">PCD</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ======== RODAPÉ ======== -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-columns">
                <div class="footer-about">
                    <h4>Sobre Nós</h4>
                    <p>O ChampionsSports conecta você aos melhores eventos esportivos e promoções na sua região.</p>
                </div>

                <div class="footer-links">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="../index.php">Início</a></li>
                        <li><a href="categorias.php">Categorias</a></li>
                        <li><a href="eventos.php">Eventos</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                        <li><a href="contato.php">Contato</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h4>Contato</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:contato@example.com">contato@example.com</a></li>
                        <li><i class="fas fa-phone"></i> <a href="tel:+5500000000000">+55 (00) 00000-0000</a></li>
                        <li><i class="fas fa-map-marker-alt"></i> Atendimento online</li>
                    </ul>
                </div>

                <div class="footer-social">
                    <h4>Siga-nos</h4>
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">© 2025 ChampionsSports. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- ======== SCRIPTS ======== -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const categoryCards = document.querySelectorAll(".category-card");
            const searchBar = document.querySelector(".search-bar");

            // Função para salvar o estado do favorito no servidor
            async function saveFavoriteState(category, isFavorited) {
                try {
                    const response = await fetch('salvar_favorito_categoria.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            category: category.toLowerCase(),
                            isFavorited: isFavorited
                        })
                    });

                    const responseText = await response.text();
                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch {
                        throw new Error('O servidor retornou uma resposta inválida ao salvar o favorito.');
                    }
                    if (!response.ok || result.status !== 'sucesso') {
                        throw new Error(result.mensagem || 'Erro ao salvar favorito.');
                    }

                    return true;

                } catch (error) {
                    console.error('Erro ao salvar favorito:', error);
                    alert(error.message || 'Não foi possível atualizar o favorito. Verifique sua conexão e tente novamente.');
                    return false; // Indica falha
                }
            }

            // Adiciona os eventos de clique para cada card
            categoryCards.forEach((card) => {
                const star = card.querySelector(".favorite-star");
                const category = card.dataset.category;

                star.addEventListener("click", async (e) => { // Tornar a função assíncrona
                    e.stopPropagation(); // Impede que o clique na estrela acione o clique no card
                    const isNowFavorited = !star.classList.contains("favorited");
                    
                    const success = await saveFavoriteState(category, isNowFavorited); // Aguarda o resultado
                    if (success) {
                        star.classList.toggle("favorited", isNowFavorited);
                        window.location.href = 'favoritos.php'; // Redireciona sempre após a ação
                    }
                });

                card.addEventListener("click", () => {
                    window.location.href = `eventos.php?game=${encodeURIComponent(category)}`;
                });
            });

            // Adiciona o evento de busca na barra de pesquisa
            searchBar.addEventListener("input", (e) => {
                const searchTerm = e.target.value.toLowerCase();
                categoryCards.forEach((card) => {
                    const categoryName = card.dataset.category.toLowerCase();
                    card.style.display = categoryName.includes(searchTerm) ? "" : "none";
                });
            });
        });
    </script>

</body>

</html>