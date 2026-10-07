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
    <title>Sobre - ChampionsSports</title>

    <!-- Ícones FontAwesome -->
    <link 
        rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer"
    />
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
                radial-gradient(ellipse at 80% 8%, rgba(110, 0, 255, .18), transparent 38rem),
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

        .container { width: min(1080px, calc(100% - 40px)); margin: 0 auto; }

        .hero {
            position: relative;
            display: grid;
            min-height: 340px;
            place-items: center;
            padding: 72px 20px;
            overflow: hidden;
            text-align: center;
            background:
                linear-gradient(120deg, rgba(17, 17, 31, .86), rgba(60, 0, 105, .78)),
                url("../img/Imagem-Champions02.png") center / cover no-repeat;
        }

        .hero::after {
            position: absolute;
            inset: auto 0 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--secondary), transparent);
            content: "";
        }

        .hero .container { position: relative; z-index: 1; }
        .hero-title { margin: 0 auto 16px; font-size: clamp(2.3rem, 6vw, 4.5rem); line-height: 1.08; letter-spacing: -.045em; }
        .hero-subtitle { max-width: 720px; margin: 0 auto; color: #e1ddec; font-size: clamp(1rem, 2vw, 1.2rem); line-height: 1.7; }

        .section { padding: clamp(56px, 8vw, 88px) 0; }
        .section-alt { background: linear-gradient(180deg, rgba(38, 38, 61, .45), rgba(29, 29, 49, .7)); }

        .section-title {
            margin: 0 0 36px;
            text-align: center;
            font-size: clamp(1.9rem, 4vw, 2.7rem);
            line-height: 1.2;
            letter-spacing: -.035em;
        }

        .about-content {
            max-width: 850px;
            margin: 0 auto;
        }

        .about-text { color: var(--muted); font-size: 1rem; line-height: 1.8; }
        .about-text p { margin: 0 0 16px; }
        .about-text strong { color: var(--text); }
        .about-subtitle { margin: 0 0 16px; color: #ff70cf; font-size: 1.25rem; }

        .section-intro { max-width: 760px; margin: -14px auto 36px; color: var(--muted); text-align: center; line-height: 1.7; }

        .features-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }

        .feature-card {
            padding: clamp(22px, 4vw, 30px);
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 18px;
            background: linear-gradient(145deg, rgba(38, 38, 61, .96), rgba(29, 29, 49, .96));
            box-shadow: 0 12px 30px rgba(0, 0, 0, .16);
            transition: transform .2s ease, border-color .2s ease;
        }

        .feature-card:hover { transform: translateY(-4px); border-color: rgba(255, 0, 170, .4); }
        .feature-icon { color: #ff70cf; font-size: 1.7rem; }
        .feature-title { margin: 16px 0 10px; font-size: 1.12rem; }
        .feature-card p { margin: 0; color: var(--muted); font-size: .95rem; line-height: 1.7; }

        footer { border-top: 1px solid rgba(255, 255, 255, .08); background: #0c0c17; }
        .footer-content { width: min(1120px, calc(100% - 40px)); margin: 0 auto; }
        .footer-columns { display: grid; grid-template-columns: 1.4fr 1fr 1.4fr .8fr; gap: 32px; padding: 42px 0 30px; }
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
            .hero { min-height: 300px; padding: 60px 0; }
            .features-grid { grid-template-columns: 1fr; }
            .footer-columns { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px 20px; }
        }

        @media (max-width: 440px) {
            .container, .footer-content { width: calc(100% - 32px); }
            .footer-columns { grid-template-columns: 1fr; }
            .section { padding: 52px 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>

<body>

    <!-- ====== HEADER ====== -->
    <header class="header">
        <input type="checkbox" id="menu-toggle" class="menu-toggle">

        <a href="../index.php" class="logo" aria-label="Voltar à página inicial">
            <img src="../img/Logo.png" alt="Logo ChampionsSports" width="50" height="50">
            <p>ChampionsSports</p>
        </a>

        <label for="menu-toggle" class="menu-icon" aria-label="Abrir ou fechar menu">
            <span class="hamburguer"></span>
        </label>

        <nav class="nav" aria-label="Menu principal">
            <ul>
                <li><a href="../index.php">Início</a></li>
                <li><a href="categorias.php">Categorias</a></li>
                <li><a href="eventos.php">Eventos</a></li>
                <li><a href="sobre.php" class="active">Sobre</a></li>
                <li><a href="contato.php">Contato</a></li>
            </ul>
        </nav>
    </header>

    <!-- ====== HERO ====== -->
    <section class="hero">
        <div class="container">
            <h1 class="hero-title">Conectamos você ao esporte</h1>
            <p class="hero-subtitle">Descubra eventos, encontre sua próxima atividade e viva de perto a paixão pelo esporte.</p>
        </div>
    </section>

    <!-- ====== NOSSA HISTÓRIA ====== -->
    <section id="sobre" class="section">
        <div class="container">
            <h2 class="section-title">Nossa História</h2>
            <div class="about-content">
                <div class="about-text">
                    <h3 class="about-subtitle">De onde viemos</h3>
                    <p>A ChampionsSports conecta você às melhores opções de esportes e eventos esportivos da sua região. Tudo começou como um pequeno projeto em grupo, movido pelo sonho de tornar o esporte acessível a todos.</p>
                    <p>Fundada em <strong>01 de agosto de 2025</strong>, a ChampionsSports nasceu com o propósito de aprimorar sua experiência na busca por conteúdos sobre esportes e o universo esportivo em geral.</p>
                    <p>Hoje, nossa paixão pelo esporte nos motiva a levar até você informações que inspiram, emocionam e revelam o poder da superação. Mais do que falar sobre esportes, queremos viver essa jornada junto com você.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== ACESSIBILIDADE ====== -->
    <section id="acessibilidade" class="section section-alt">
        <div class="container">
            <h2 class="section-title">Funcionalidade do conteúdo</h2>
            <p class="section-intro">Acreditamos que todos devem ter acesso igual à informação e aos nossos serviços. Por isso, desenvolvemos nosso site com recursos de acessibilidade.</p>

            <div class="features-grid">
                <div class="feature-card">
                    <i class="fa-solid fa-eye feature-icon"></i>
                    <h3 class="feature-title">Acessibilidade no Cadastro e Login</h3>
                    <p>Para facilitar o acesso, oferecemos a opção de cadastro e login acessíveis.</p>
                </div>

                <div class="feature-card">
                    <i class="fa-solid fa-list feature-icon"></i>
                    <h3 class="feature-title">Categorias</h3>
                    <p>As Categorias são onde podemos está escolhendo e clicando em cima da qual esporte você
                    desejar, automaticamente você e direcionado para tela de Eventos onde encontrara algum
                    evento do qual você escolheu esportes. Você também pode favorita nas estrelas que vocês
                    veem em cada categoria que no momento que um evento for adicionado você pode estar
                    recebendo uma notificação sobre o seu esporte favorito.</p>
                </div>

                <div class="feature-card">
                    <i class="fa-solid fa-calendar-days feature-icon"></i>
                    <h3 class="feature-title">Eventos</h3>
                    <p>Os Eventos são onde você encontra o local, hora, data e se gratuito ou não, você pode está
                    procurando uma categoria especifica ou não, podemos também pesquisar com datas,
                    exemplo: “Estou querendo eventos de 01/01/2025 ate 20/12/2025”, ai no campo de datas
                    você pode está colocando essas datas que ira aparecer todos os eventos nesses períodos.</p>
                </div>

                <div class="feature-card">
                    <i class="fa-solid fa-robot feature-icon"></i>
                    <h3 class="feature-title">Chatbot</h3>
                    <p>Ajuda você com dúvidas sobre esportes e categorias disponíveis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======= RODAPÉ PADRONIZADO ======= -->
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
                        <li><i class="fas fa-envelope" aria-hidden="true"></i> <a href="mailto:contato@ChampionsSports.com">contato@ChampionsSports.com</a></li>
                        <li><i class="fas fa-phone" aria-hidden="true"></i> <a href="tel:+5561999999999">(61) 99999-9999</a></li>
                        <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> QNL-5657575 - Taguatinga, Brasília-DF</li>
                    </ul>
                </div>

                <div class="footer-social">
                    <h4>Siga-nos</h4>
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">© <?= date('Y') ?> ChampionsSports. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="../Js/sobre.js"></script>
</body>
</html>