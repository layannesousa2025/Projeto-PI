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
    $stmt = $pdo->prepare("SELECT ciclismo, futebol, voleibol, academia, caminhada, natacao, lazer, pcd, tenis FROM categoria WHERE id_cadastro_usuarios = ?");
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

    <!-- CSS -->
    <link rel="stylesheet" href="Css/categoria.css">
    <link rel="stylesheet" href="Css/ajustesAcessibilidade.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- ======== CABEÇALHO ======== -->
    <header class="header">
        <input type="checkbox" id="menu-toggle" class="menu-toggle">

        <a href="./telaPrincipal.php" class="logo">
            <img src="img/Logo.png" alt="Logo ChampionsSports" width="50" height="50">
            <p>ChampionsSports</p>
        </a>

        <label for="menu-toggle" class="menu-icon">
            <span class="hamburguer"></span>
        </label>

        <nav class="nav">
            <ul>
                <li><a href="telaPrincipal.php">Início</a></li>
                <li><a href="categorias.php">Categorias</a></li>
                <li><a href="eventos.html">Eventos</a></li>
                <li><a href="sobre.html">Sobre</a></li>
                <li><a href="contato.html">Contato</a></li>
            </ul>
        </nav>

      

        <div class="usuario">
            <a href="usuario.php">
                <img src="exibir_foto.php?v=<?php echo time(); ?>" alt="Foto do usuário" width="60" height="60" style="object-fit: cover; border-radius: 50%;">
            </a>
        </div>
    </header>

    <!-- ======== CONTEÚDO PRINCIPAL ======== -->
    <main>
        <section>
            <h2 class="page-title">Busque Suas Categorias</h2>
            <p class="page-subtitle">
                Clique na estrela ★ para adicionar uma categoria aos seus favoritos!
            </p>

            <!-- ======== ACESSIBILIDADE ======== -->
            <div class="paineis-acessibilidade">
                <!-- ============================= -->
                <!-- AJUSTES DE TEXTO -->
                    <div id="pertoTextAdMain" tabindex="-1"> 
                        <button id="pertoTextAdTop" tabindex="0" aria-expanded="false" aria-controls="pertoTextSizeOptions"
                            aria-label="Ajustes de Texto Pressione Enter para entrar, o tab para continuar">
                            <div id="pertoTAdTextImgTop">
                                <div style="font-size: 20px; font-weight: bold;">Aa</div>
                                <div>
                                    <span class="pertoTitle">Ajustes de Texto</span>
                                    <span class="pertoSubTitle">Tamanho, Espaçamento e Formatação de Texto</span>
                                </div>
                            </div>
                            <span style="font-size: 20px;">▼</span>
                        </button>

                        <div id="pertoTextSizeOptions" tabindex="-1" class="pertoOptions" style="max-height: 0px;">
                            <div class="pertoFontOptMain" tabindex="0" aria-label="Tamanho da Letra">
                                <span class="pertoTitle">Tamanho da Letra</span>
                                <div class="pertoFontOptSliderMain">
                                    <button tabindex="0" aria-label="diminuir Tamanho da Letra" class="pertoFontOptButton" id="perto_slider_letter_size_minus" operation="-">-</button>
                                    <div id="perto_font_slider_main_letter_size" class="pertoFontSliderMain">
                                        <input tabindex="-1" class="perto_text_slider" id="perto_font_slider_letter_size" type="range" min="0" max="100" value="0" step="10">
                                        <output for="perto_font_slider_letter_size" id="perto_font_slider_label_letter_size">0%</output>
                                    </div>
                                    <button tabindex="0" aria-label="aumentar Tamanho da Letra" class="pertoFontOptButton" id="perto_slider_letter_size_plus" operation="+">+</button>
                                </div>
                            </div>
                            <div class="pertoFontOptMain" tabindex="0" aria-label="Espaço Entre Linhas">
                                <span class="pertoTitle">Espaço Entre Linhas</span>
                                <div class="pertoFontOptSliderMain">
                                    <button tabindex="0" aria-label="diminuir Espaço Entre Linhas" class="pertoFontOptButton" id="perto_slider_line_spacing_minus" operation="-">-</button>
                                    <div id="perto_font_slider_main_line_spacing" class="pertoFontSliderMain">
                                        <input tabindex="-1" class="perto_text_slider" id="perto_font_slider_line_spacing" type="range" min="0" max="100" value="0" step="10">
                                        <output for="perto_font_slider_line_spacing" id="perto_font_slider_label_line_spacing">0%</output>
                                    </div>
                                    <button tabindex="0" aria-label="aumentar Espaço Entre Linhas" class="pertoFontOptButton" id="perto_slider_line_spacing_plus" operation="+">+</button>
                                </div>
                            </div>
                            <div class="pertoFontOptMain" tabindex="0" aria-label="Espaço Entre Letras">
                                <span class="pertoTitle">Espaço Entre Letras</span>
                                <div class="pertoFontOptSliderMain">
                                    <button tabindex="0" aria-label="diminuir Espaço Entre Letras" class="pertoFontOptButton" id="perto_slider_letter_spacing_minus" operation="-">-</button>
                                    <div id="perto_font_slider_main_letter_spacing" class="pertoFontSliderMain">
                                        <input tabindex="-1" class="perto_text_slider" id="perto_font_slider_letter_spacing" type="range" min="0" max="100" value="0" step="10">
                                        <output for="perto_font_slider_letter_spacing" id="perto_font_slider_label_letter_spacing">0%</output>
                                    </div>
                                    <button tabindex="0" aria-label="aumentar Espaço Entre Letras" class="pertoFontOptButton" id="perto_slider_letter_spacing_plus" operation="+">+</button>
                                </div>
                            </div>
                            <div class="pertoFontOptMain pertoIntFunctions">
                                <span class="pertoTitle">Funções</span>
                                <div class="pertoFontOpts pertoFunctions">
                                    <button class="pertoFunctionBtn" id="perto_func_format_fonts" tabindex="0">
                                        <div class="pertoTooltip" style="position: absolute; display: none;">Altera o estilo das letras para fontes mais fáceis de ler.</div>
                                        <span>Formatar Fontes</span>
                                    </button>
                                    <button class="pertoFunctionBtn" id="perto_func_underline_titles" tabindex="0">
                                        <div class="pertoTooltip" style="position: absolute; display: none;">Adiciona sublinhado aos títulos da página para destacá-los visualmente.</div>
                                        <span>Sublinhar Títulos</span>
                                    </button>
                                    <button class="pertoFunctionBtn" id="perto_func_uppercase_font" tabindex="0">
                                        <div class="pertoTooltip" style="position: absolute; display: none;">Transforma todos textos para letras maiúsculas a fim de facilitar a leitura.</div>
                                        <span>Fonte Maiúscula</span>
                                    </button>
                                    <button class="pertoFunctionBtn" id="perto_func_format_dyslexia" tabindex="0">
                                        <div class="pertoTooltip" style="position: absolute; display: none;">Ativa uma fonte desenvolvida para facilitar a leitura de pessoas com dislexia.</div>
                                        <span>Fonte para dislexia</span>
                                    </button>
                                    <button class="pertoFunctionBtn" id="perto_func_text_block_width" tabindex="0">
                                        <div class="pertoTooltip" style="position: absolute; display: none;">Ajusta a largura dos blocos de texto.</div>
                                        <span>Largura dos Blocos de Texto</span>
                                    </button>
                                    <button class="pertoFunctionBtn" id="perto_func_align_text" tabindex="0">
                                        <div class="pertoTooltip" style="position: absolute; display: none;">Permite ajustar o alinhamento do texto.</div>
                                        <span>Alinhamento</span>
                                        <div class="alignment-indicators">
                                            <div class="alignment-dot"></div>
                                            <div class="alignment-dot"></div>
                                            <div class="alignment-dot"></div>
                                            <div class="alignment-dot"></div>
                                        </div>
                                    </button>
                                    <button class="pertoFunctionBtn" id="perto_func_between_paragraphs" tabindex="0">
                                        <div class="pertoTooltip" style="position: absolute; display: none;">Aumenta o espaço entre parágrafos para melhorar a organização.</div>
                                        <span>Espaçamento Entre Parágrafos</span>
                                        <div class="alignment-indicators">
                                            <div class="alignment-dot"></div>
                                            <div class="alignment-dot"></div>
                                            <div class="alignment-dot"></div>
                                        </div>
                                    </button>
                                    <button class="pertoFunctionBtn" id="perto_func_between_words" tabindex="0">
                                        <div class="pertoTooltip" style="position: absolute; display: none;">Aumenta o espaço entre as palavras.</div>
                                        <span>Espaçamento Entre Palavras</span>
                                        <div class="alignment-indicators">
                                            <div class="alignment-dot"></div>
                                            <div class="alignment-dot"></div>
                                            <div class="alignment-dot"></div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AJUSTES DE CORES -->
                    <div id="pertoTextColorAdMain" tabindex="-1"> 
                        <button id="pertoTextColorAdTop" tabindex="0" aria-expanded="false" aria-controls="pertoTextColorOptions"
                            aria-label="Ajustes de Cores Pressione Enter para entrar, o tab para continuar">
                            <div id="pertoColorTextImgTop">
                                <div style="font-size: 20px;">🎨</div>
                                <div>
                                    <span class="pertoTitle">Ajustes de Cores</span>
                                    <span class="pertoSubTitle">Cor do Texto, Título, Fundo e Contraste</span>
                                </div>
                            </div>
                            <span style="font-size: 20px;">▼</span>
                        </button>

                        <div id="pertoTextColorOptions" tabindex="-1" class="pertoOptions" style="max-height: 0px;">
                            <div class="pertoColorOptMain pertoIntFunctions" key="color_main">
                                <span class="pertoTitle">Funções</span>
                                <div class="pertoColorsOpts pertoFunctions">
                                    <button class="pertoFunctionBtn" id="perto_func_high_contrast_dark" tabindex="0"><span>Alto Contraste Escuro</span></button>
                                    <button class="pertoFunctionBtn" id="perto_func_high_contrast_clear" tabindex="0"><span>Alto Contraste Claro</span></button>
                                    <button class="pertoFunctionBtn" id="perto_func_monochromatic" tabindex="0"><span>Monocromático</span></button>
                                    <button class="pertoFunctionBtn" id="perto_func_high_saturation" tabindex="0"><span>Alta Saturação</span></button>
                                    <button class="pertoFunctionBtn" id="perto_func_low_saturation" tabindex="0"><span>Baixa Saturação</span></button>
                                </div>
                            </div>
                            <div class="pertoColorOptMain" key="color_text_main" tabindex="0" aria-label="Cor do Texto">
                                <div>
                                    <span tabindex="-1" class="pertoTitle">Cor do Texto</span>
                                    <button class="pertoResetColorButton" data-target="text"><span>Restaurar Cor</span></button>
                                </div>
                                <div class="pertoColorsOpts">
                                    <button class="pertoColorOpt" style="background-color: #E9D985;" data-color="#E9D985" data-target="text"></button>
                                    <button class="pertoColorOpt" style="background-color: #E63462;" data-color="#E63462" data-target="text"></button>
                                    <button class="pertoColorOpt" style="background-color: #C7EFCF;" data-color="#C7EFCF" data-target="text"></button>
                                    <button class="pertoColorOpt" style="background-color: #FFA0FD;" data-color="#FFA0FD" data-target="text"></button>
                                    <button class="pertoColorOpt" style="background-color: #1a1a2e;" data-color="#197BBD" data-target="text"></button>
                                    <button class="pertoColorOpt" style="background-color: #000000;" data-color="#000000" data-target="text"></button>
                                    <button class="pertoColorOpt" style="background-color: #FFFFFF;" data-color="#FFFFFF" data-target="text"></button>
                                </div>
                            </div>
                            <div class="pertoColorOptMain" key="color_background_main" tabindex="0" aria-label="Cor do Fundo">
                                <div>
                                    <span tabindex="-1" class="pertoTitle">Cor do Fundo</span>
                                    <button class="pertoResetColorButton" data-target="background"><span>Restaurar Cor</span></button>
                                </div>
                                <div class="pertoColorsOpts">
                                    <button class="pertoColorOpt" style="background-color: #E9D985;" data-color="#E9D985" data-target="background"></button>
                                    <button class="pertoColorOpt" style="background-color: #E63462;" data-color="#E63462" data-target="background"></button>
                                    <button class="pertoColorOpt" style="background-color: #C7EFCF;" data-color="#C7EFCF" data-target="background"></button>
                                    <button class="pertoColorOpt" style="background-color: #FFA0FD;" data-color="#FFA0FD" data-target="background"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="container">

                <div class="search-container">
                    <input type="text" class="search-bar" placeholder="Buscar categoria...">
                    <img src="./img/img-pesquisa (1).png" alt="Buscar" class="search-icon">
                </div>

                <div class="categories-grid">
                    <div class="category-card" data-category="Ciclismo" data-db-column="ciclismo">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['ciclismo'] ?? 0) == 1 ? 'favorited' : ''; ?>"></i>
                        <img src="./img/bike.png" alt="Ícone de Ciclismo" class="category-icon">
                        <span class="category-name">Ciclismo</span>
                    </div>
                    <div class="category-card" data-category="Futebol" data-db-column="futebol">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['futebol'] ?? 0) == 1 ? 'favorited' : ''; ?>"></i>
                        <img src="./img/jogador.png" alt="Ícone de Futebol" class="category-icon">
                        <span class="category-name">Futebol</span>
                    </div>
                    <div class="category-card" data-category="Voleibol" data-db-column="voleibol">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['voleibol'] ?? 0) == 1 ? 'favorited' : ''; ?>"></i>
                        <img src="./img/volei.png" alt="Ícone de Voleibol" class="category-icon">
                        <span class="category-name">Voleibol</span>
                    </div>
                    <div class="category-card" data-category="Academia" data-db-column="academia">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['academia'] ?? 0) == 1 ? 'favorited' : ''; ?>"></i>
                        <img src="./img/musculacao.png" alt="Ícone de Musculação" class="category-icon">
                        <span class="category-name">Academia</span>
                    </div>
                    <div class="category-card" data-category="Caminhada" data-db-column="caminhada">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['caminhada'] ?? 0) == 1 ? 'favorited' : ''; ?>"></i>
                        <img src="./img/caminhada.png" alt="Ícone de Caminhada" class="category-icon">
                        <span class="category-name">Caminhada</span>
                    </div>
                    <div class="category-card" data-category="Natação" data-db-column="natacao">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['natacao'] ?? 0) == 1 ? 'favorited' : ''; ?>"></i>
                        <img src="./img/natacao.png" alt="Ícone de Natação" class="category-icon">
                        <span class="category-name">Natação</span>
                    </div>
                    <div class="category-card" data-category="Lazer" data-db-column="lazer">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['lazer'] ?? 0) == 1 ? 'favorited' : ''; ?>"></i>
                        <img src="./img/lazer.png" alt="Ícone de Lazer" class="category-icon">
                        <span class="category-name">Lazer</span>
                    </div>
                    <div class="category-card" data-category="PCD" data-db-column="pcd">
                        <i class="fas fa-star favorite-star <?php echo ($favoritos_usuario['pcd'] ?? 0) == 1 ? 'favorited' : ''; ?>"></i>
                        <img src="./img/rodas.png" alt="Ícone de Esportes Adaptados (PCD)" class="category-icon">
                        <span class="category-name">PCD</span>
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
                        <li><a href="telaPrincipal.php#home">Início</a></li>
                        <li><a href="telaPrincipal.php#search">Buscar Eventos</a></li>
                        <li><a href="telaPrincipal.php#events">Eventos</a></li>
                        <li><a href="telaPrincipal.php#chatbot">Chatbot</a></li>
                        <li><a href="#">Termos de Uso</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h4>Contato</h4>
                    <ul>
                        <li>Email: contato@ChampionsSports.com</li>
                        <li>Telefone: (61) 99999-9999</li>
                        <li>Endereço: QNL - Taguatinga, Brasília/DF</li>
                    </ul>
                </div>

                <div class="footer-social">
                    <h4>Siga-nos</h4>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-discord"></i></a>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© 2025 ChampionsSports. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- ======== SCRIPTS ======== -->
    <script src="Js/ajustesAcessibilidade01.js"></script>

    <script>
        // Script separado para maior clareza
        document.addEventListener('DOMContentLoaded', () => {
            const categoryCards = document.querySelectorAll('.category-card');
            const searchBar = document.querySelector('.search-bar');

            // Função para salvar o estado do favorito no banco de dados
            async function saveFavoriteState(categoryDbColumn, isFavorited, starElement) {
                try {
                    const response = await fetch('salvar_favorito_categoria.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ category: categoryDbColumn, isFavorited: isFavorited }),
                    });

                    if (!response.ok) {
                        const result = await response.json();
                        throw new Error(result.mensagem || 'Falha ao salvar favorito.');
                    }
                    // Sucesso, não precisa fazer nada.
                } catch (error) {
                    console.error('Erro ao salvar favorito:', error);
                    alert('Ocorreu um erro ao salvar seu favorito. Por favor, recarregue a página e tente novamente.');
                    // Reverte a mudança visual na estrela em caso de erro
                    starElement.classList.toggle('favorited');
                }
            }

            categoryCards.forEach(card => {
                const categoryNameForUrl = card.dataset.category;
                const categoryDbColumn = card.dataset.dbColumn;
                const star = card.querySelector('.favorite-star');

                star.addEventListener('click', (e) => {
                    e.stopPropagation();
                    star.classList.toggle('favorited');
                    const isNowFavorited = star.classList.contains('favorited');
                    saveFavoriteState(categoryDbColumn, isNowFavorited, star);
                });

                card.addEventListener('click', () => {
                    window.location.href = `eventos.html?game=${categoryNameForUrl}`;
                });
            });

            searchBar.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                categoryCards.forEach(card => {
                    const name = card.dataset.category.toLowerCase();
                    card.style.display = name.includes(term) ? '' : 'none';
                });
            });
        });
    </script>
</body>

</html>