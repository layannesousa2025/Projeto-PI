<?php
session_start(); // Inicia a sessão

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { 
    header('Location: login.php');
    exit; 
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <?php require_once __DIR__ . '/tailwind.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contato - ChampionsSports</title>
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

    .contato-section {
      min-height: calc(100vh - 300px);
      padding: clamp(56px, 8vw, 88px) 0;
    }

    .container { width: min(1000px, calc(100% - 40px)); margin: 0 auto; }

    .page-title {
      margin: 0;
      text-align: center;
      font-size: clamp(2rem, 5vw, 3rem);
      line-height: 1.15;
      letter-spacing: -.04em;
    }

    .subtitle {
      max-width: 720px;
      margin: 16px auto 40px;
      color: var(--muted);
      text-align: center;
      font-size: 1rem;
      line-height: 1.7;
    }

    .contato-grid { display: grid; grid-template-columns: 1fr; max-width: 760px; margin: 0 auto; }

    .contato-card {
      padding: clamp(22px, 5vw, 38px);
      border: 1px solid rgba(255, 255, 255, .09);
      border-radius: 20px;
      background: linear-gradient(145deg, rgba(38, 38, 61, .98), rgba(29, 29, 49, .98));
      box-shadow: 0 18px 42px rgba(0, 0, 0, .24);
    }

    .contato-info-list { display: grid; gap: 18px; }

    .info-item {
      display: flex;
      min-width: 0;
      align-items: center;
      gap: 18px;
      padding: 18px;
      border: 1px solid rgba(255, 255, 255, .07);
      border-radius: 14px;
      background: rgba(255, 255, 255, .035);
    }

    .info-item > i {
      display: grid;
      width: 48px;
      height: 48px;
      flex: 0 0 48px;
      place-items: center;
      border-radius: 13px;
      background: linear-gradient(135deg, rgba(110, 0, 255, .35), rgba(255, 0, 170, .25));
      color: #ff8bd5;
      font-size: 1.25rem;
    }

    .info-item h3 { margin: 0 0 5px; font-size: 1rem; }
    .info-item a { overflow-wrap: anywhere; color: var(--muted); text-decoration: none; }
    .info-item a:hover { color: #ff8bd5; text-decoration: underline; }

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
      .footer-columns { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px 20px; }
    }

    @media (max-width: 440px) {
      .container, .footer-content { width: calc(100% - 32px); }
      .contato-section { padding: 52px 0; }
      .footer-columns { grid-template-columns: 1fr; }
      .info-item { align-items: flex-start; gap: 13px; padding: 15px; }
      .info-item > i { width: 42px; height: 42px; flex-basis: 42px; }
    }
  </style>
</head>

<body>
  <header class="header">
    <!-- Checkbox para controlar o menu mobile -->
    <input type="checkbox" id="menu-toggle" class="menu-toggle">
    
    <a href="../index.php" class="logo">
      <img src="../img/Logo.png" alt="Logo ChampionsSports" width="50" height="50">
      <p>ChampionsSports</p>
    </a>

    <label for="menu-toggle" class="menu-icon">
      <span class="hamburguer"></span>
    </label>
    
    <nav class="nav">
      <ul>
        <li><a href="../index.php">Início</a></li>
        <li><a href="./categorias.php">Categorias</a></li>
        <li><a href="./eventos.php">Eventos</a></li>
        <li><a href="./sobre.php">Sobre</a></li>
        <li><a href="./contato.php" class="active">Contato</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="contato-section">
      <div class="container">
        <h1 class="page-title">Fale com a equipe ChampionsSports</h1>
        <p class="subtitle">Quer divulgar um evento esportivo, tirar dúvidas ou enviar uma sugestão? Escolha o canal de sua preferência e fale com a gente.</p>

        <div class="contato-grid">
          <!-- Card de Contato -->
          <div class="contato-card">
            <div class="contato-info-list">
              <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div>
                  <h3>E-mail</h3>
                  <a href="mailto:pichampionssport@gmail.com">pichampionssport@gmail.com</a>
                </div>
              </div>

              <div class="info-item">
                <i class="fab fa-whatsapp"></i>
                <div>
                  <h3>WhatsApp</h3>
                  <a href="https://wa.me/5561999998888" target="_blank" rel="noopener noreferrer">
                    (61) 99999-8888
                  </a>
                </div>
              </div>

              <div class="info-item">
                <i class="fas fa-phone"></i>
                <div>
                  <h3>Telefone</h3>
                  <a href="tel:+556199999999">(61) 99999-9999</a>
                </div>
              </div>
            </div>
          </div>

          
        </div>
      </div>
    </section>

  </main>


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
                    <li><a href="./categorias.php">Categorias</a></li>
                    <li><a href="./eventos.php">Eventos</a></li>
                    <li><a href="./sobre.php">Sobre</a></li>
                    <li><a href="./contato.php">Contato</a></li>
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
            <p class="copyright">© <?= date('Y') ?> ChampionsSports. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

</body>

</html>
