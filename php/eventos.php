<?php
session_start(); // Inicia a sessão

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { 
    header('Location: login.php');
    exit; 
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
 
<head>
    <?php require_once __DIR__ . '/tailwind.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Esportivos - ChampionsSports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        .logo p { margin: 0; }
        .menu-toggle, .menu-icon { display: none; }

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

        .eventos-section {
            width: min(1120px, calc(100% - 40px));
            min-height: calc(100vh - 250px);
            margin: 0 auto;
            padding: clamp(44px, 7vw, 76px) 0 80px;
        }

        .section-title { margin-bottom: 34px; text-align: center; }

        .section-title h2 {
            margin: 0;
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.15;
            letter-spacing: -.04em;
        }

        .container { width: min(100%, 1000px); margin: 0 auto; }

        .filters-container {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 18px;
            background: rgba(29, 29, 49, .9);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .18);
        }

        .filter-item { display: flex; min-width: 0; flex-direction: column; gap: 8px; }

        .filter-item label {
            color: #e7e7f0;
            font-size: .9rem;
            font-weight: 600;
        }

        .filter-item input, .filter-item select {
            width: 100%;
            min-height: 46px;
            padding: 0 13px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 10px;
            outline: none;
            background: #24243b;
            color: var(--text);
            font: inherit;
        }

        .filter-item input:focus, .filter-item select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(255, 0, 170, .16);
        }

        #clear-filters-container { justify-content: flex-end; }

        #clearFilters, .event-details-button {
            min-height: 46px;
            padding: 0 18px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(110deg, var(--primary), var(--secondary));
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: filter .2s ease, transform .2s ease;
        }

        #clearFilters:hover, .event-details-button:hover { filter: brightness(1.12); transform: translateY(-1px); }

        .results-header { margin: 28px 0 16px; color: var(--muted); }
        .results-header p { margin: 0; }
        #count { color: #ff70cf; font-weight: 700; }

        .events-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
        .loading-message { grid-column: 1 / -1; margin: 24px 0; color: var(--muted); text-align: center; }

        .event-card {
            display: flex;
            min-width: 0;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 18px;
            background: linear-gradient(145deg, rgba(38, 38, 61, .98), rgba(29, 29, 49, .98));
            box-shadow: 0 12px 30px rgba(0, 0, 0, .2);
            transition: transform .2s ease, border-color .2s ease;
        }

        .event-card:hover { transform: translateY(-4px); border-color: rgba(255, 0, 170, .45); }
        .event-content { padding: 22px; }

        .event-category-icon {
            display: block;
            width: 60px;
            height: 60px;
            margin-bottom: 16px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .event-title { margin: 0 0 16px; font-size: 1.2rem; line-height: 1.35; }
        .event-info { display: grid; gap: 11px; color: var(--muted); font-size: .92rem; }
        .event-info > div { display: flex; align-items: flex-start; gap: 10px; }
        .event-info i { width: 16px; margin-top: 3px; color: #ff70cf; }
        .event-actions { padding: 0 22px 22px; }
        .event-details-button { width: 100%; }

        #empty {
            margin-top: 20px;
            padding: 38px 20px;
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 16px;
            color: var(--muted);
            text-align: center;
        }

        #empty h3 { margin: 0 0 8px; color: var(--text); }
        #empty p { margin: 0; }

        .modal {
            position: fixed;
            z-index: 30;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, .76);
        }

        .modal.active { display: flex; }

        .modal-content {
            position: relative;
            width: min(100%, 680px);
            max-height: min(90vh, 800px);
            overflow: auto;
            padding: clamp(24px, 5vw, 38px);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 20px;
            background: #1d1d31;
            box-shadow: 0 24px 80px rgba(0, 0, 0, .5);
        }

        .close-button {
            position: absolute;
            top: 12px;
            right: 16px;
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            color: var(--text);
            font-size: 1.7rem;
            cursor: pointer;
        }

        #modal-title { margin: 0 44px 12px 0; font-size: clamp(1.5rem, 4vw, 2rem); }
        #modal-category { color: #ff70cf; font-weight: 700; }
        #modal-info, #modal-date-location { color: var(--muted); line-height: 1.65; white-space: pre-line; }
        #modal-date-location i { color: #ff70cf; }

        #modal-map-container { margin-top: 20px; }
        #modal-map-container iframe { display: block; width: 100%; height: 300px; border: 0; border-radius: 12px; }

        footer { border-top: 1px solid rgba(255, 255, 255, .08); background: #0c0c17; }
        .footer-content { width: min(1120px, calc(100% - 40px)); margin: 0 auto; }
        .footer-columns { display: grid; grid-template-columns: 1.4fr 1fr 1.4fr .8fr; gap: 32px; padding: 42px 0 30px; }
        .footer-columns h4 { margin: 0 0 14px; font-size: 1rem; }
        .footer-columns p, .footer-columns li { color: var(--muted); font-size: .9rem; line-height: 1.7; }
        .footer-columns p { margin: 0; }
        .footer-columns ul { margin: 0; padding: 0; list-style: none; }
        .footer-columns a { color: var(--muted); text-decoration: none; }
        .footer-columns a:hover, .footer-columns a.active { color: #ff70cf; }
        .footer-social a { margin-right: 12px; font-size: 1.25rem; }
        .footer-bottom { padding: 16px 0; border-top: 1px solid rgba(255, 255, 255, .08); text-align: center; }
        .copyright { margin: 0; color: var(--muted); font-size: .82rem; }

        @media (max-width: 850px) {
            .events-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .footer-columns { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px 20px; }
        }

        @media (max-width: 680px) {
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
            .eventos-section { width: min(100% - 32px, 600px); padding-top: 44px; }
            .filters-container { grid-template-columns: 1fr; padding: 18px; gap: 14px; }
            #clear-filters-container { justify-content: stretch; }
            .events-grid { gap: 14px; }
        }

        @media (max-width: 440px) {
            .events-grid, .footer-columns { grid-template-columns: 1fr; }
            .section-title { margin-bottom: 26px; }
            .event-content { padding: 18px; }
            .event-actions { padding: 0 18px 18px; }
            #modal-map-container iframe { height: 230px; }
        }
    </style>
</head>

<body>
    <!-- ======= CABEÇALHO ======= -->
    <header class="header">
        <input type="checkbox" id="menu-toggle" class="menu-toggle">

        <a href="../index.php" class="logo">
            <img src="../img/Logo.png" alt="Logo ChampionsSports">
            <p>ChampionsSports</p>
        </a>

        <label for="menu-toggle" class="menu-icon">
            <span class="hamburguer"></span>
        </label>

        <nav class="nav">
            <ul>
                <li><a href="../index.php">Início</a></li>
                <li><a href="./categorias.php">Categorias</a></li>
                <li><a href="./eventos.php" class="active">Eventos</a></li>
                <li><a href="./sobre.php">Sobre</a></li>
                <li><a href="./contato.php">Contato</a></li>
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
                    <p class="loading-message">Carregando eventos...</p>
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
                        <li><a href="../index.php">Início</a></li>
                        <li><a href="categorias.php">Categorias</a></li>
                        <li><a href="eventos.php" class="active">Eventos</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                        <li><a href="contato.php">Contato</a></li>
                        <li><a href="termos.php">Termos de Uso</a></li>
                        <li><a href="privacidade.php">Política de Privacidade</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h4>Contato</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i>
                            <a href="mailto:pichampionssport@gmail.com">pichampionssport@gmail.com</a>
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
                        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">© 2025 ChampionsSports. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="../Js/eventos.js"></script>
   
</body>

</html>