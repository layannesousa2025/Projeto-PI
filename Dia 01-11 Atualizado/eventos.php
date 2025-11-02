<?php
session_start(); // Inicia a sessão

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Esportivos - ChampionsSports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="Css/eventos.css">
</head>

<body>
    <!-- ======= CABEÇALHO ======= -->
    <header class="header">
        <input type="checkbox" id="menu-toggle" class="menu-toggle">

        <a href="telaPrincipal.php" class="logo">
            <img src="./img/Logo.png" alt="Logo ChampionsSports">
            <p>ChampionsSports</p>
        </a>

        <label for="menu-toggle" class="menu-icon">
            <span class="hamburguer"></span>
        </label>

        <nav class="nav">
            <ul>
                <li><a href="telaPrincipal.php">Início</a></li>
                <li><a href="categoria.php">Categorias</a></li>
                <li><a href="eventos.php" class="active">Eventos</a></li>
                <li><a href="sobre.php">Sobre</a></li>
                <li><a href="contato.php">Contato</a></li>
            </ul>
        </nav>
    </header>

    <!-- ======= SEÇÃO DE EVENTOS ======= -->
    <main>
        <section class="eventos-section">
            <div class="section-title">
                <h2>Eventos Disponíveis</h2>
            </div>

            <div class="container">
                <!-- Filtros -->
                <div class="filters-container">
                    <div class="filter-item">
                        <label for="q">Buscar por nome ou local</label>
                        <input type="text" id="q" placeholder="Ex: Futebol em Brasília">
                    </div>

                    <div class="filter-item">
                        <label for="game">Categoria</label>
                        <select id="game">
                            <option value="">Todas</option>
                            <option value="Futebol">Futebol</option>
                            <option value="Voleibol">Voleibol</option>
                            <option value="Academia">Academia</option>
                            <option value="Caminhada">Caminhada</option>
                            <option value="Natação">Natação</option>
                            <option value="Ciclismo">Ciclismo</option>
                            <option value="Lazer">Lazer</option>
                            <option value="PCD">PCD</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label for="from">Data Início</label>
                        <input type="date" id="from">
                    </div>

                    <div class="filter-item">
                        <label for="to">Data Fim</label>
                        <input type="date" id="to">
                    </div>

                    <div class="filter-item" id="clear-filters-container">
                        <button id="clearFilters">Limpar Filtros</button>
                    </div>
                </div>

                <!-- Resultados -->
                <div class="results-header">
                    <p>Encontrados <span id="count">0</span> eventos.</p>
                </div>

                <div id="results-grid" class="events-grid">
                    <!-- Exemplo de card de evento (será preenchido dinamicamente) -->
                    <div class="event-card">
                        <img src="./img/jogador.png" alt="Evento" class="event-image">
                        <div class="event-content">
                            <h3 class="event-title">Campeonato de Futebol</h3>
                            <div class="event-info">
                                <div><i class="fas fa-calendar"></i>15 de Dezembro, 2025</div>
                                <div><i class="fas fa-map-marker-alt"></i>Estádio Nacional, Brasília</div>
                                <div><i class="fas fa-clock"></i>14:00 - 18:00</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensagem quando não houver resultados -->
                <div id="empty" style="display: none;">
                    <h3>Nenhum evento encontrado</h3>
                    <p>Tente ajustar seus filtros de busca.</p>
                </div>
            </div>

            <!-- Modal de Detalhes -->
            <div id="event-modal" class="modal">
                <div class="modal-content">
                    <button class="close-button" aria-label="Fechar Modal">&times;</button>
                    <div id="modal-body">
                        <h2 id="modal-title">Título do Evento</h2>
                        <p id="modal-category" class="event-category">Categoria</p>
                        <p id="modal-info">Informações detalhadas do evento...</p>
                        <p id="modal-date-location"></p>
                        <div id="modal-map-container"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ======= RODAPÉ ======= -->
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
                        <li><a href="telaPrincipal.php">Início</a></li>
                        <li><a href="eventos.php">Eventos</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                        <li><a href="#">Termos de Uso</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h4>Contato</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i>
                            <a href="mailto:contato@ChampionsSports.com">contato@ChampionsSports.com</a>
                        </li>
                        <li><i class="fas fa-phone"></i>
                            <a href="tel:+5561999999999">(61) 99999-9999</a>
                        </li>
                        <li><i class="fas fa-map-marker-alt"></i> QNL-5657575 - Taguatinga, Brasília-DF</li>
                    </ul>
                </div>

                <div class="footer-social">
                    <h4>Siga-nos</h4>
                    <div>
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">© 2025 ChampionsSports. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="Js/eventos.js"></script>
</body>

</html>
