<?php
session_start(); // Inicia a sessão

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Mapeamento de nomes de coluna para nomes de exibição e imagens
$allCategories = [
    'ciclismo' => ['nome' => 'Ciclismo', 'img' => '../img/bike.png'],
    'futebol' => ['nome' => 'Futebol', 'img' => '../img/jogador.png'],
    'voleibol' => ['nome' => 'Voleibol', 'img' => '../img/volei.png'],
    'academia' => ['nome' => 'Academia', 'img' => '../img/musculacao.png'],
    'caminhada' => ['nome' => 'Caminhada', 'img' => '../img/caminhada.png'],
    'natacao' => ['nome' => 'Natação', 'img' => '../img/natacao.png'],
    'lazer' => ['nome' => 'Lazer', 'img' => '../img/lazer.png'],
    'pcd' => ['nome' => 'PCD', 'img' => '../img/rodas.png']
];

$favoritos_usuario = [];
$favorites_error = false;
$id_usuario = $_SESSION['id_cadastro_usuario'] ?? (
    ($_SESSION['tipo_usuario'] ?? '') === 'Admin' ? null : ($_SESSION['id'] ?? null)
);

if ($id_usuario !== null || ($_SESSION['tipo_usuario'] ?? '') === 'Admin') {
    try {
        // Usa o ID do cadastro do usuário para buscar suas categorias favoritas.
        require_once __DIR__ . '/database.php';
        $pdo = champions_pdo();

        if ($id_usuario === null) {
            $stmtUsuario = $pdo->prepare("SELECT id_cadastro_usuario FROM cadastro_usuario WHERE id_login = ? LIMIT 1");
            $stmtUsuario->execute([$_SESSION['id'] ?? null]);
            $id_usuario = $stmtUsuario->fetchColumn() ?: null;
        }

        if ($id_usuario !== null) {
            $sql = "SELECT MAX(ciclismo) AS ciclismo, MAX(futebol) AS futebol, MAX(voleibol) AS voleibol, MAX(academia) AS academia, MAX(caminhada) AS caminhada, MAX(natacao) AS natacao, MAX(lazer) AS lazer, MAX(pcd) AS pcd FROM categoria WHERE id_cadastro_usuario = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_usuario]);
            $categorias = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categorias) {
                foreach ($categorias as $nome_categoria => $is_favorito) {
                    if ($is_favorito == 1) {
                        $favoritos_usuario[] = $nome_categoria;
                    }
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Erro ao carregar favoritos: " . $e->getMessage());
        $favorites_error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php require_once __DIR__ . '/tailwind.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus favoritos - ChampionsSports</title>
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
        html { min-width: 320px; min-height: 100%; }

        body {
            min-height: 100vh;
            margin: 0;
            padding-bottom: 56px;
            background:
                radial-gradient(ellipse at 80% 8%, rgba(110, 0, 255, .2), transparent 38rem),
                var(--page-bg);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-favoritos {
            display: flex;
            min-height: 74px;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 12px max(20px, calc((100vw - 1120px) / 2));
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            background: rgba(17, 17, 31, .88);
        }

        .btn-voltar {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            gap: 9px;
            padding: 0 15px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 999px;
            color: var(--text);
            text-decoration: none;
            transition: border-color .2s ease, background .2s ease;
        }

        .btn-voltar:hover { border-color: var(--secondary); background: rgba(255, 255, 255, .06); }

        .foto-perfil {
            display: block;
            width: 44px;
            height: 44px;
            border: 2px solid rgba(255, 0, 170, .7);
            border-radius: 50%;
            object-fit: cover;
        }

        .favorites-title {
            margin: 54px 16px 10px;
            text-align: center;
            font-size: clamp(1.9rem, 5vw, 2.8rem);
            line-height: 1.2;
            letter-spacing: -.04em;
        }

        .page-subtitle {
            max-width: 720px;
            margin: 0 auto 34px;
            padding: 0 16px;
            color: var(--muted);
            text-align: center;
            line-height: 1.65;
        }

        .container { width: min(1080px, calc(100% - 40px)); margin: 0 auto; }

        .favorites-grid-flex {
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

        .category-icon { width: 82px; height: 82px; object-fit: contain; filter: brightness(0) invert(1); }
        .category-name { color: var(--text); font-size: 1.05rem; font-weight: 650; }

        .favorite-star {
            position: absolute;
            top: 13px;
            right: 15px;
            z-index: 1;
            padding: 6px;
            color: #ffd166;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color .2s ease, transform .2s ease;
        }

        .favorite-star:hover { color: #fff; transform: scale(1.16); }
        .favorite-star:focus-visible { border-radius: 4px; outline: 2px solid #fff; outline-offset: 2px; }

        .empty-state, .error-state {
            grid-column: 1 / -1;
            padding: 44px 20px;
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 16px;
            color: var(--muted);
            text-align: center;
        }

        .empty-state i { display: block; margin-bottom: 14px; color: #ff70cf; font-size: 2rem; }
        .empty-state p, .error-state p { margin: 0; line-height: 1.6; }
        .error-state { border-color: rgba(248, 113, 113, .35); color: #fecaca; }

        @media (max-width: 850px) {
            .favorites-grid-flex { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        }

        @media (max-width: 620px) {
            .header-favoritos { min-height: 66px; padding: 10px 16px; }
            .favorites-title { margin-top: 40px; }
            .favorites-grid-flex { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
            .category-card { min-height: 160px; }
            .category-icon { width: 68px; height: 68px; }
        }

        @media (max-width: 380px) {
            .container { width: calc(100% - 28px); }
            .favorites-grid-flex { gap: 10px; }
            .category-card { min-height: 145px; padding-inline: 12px; }
            .category-name { font-size: .95rem; }
        }
    </style>
</head>

<body>
    <!-- Cabeçalho com botão de voltar e foto do usuário -->
    <header class="header-favoritos">
        <div class="voltar-container">
            <a href="categorias.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="usuario-container">
            <a href="usuario.php" aria-label="Acessar seu perfil">
                <!-- Adiciona um parâmetro 'v' com o timestamp atual para evitar cache do navegador -->
                <img src="exibir_foto.php?v=<?php echo time(); ?>" alt="Foto de perfil do usuário" class="foto-perfil">
            </a>
        </div>
    </header>


    <h1 class="favorites-title">Suas categorias favoritas</h1>

    <p class="page-subtitle">
        Clique na estrela para remover dos favoritos ou no card para ver os eventos.
    </p>


    
    <div class="container">
        <div class="favorites-grid-flex" id="favorites-grid">
            <?php if ($favorites_error): ?>
                <div id="favorites-error-message" class="error-state" role="alert"><p>Não foi possível carregar seus favoritos. Tente novamente mais tarde.</p></div>
            <?php elseif (empty($favoritos_usuario)): ?>
                <div id="no-favorites-message" class="empty-state">
                    <i class="fa-regular fa-star" aria-hidden="true"></i>
                    <p>Você ainda não adicionou nenhuma categoria aos favoritos.</p>
                    <p>Volte à página de categorias para escolher seus esportes.</p>
                </div>
            <?php else: ?>
                <?php foreach ($favoritos_usuario as $db_column): ?>
                    <?php if (isset($allCategories[$db_column])): 
                        $category_info = $allCategories[$db_column]; ?>
                        <div class="category-card" data-category="<?php echo htmlspecialchars($db_column, ENT_QUOTES, 'UTF-8'); ?>" role="link" tabindex="0" aria-label="Ver eventos de <?php echo htmlspecialchars($category_info['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fas fa-star favorite-star favorited" role="button" tabindex="0" aria-label="Remover <?php echo htmlspecialchars($category_info['nome'], ENT_QUOTES, 'UTF-8'); ?> dos favoritos"></i>
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
                if (event.target.closest('.favorite-star')) {
                    event.stopPropagation(); // Impede que o clique na estrela acione o clique no card
                    const star = card.querySelector('.favorite-star');
                    star.style.pointerEvents = 'none';
                    try {
                        await saveFavoriteState(categoryDbColumn, false);
                        card.remove();
                        if (!favoritesGrid.querySelector('.category-card')) {
                            favoritesGrid.innerHTML = '<div id="no-favorites-message" class="empty-state"><i class="fa-regular fa-star" aria-hidden="true"></i><p>Você não tem mais categorias favoritas.</p><p>Volte à página de categorias para escolher seus esportes.</p></div>';
                        }
                    } catch (error) {
                        console.error('Erro ao remover favorito:', error);
                        alert(error.message || 'Não foi possível remover o favorito. Tente novamente.');
                    } finally {
                        star.style.pointerEvents = '';
                    }
                } else {
                    // Se o clique foi no card (mas não na estrela), navega para a página de eventos
                    const categoryName = card.querySelector('.category-name').textContent; // Pega o nome de exibição (ex: 'Ciclismo')
                    window.location.href = `eventos.php?game=${encodeURIComponent(categoryName)}`;
                }
            });

            // Função para salvar/remover o estado do favorito no banco de dados
            async function saveFavoriteState(category, isFavorited) {
                const response = await fetch('salvar_favorito_categoria.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ category: category.toLowerCase(), isFavorited })
                });
                let result;
                try {
                    result = await response.json();
                } catch {
                    throw new Error('O servidor retornou uma resposta inválida ao atualizar o favorito.');
                }
                if (!response.ok || result.status !== 'sucesso') {
                    throw new Error(result.mensagem || 'Falha ao atualizar favorito.');
                }
            }
        });
    </script>

</body>

</html>