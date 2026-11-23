<?php
session_start(); // Inicia a sessão

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { 
    header('Location: login.php');
    exit; 
}
?>

<?php

// Busca as categorias favoritas do usuário no banco para marcar as estrelas
$favoritos_usuario = [];
try {
    require_once __DIR__ . '/database.php';
    $pdo = champions_pdo();
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
    <?php require_once __DIR__ . '/tailwind.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - ChampionsSports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            color-scheme: dark;
            --page-bg: #11111f;
            --surface: #1d1d31;
            --surface-soft: #26263d;
            --text: #f8f9fa;
            --muted: #b8b8c9;
            --primary: #6e00ff;
            --secondary: #ff00aa;
        }

        * { box-sizing: border-box; }

        html { min-width: 320px; scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            background:
                radial-gradient(ellipse at 80% 10%, rgba(110, 0, 255, .18), transparent 38rem),
                var(--page-bg);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        a { color: inherit; }

        .header {
            position: sticky;
            z-index: 10;
            top: 0;
            display: flex;
            min-height: 76px;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 12px max(24px, calc((100vw - 1180px) / 2));
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            background: rgba(17, 17, 31, .94);
            backdrop-filter: blur(14px);
        }

        .logo {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 10px;
            color: var(--text);
            font-size: 1.05rem;
            font-weight: 700;
            text-decoration: none;
        }

        .logo img { width: 44px; height: 44px; object-fit: contain; }

        .nav ul {
            display: flex;
            align-items: center;
            gap: clamp(12px, 2.2vw, 30px);
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .nav a {
            color: var(--muted);
            font-size: .95rem;
            text-decoration: none;
            transition: color .2s ease;
        }

        .nav a:hover, .nav a.active { color: #ff70cf; }

        .menu-toggle, .menu-icon { display: none; }

        .usuario { flex: 0 0 auto; }

        .usuario img {
            display: block;
            width: 44px;
            height: 44px;
            border: 2px solid rgba(255, 0, 170, .7);
            border-radius: 50%;
            object-fit: cover;
        }

        .categories-section {
            width: min(1120px, calc(100% - 40px));
            min-height: calc(100vh - 250px);
            margin: 0 auto;
            padding: clamp(48px, 8vw, 88px) 0 80px;
        }

        .categories-section h1 {
            margin: 0;
            text-align: center;
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.15;
            letter-spacing: -.04em;
        }

        .subtitle {
            margin: 14px 0 36px;
            color: var(--muted);
            text-align: center;
            font-size: 1rem;
            line-height: 1.6;
        }

        .container { width: min(100%, 1000px); margin: 0 auto; }

        .search-container {
            position: relative;
            width: min(100%, 560px);
            margin: 0 auto 36px;
        }

        .search-bar {
            width: 100%;
            min-height: 52px;
            padding: 0 54px 0 18px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            outline: none;
            background: rgba(255, 255, 255, .07);
            color: var(--text);
            font: inherit;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .search-bar::placeholder { color: #a5a5b9; }
        .search-bar:focus { border-color: var(--secondary); background: rgba(255, 255, 255, .1); box-shadow: 0 0 0 3px rgba(255, 0, 170, .16); }

        .search-icon {
            position: absolute;
            top: 50%;
            right: 17px;
            width: 21px;
            height: 21px;
            object-fit: contain;
            opacity: .72;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        .category-card {
            position: relative;
            display: flex;
            min-height: 190px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding: 25px 18px 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 18px;
            background: linear-gradient(145deg, rgba(38, 38, 61, .98), rgba(29, 29, 49, .98));
            box-shadow: 0 12px 30px rgba(0, 0, 0, .2);
            cursor: pointer;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 0, 170, .55);
            box-shadow: 0 18px 36px rgba(0, 0, 0, .3), 0 0 24px rgba(110, 0, 255, .13);
        }

        .category-icon {
            width: 82px;
            height: 82px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .category-name { font-size: 1.05rem; font-weight: 650; }

        .favorite-star {
            position: absolute;
            top: 13px;
            right: 15px;
            z-index: 1;
            padding: 5px;
            color: #9292a5;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color .2s ease, transform .2s ease;
        }

        .favorite-star:hover { color: #ffd166; transform: scale(1.16); }
        .favorite-star.favorited { color: #ffd166; }

        footer {
            border-top: 1px solid rgba(255, 255, 255, .08);
            background: #0c0c17;
        }

        .footer-content { width: min(1120px, calc(100% - 40px)); margin: 0 auto; }

        .footer-columns {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1.4fr .8fr;
            gap: 32px;
            padding: 42px 0 30px;
        }

        .footer-columns h4 { margin: 0 0 14px; font-size: 1rem; }
        .footer-columns p, .footer-columns li { color: var(--muted); font-size: .9rem; line-height: 1.7; }
        .footer-columns p { margin: 0; }
        .footer-columns ul { margin: 0; padding: 0; list-style: none; }
        .footer-columns a { color: var(--muted); text-decoration: none; }
        .footer-columns a:hover { color: #ff70cf; }
        .footer-social a { margin-right: 12px; font-size: 1.25rem; }

        .footer-bottom { padding: 16px 0; border-top: 1px solid rgba(255, 255, 255, .08); text-align: center; }
        .copyright { margin: 0; color: var(--muted); font-size: .82rem; }

        @media (max-width: 760px) {
            .header { flex-wrap: wrap; gap: 14px; padding: 12px 20px; }
            .logo { margin-right: auto; }
            .menu-icon { display: block; order: 3; padding: 8px; cursor: pointer; }
            .hamburguer, .hamburguer::before, .hamburguer::after {
                display: block;
                width: 23px;
                height: 2px;
                border-radius: 2px;
                background: var(--text);
            }
            .hamburguer { position: relative; }
            .hamburguer::before, .hamburguer::after { position: absolute; content: ""; }
            .hamburguer::before { top: -7px; }
            .hamburguer::after { top: 7px; }
            .nav { display: none; order: 4; width: 100%; }
            .nav ul { flex-direction: column; align-items: stretch; gap: 0; }
            .nav li { border-top: 1px solid rgba(255, 255, 255, .08); }
            .nav a { display: block; padding: 12px 4px; }
            .menu-toggle:checked ~ .nav { display: block; }
            .usuario img { width: 40px; height: 40px; }
            .categories-section { width: min(100% - 32px, 600px); padding-top: 48px; }
            .categories-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
            .category-card { min-height: 160px; }
            .category-icon { width: 68px; height: 68px; }
            .footer-columns { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px 20px; }
        }

        @media (max-width: 420px) {
            .categories-grid { gap: 10px; }
            .category-card { min-height: 145px; padding-inline: 12px; }
            .category-name { font-size: .95rem; }
            .footer-columns { grid-template-columns: 1fr; }
        }
    </style>
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
                        <li><i class="fas fa-envelope"></i> <a href="mailto:contato@ChampionsSports.com">contato@ChampionsSports.com</a></li>
                        <li><i class="fas fa-phone"></i> <a href="tel:+5561999999999">(61) 99999-9999</a></li>
                        <li><i class="fas fa-map-marker-alt"></i> QNL-5657575 - Taguatinga, Brasília-DF</li>
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
                        window.location.href = 'favoritos.php';
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