<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}

// Inclui o arquivo de conexão centralizado
require_once 'db_connect.php';

// Busca as categorias favoritas do usuário no banco
$favoritos_usuario = [];
try {
    // Busca o registro de categorias do usuário
    $sql = "SELECT ciclismo, futebol, voleibol, academia, caminhada, natacao, lazer, pcd FROM categoria WHERE id_cadastro_usuarios = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id_cadastro_usuario']);
    $stmt->execute();
    $categorias = $stmt->get_result()->fetch_assoc();

    if ($categorias) {
        // Filtra apenas as categorias que estão marcadas como 1 (favoritas)
        foreach ($categorias as $nome_categoria => $is_favorito) {
            if ($is_favorito == 1) {
                // Adiciona o nome da categoria (com a primeira letra maiúscula) ao array de favoritos
                $favoritos_usuario[] = ucfirst($nome_categoria);
            }
        }
    }
} catch (mysqli_sql_exception $e) {
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
    <link rel="stylesheet" href="Css/ajustesAcessibilidade.css">
    <!-- Adicionando Font Awesome para o ícone de estrela -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Adiciona estilo para centralizar o grid de categorias favoritas */
        .categories-grid {
            justify-content: center; /* Centraliza os cards horizontalmente */
        }
    </style>
</head>

<body>
    <h1>
        <div class="voltar">
            <a href="./usuario.php"><img src="./img/button (2).png" alt="Botão voltar"></a>
        </div>
        <div class="usuario" float: right;>
            <a href="usuario.php">
                <!-- Adiciona um parâmetro 'v' com o timestamp atual para evitar cache do navegador -->
                <img src="exibir_foto.php?v=<?php echo time(); ?>" alt="Foto do usuário" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
            </a>
        </div>
    </h1>

    <h2 style="text-align: center; color: white;">Suas Categorias Favoritas</h2>

    <div>
        <p style="text-align: center; color: white; margin-top: -10px; margin-bottom: 20px;">
            Clique na estrela para remover dos favoritos ou no card para ver os eventos.
        </p>
    </div>


    
    <div class="acebiblidade">
        <!-- Contêiner para alinhar os painéis lado a lado -->
        <div class="paineis-acessibilidade"  style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
            <div id="pertoTextAdMain" tabindex="-1" class="">
                <button aria-label="Ajustes de Texto Pressione Enter para entrar, o tab para continuar"
                    id="pertoTextAdTop" tabindex="0">
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
                                <input tabindex="-1" class="perto_text_slider" id="perto_font_slider_letter_size"
                                    type="range" min="0" max="100" value="0" step="10">
                                <output for="perto_font_slider_letter_size"
                                    id="perto_font_slider_label_letter_size">0%</output>
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
                                <input tabindex="-1" class="perto_text_slider" id="perto_font_slider_line_spacing"
                                    type="range" min="0" max="100" value="0" step="10">
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
                                <div class="pertoTooltip" style="position: absolute; display: none;">Altera o estilo das
                                    letras
                                    para fontes mais fáceis de ler.</div>
                                <span>Formatar Fontes</span>
                            </button>
                            <button class="pertoFunctionBtn" id="perto_func_underline_titles" tabindex="0">
                                <div class="pertoTooltip" style="position: absolute; display: none;">Adiciona sublinhado
                                    aos
                                    títulos da página para destacá-los visualmente.</div>
                                <span>Sublinhar Títulos</span>
                            </button>
                            <button class="pertoFunctionBtn" id="perto_func_uppercase_font" tabindex="0">
                                <div class="pertoTooltip" style="position: absolute; display: none;">Transforma todos
                                    textos
                                    para letras maiúsculas a fim de facilitar a leitura.</div>
                                <span>Fonte Maiúscula</span>
                            </button>
                            <button class="pertoFunctionBtn" id="perto_func_format_dyslexia" tabindex="0">
                                <div class="pertoTooltip" style="position: absolute; display: none;">Ativa uma fonte
                                    desenvolvida para facilitar a leitura de pessoas com dislexia.</div>
                                <span>Fonte para dislexia</span>
                            </button>
                            <button class="pertoFunctionBtn" id="perto_func_text_block_width" tabindex="0">
                                <div class="pertoTooltip" style="position: absolute; display: none;">Ajusta a largura
                                    dos blocos
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
                                <div class="pertoTooltip" style="position: absolute; display: none;">Aumenta o espaço
                                    entre
                                    parágrafos para melhorar a organização.</div>
                                <span>Espaçamento Entre Parágrafos</span>
                                <div class="alignment-indicators">
                                    <div class="alignment-dot"></div>
                                    <div class="alignment-dot"></div>
                                    <div class="alignment-dot"></div>
                                </div>
                            </button>
                            <button class="pertoFunctionBtn" id="perto_func_between_words" tabindex="0">
                                <div class="pertoTooltip" style="position: absolute; display: none;">Aumenta o espaço
                                    entre as
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
                <button id="pertoTextColorAdTop"
                    aria-label="Ajustes de Cores Pressione Enter para entrar, o tab para continuar" tabindex="0">
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
                            <button class="pertoResetColorButton" data-target="background"><span>Restaurar
                                    Cor</span></button>
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
    </div>
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
    <script src="Js/ajustesAcessibilidade.js"></script>

</body>

</html>