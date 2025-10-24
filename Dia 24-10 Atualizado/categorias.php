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
    // Usar 127.0.0.1 e a porta 3309 para consistência com outras conexões no projeto
    $pdo = new PDO("mysql:host=127.0.0.1;port=3309;dbname=banco_teste01;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT ciclismo, futebol, voleibol, academia, caminhada, natacao, lazer, pcd FROM categoria WHERE id_cadastro_usuarios = ?");
    $stmt->execute([$_SESSION['id_cadastro_usuario']]);
    $favoritos_usuario = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    // Melhor tratamento de erro: informa o usuário sobre o problema no banco de dados.
    error_log("Erro ao buscar favoritos em categorias.php: " . $e->getMessage());
    if (str_contains($e->getMessage(), 'refused it') || $e->getCode() === 2002) {
        http_response_code(500);
        die("❌ Falha na conexão: O servidor MySQL parece estar desligado ou inacessível na porta 3309. 
            Por favor, inicie-o no painel do XAMPP e tente novamente.");
    } else {
        http_response_code(500);
        die("Erro ao conectar ou consultar o banco de dados para categorias: " . $e->getMessage());
    }
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
    <link rel="stylesheet" href="Css/ajustesAcessibilidade.css">
</head>

<body>
    <header class="header" >
    <!-- Checkbox para controlar o menu mobile -->
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
  </header>
   
    <section>
    <h1>

        <div class="usuario" float: right;>
            <a href="usuario.php">
                <!-- Adiciona um parâmetro 'v' com o timestamp atual para evitar cache do navegador -->
                <img src="exibir_foto.php?v=<?php echo time(); ?>" alt="Foto do usuário" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
            </a>

        </div>

    </h1>

         <h2 style="text-align: center; color: white; gap: 20px;">Busque Suas Categorias</h2>
            <p style="text-align: center; color: #5d2e5f; margin-top: -10px; margin-bottom: 20px;">
                Clique na estrela ★ para adicionar uma categoria aos seus favoritos!
            </p>
   


    <div class="container">


        <div class="search-container">
            <input type="text" class="search-bar" placeholder="Buscar categoria...">
            <img src="./img/img-pesquisa (1).png" alt="Buscar" class="search-icon">
        </div>

          <div>


    <!-- NOVO PAINEL DE AJUSTES DE TEXTO -->
    <div class="paineis-acessibilidade" style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
      <div id="pertoTextAdMain" tabindex="-1" class="">
        <button aria-label="Ajustes de Texto Pressione Enter para entrar, o tab para continuar" id="pertoTextAdTop" tabindex="0">
            <div id="pertoTAdTextImgTop">
                <!-- Ícone principal simplificado -->
                <div style="font-size: 20px; font-weight: bold;">Aa</div>
                <div>
                    <span class="pertoTitle">Ajustes de Texto</span>
                    <span class="pertoSubTitle">Tamanho, Espaçamento e Formatação de Texto</span>
                </div>
            </div>
            <!-- Ícone de abrir/fechar simplificado -->
            <span style="font-size: 20px;">▼</span>
        </button>

        <!-- Painel de Opções (removi o style="max-height: 0px;" para visualização) -->
        <div id="pertoTextSizeOptions" tabindex="-1" class="pertoOptions" style="max-height: 0px;">
            <div class="pertoFontOptMain" tabindex="0" aria-label="Tamanho da Letra">
                <span class="pertoTitle">Tamanho da Letra</span>
                <div class="pertoFontOptSliderMain">
                    <button tabindex="0" aria-label="diminuir Tamanho da Letra" class="pertoFontOptButton"
                        id="perto_slider_letter_size_minus" operation="-">
                        -
                    </button>
                    <div id="perto_font_slider_main_letter_size" class="pertoFontSliderMain">
                        <input tabindex="-1" class="perto_text_slider" id="perto_font_slider_letter_size" type="range"
                            min="0" max="100" value="0" step="10">
                        <output for="perto_font_slider_letter_size" id="perto_font_slider_label_letter_size">0%</output>
                    </div>
                    <button tabindex="0" aria-label="aumentar Tamanho da Letra" class="pertoFontOptButton"
                        id="perto_slider_letter_size_plus" operation="+">
                        +
                    </button>
                </div>
            </div>
            <div class="pertoFontOptMain" tabindex="0" aria-label="Espaço Entre Linhas">
                <span class="pertoTitle">Espaço Entre Linhas</span>
                <div class="pertoFontOptSliderMain">
                    <button tabindex="0" aria-label="diminuir Espaço Entre Linhas" class="pertoFontOptButton"
                        id="perto_slider_line_spacing_minus" operation="-">
                        -
                    </button>
                    <div id="perto_font_slider_main_line_spacing" class="pertoFontSliderMain">
                        <input tabindex="-1" class="perto_text_slider" id="perto_font_slider_line_spacing" type="range"
                            min="0" max="100" value="0" step="10">
                        <output for="perto_font_slider_line_spacing"
                            id="perto_font_slider_label_line_spacing">0%</output>
                    </div>
                    <button tabindex="0" aria-label="aumentar Espaço Entre Linhas" class="pertoFontOptButton"
                        id="perto_slider_line_spacing_plus" operation="+">
                        +
                    </button>
                </div>
            </div>
            <div class="pertoFontOptMain" tabindex="0" aria-label="Espaço Entre Letras">
                <span class="pertoTitle">Espaço Entre Letras</span>
                <div class="pertoFontOptSliderMain">
                    <button tabindex="0" aria-label="diminuir Espaço Entre Letras" class="pertoFontOptButton"
                        id="perto_slider_letter_spacing_minus" operation="-">
                        -
                    </button>
                    <div id="perto_font_slider_main_letter_spacing" class="pertoFontSliderMain">
                        <input tabindex="-1" class="perto_text_slider" id="perto_font_slider_letter_spacing"
                            type="range" min="0" max="100" value="0" step="10">
                        <output for="perto_font_slider_letter_spacing"
                            id="perto_font_slider_label_letter_spacing">0%</output>
                    </div>
                    <button tabindex="0" aria-label="aumentar Espaço Entre Letras" class="pertoFontOptButton"
                        id="perto_slider_letter_spacing_plus" operation="+">
                        +
                    </button>
                </div>
            </div>
            <div class="pertoFontOptMain pertoIntFunctions">
                <span class="pertoTitle">Funções</span>
                <div class="pertoFontOpts pertoFunctions">
                    <!-- Ícones dos botões foram removidos para simplificar. Eles podem ser adicionados via CSS ou com <img> -->
                    <button class="pertoFunctionBtn" id="perto_func_format_fonts" tabindex="0">
                        <div class="pertoTooltip" style="position: absolute; display: none;">Altera o estilo das letras
                            para fontes mais fáceis de ler.</div>
                        <span>Formatar Fontes</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_underline_titles" tabindex="0">
                        <div class="pertoTooltip" style="position: absolute; display: none;">Adiciona sublinhado aos
                            títulos da página para destacá-los visualmente.</div>
                        <span>Sublinhar Títulos</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_uppercase_font" tabindex="0">
                        <div class="pertoTooltip" style="position: absolute; display: none;">Transforma todos textos
                            para letras maiúsculas a fim de facilitar a leitura.</div>
                        <span>Fonte Maiúscula</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_format_dyslexia" tabindex="0">
                        <div class="pertoTooltip" style="position: absolute; display: none;">Ativa uma fonte
                            desenvolvida para facilitar a leitura de pessoas com dislexia.</div>
                        <span>Fonte para dislexia</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_text_block_width" tabindex="0">
                        <div class="pertoTooltip" style="position: absolute; display: none;">Ajusta a largura dos blocos
                            de texto.</div>
                        <span>Largura dos Blocos de Texto</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_align_text" tabindex="0">
                        <div class="pertoTooltip" style="position: absolute; display: none;">Permite ajustar o
                            alinhamento do texto.</div>
                        <span>Alinhamento</span>
                        <div class="alignment-indicators">
                            <div class="alignment-dot"></div>
                            <div class="alignment-dot"></div>
                            <div class="alignment-dot"></div>
                            <div class="alignment-dot"></div>
                        </div>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_between_paragraphs" tabindex="0">
                        <div class="pertoTooltip" style="position: absolute; display: none;">Aumenta o espaço entre
                            parágrafos para melhorar a organização.</div>
                        <span>Espaçamento Entre Parágrafos</span>
                        <div class="alignment-indicators">
                            <div class="alignment-dot"></div>
                            <div class="alignment-dot"></div>
                            <div class="alignment-dot"></div>
                        </div>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_between_words" tabindex="0">
                        <div class="pertoTooltip" style="position: absolute; display: none;">Aumenta o espaço entre as
                            palavras.</div>
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

      <!-- NOVO PAINEL DE AJUSTES DE CORES -->
      <div id="pertoTextColorAdMain" tabindex="-1" class="">
        <button id="pertoTextColorAdTop" aria-label="Ajustes de Cores Pressione Enter para entrar, o tab para continuar" tabindex="0">
            <div id="pertoColorTextImgTop">
                <!-- Ícone simplificado -->
                <div style="font-size: 20px;">🎨</div>
                <div>
                    <span class="pertoTitle">Ajustes de Cores</span>
                    <span class="pertoSubTitle">Cor do Texto, Título, Fundo e Contraste</span>
                </div>
            </div>
            <!-- Ícone de abrir/fechar simplificado -->
            <span style="font-size: 20px;">▼</span>
        </button>

        <div id="pertoTextColorOptions" tabindex="-1" class="pertoOptions" style="max-height: 0px;">
            <div class="pertoColorOptMain pertoIntFunctions" key="color_main">
                <span class="pertoTitle">Funções</span>
                <div class="pertoColorsOpts pertoFunctions">
                    <button class="pertoFunctionBtn" id="perto_func_high_contrast_dark" tabindex="0">
                        <span>Alto Contraste Escuro</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_high_contrast_clear" tabindex="0">
                        <span>Alto Contraste Claro</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_monochromatic" tabindex="0">
                        <span>Monocromático</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_high_saturation" tabindex="0">
                        <span>Alta Saturação</span>
                    </button>
                    <button class="pertoFunctionBtn" id="perto_func_low_saturation" tabindex="0">
                        <span>Baixa Saturação</span>
                    </button>
                </div>
            </div>

            <div class="pertoColorOptMain" key="color_text_main" tabindex="0" aria-label="Cor do Texto">
                <div>
                    <span tabindex="-1" class="pertoTitle">Cor do Texto</span>
                    <button class="pertoResetColorButton" data-target="text"><span>Restaurar Cor</span></button>
                </div>
                <div class="pertoColorsOpts">
                    <button class="pertoColorOpt" style="background-color: #E9D985;" data-color="#E9D985"
                        data-target="text"></button>
                    <button class="pertoColorOpt" style="background-color: #E63462;" data-color="#E63462"
                        data-target="text"></button>
                    <button class="pertoColorOpt" style="background-color: #C7EFCF;" data-color="#C7EFCF"
                        data-target="text"></button>
                    <button class="pertoColorOpt" style="background-color: #FFA0FD;" data-color="#FFA0FD"
                        data-target="text"></button>
                    <button class="pertoColorOpt" style="background-color: #197BBD;" data-color="#197BBD"
                        data-target="text"></button>
                    <button class="pertoColorOpt" style="background-color: #000000;" data-color="#000000"
                        data-target="text"></button>
                    <button class="pertoColorOpt" style="background-color: #FFFFFF;" data-color="#FFFFFF"
                        data-target="text"></button>
                </div>
            </div>

            <div class="pertoColorOptMain" key="color_background_main" tabindex="0" aria-label="Cor do Fundo">
                <div><span tabindex="-1" class="pertoTitle">Cor do Fundo</span>
                    <button class="pertoResetColorButton" data-target="background"><span>Restaurar Cor</span></button>
                </div>
                <div class="pertoColorsOpts">
                    <button class="pertoColorOpt" style="background-color: #E9D985;" data-color="#E9D985"
                        data-target="background"></button>
                    <button class="pertoColorOpt" style="background-color: #E63462;" data-color="#E63462"
                        data-target="background"></button>
                    <button class="pertoColorOpt" style="background-color: #C7EFCF;" data-color="#C7EFCF"
                        data-target="background"></button>
                    <button class="pertoColorOpt" style="background-color: #FFA0FD;" data-color="#FFA0FD"
                        data-target="background"></button>
                </div>
            </div>
        </div>
    </div>
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
</section>

       
<footer id="contact" style="margin-top: 50px;">
    <div class="footer-content">
      <div class="footer-columns">
        <div class="footer-about">
          <h4>Sobre Nós</h4>
          <p>O ChampionsSports conecta você aos melhores eventos esportivos e promoções na sua região.</p>
        </div>
        <div class="footer-links">
          <h4>Links Rápidos</h4>
          <ul>
            <li><a href="telaPrincipal.php">Início</a></li>
            <li><a href="categorias.php">Categorias</a></li>
            <li><a href="eventos.html">Eventos</a></li>
            <li><a href="sobre.html">Sobre</a></li>
            <li><a href="#">Termos de Uso</a></li>
            <li><a href="#">Política de Privacidade</a></li>
          </ul>
        </div>
        <div class="footer-contact">
          <h4>Contato</h4>
          <ul style="list-style: none; padding: 0;">
            <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;"><i class="fas fa-envelope"></i> <a href="mailto:contato@ChampionsSports.com">contato@ChampionsSports.com</a></li>
            <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;"><i class="fas fa-phone"></i> <a href="tel:+5561999999999">(61) 99999-9999</a></li>
            <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;"><i class="fas fa-map-marker-alt"></i> Qnl-5657575 - Taguatinga, Brasília-DF</li>
          </ul>
        </div>
        <div class="footer-social">
          <h4>Siga-nos</h4>
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Discord"><i class="fab fa-discord"></i></a>
        </div>
      </div>
      <div class="footer-bottom">
        <p class="copyright">© 2025 ChampionsSports. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>
 

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const categoryCards = document.querySelectorAll('.category-card');
            const searchBar = document.querySelector('.search-bar');
            const favoritosGrid = document.querySelector('.favoritos-grid');

            // Pega os favoritos do usuário (injetados pelo PHP)
            const favorites = <?php echo json_encode($favoritos_usuario); ?>;

            function updateStars() {
                categoryCards.forEach(card => {
                    const category = card.dataset.category;
                    const star = card.querySelector('.favorite-star');
                    // A chave no objeto `favorites` é o nome da categoria em minúsculas
                    if (favorites[category.toLowerCase()] == 1) {
                        star.classList.add('favorited');
                    } else {
                        star.classList.remove('favorited');
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

                    // Atualiza o objeto de favoritos localmente
                    favorites[category.toLowerCase()] = isFavorited ? 1 : 0;

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

                    const result = await response.json();

                    if (response.ok && result.status === 'sucesso') {
                        // Ação de sucesso: não faz nada, apenas mantém o usuário na página.
                        // A estrela já foi atualizada visualmente.
                        console.log(result.mensagem); // Opcional: mostra a mensagem de sucesso no console.
                    } else {
                        // Se falhar, reverte a aparência da estrela e mostra um alerta
                        const card = document.querySelector(`.category-card[data-category="${category}"]`);
                        const star = card.querySelector('.favorite-star');
                        star.classList.toggle('favorited'); // Desfaz a mudança visual
                        alert(result.mensagem || 'Ocorreu um erro. Tente novamente.');
                    }
                } catch (error) {
                    console.error('Erro ao salvar favorito:', error);
                    alert('Não foi possível conectar ao servidor para salvar o favorito.');
                }
            }

            // Adiciona a funcionalidade de busca
            searchBar.addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase();

                categoryCards.forEach(card => {
                    const categoryName = card.dataset.category.toLowerCase();
                    if (categoryName.includes(searchTerm)) {
                        card.style.display = ''; // Mostra o card se corresponder
                    } else {
                        card.style.display = 'none'; // Esconde o card se não corresponder
                    }
                });
            });

            // Adiciona a funcionalidade de filtro pelos botões de favoritos
            if (favoritosGrid) {
                favoritosGrid.addEventListener('click', function(e) {
                    if (e.target.matches('.favorito-btn')) {
                        const filter = e.target.dataset.filter;
                        const isMostrarTodos = e.target.id === 'mostrar-todos-btn';

                        categoryCards.forEach(card => {
                            const categoryName = card.dataset.category;
                            // Mostra todos se o botão "Mostrar Todos" for clicado ou se não houver filtro
                            // Ou mostra apenas o card que corresponde ao filtro
                            if (isMostrarTodos || !filter || categoryName === filter) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    }
                });
            }

            // Marca as estrelas corretas quando a página carrega
            updateStars();
        });
    </script>
          <script src="Js/ajustesAcessibilidade.js"></script>
</body>

</body>

</html>