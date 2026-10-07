<?php
session_start(); // Inicia a sessão para verificar o login
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tela-Principal</title>

  <!-- TailwindCSS v3 via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary:   '#6e00ff',
            secondary: '#ff00aa',
            dark:      '#1a1a2e',
            darkCard:  '#2d2d46',
            darkAlt:   '#23233b',
            darkInput: '#3d3d5a',
          },
          fontFamily: {
            sans: ['Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif'],
          },
        }
      }
    }
  </script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="./Css/footer.css">

  <style>
    :root {
      --primary: #6e00ff;
      --secondary: #ff00aa;
      --dark: #1a1a2e;
      --light: #f8f9fa;
    }

    html, body {
      overflow-x: hidden;
      max-width: 100%;
    }

    body {
      min-width: 320px;
    }

    /* Hero: imagem de fundo + text-shadow */
    .hero {
      background-image:
        linear-gradient(135deg, rgba(110,0,255,0.2) 0%, rgba(255,0,170,0.2) 100%),
        url('./img/imagem-Champions02.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      color: white;
      text-shadow:
        -1px -1px 0 #f3f1f2ff,
         1px -1px 0 #ff00aa,
        -1px  1px 0 #ff00aa,
         1px  1px 0 #ff00aa,
         0 0 15px rgba(255,255,255,0.5);
    }

    /* Nav underline hover */
    .nav-menu a::after {
      content: '';
      position: absolute;
      width: 0; height: 2px;
      bottom: -5px; left: 0;
      background-color: var(--secondary);
      transition: width 0.3s;
    }
    .nav-menu a:hover::after { width: 100%; }

    /* Hero h1 gradient text */
    .hero-title {
      background: linear-gradient(to right, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      text-shadow: none;
    }

    .hero-description {
      color: white;
      font-weight: 700;
    }

    /* Search box */
    .search-box {
      display: flex;
      max-width: 800px;
      margin: 0 auto 3rem;
      border-radius: 50px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .search-box input {
      flex: 1; padding: 1rem 1.5rem;
      border: none; font-size: 1rem; outline: none;
    }
    .search-box button {
      padding: 0 2rem;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      color: white; border: none; cursor: pointer; font-weight: bold;
    }
    .search-box button:hover {
      background: linear-gradient(to right, #5a00d1, #e00096);
    }

    /* Event card hover image scale */
    .event-card:hover .event-image img { transform: scale(1.1); }

    /* Carousel slides flex + transition */
    .carousel-slides { display: flex; transition: transform 0.5s ease; }
    .carousel-slide  { min-width: 100%; position: relative; }
    .carousel-slide img {
      width: 100%;
      display: block;
      height: 320px;
      object-fit: cover;
    }
    .slide-content {
      padding: 2.5rem 1.25rem 4.25rem;
    }

    /* Carousel indicators absolute */
    .carousel-indicators {
      position: absolute;
      right: 1.5rem;
      bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.65rem;
      z-index: 10;
    }
    .indicator {
      width: 12px;
      height: 12px;
      background: rgba(255,255,255,0.6);
      border-radius: 50%;
      border: 1px solid rgba(0,0,0,0.25);
      cursor: pointer;
      padding: 0;
      transition: background 0.3s, transform 0.3s;
    }
    .indicator.active {
      background: #fff;
      transform: scale(1.2);
    }
    .indicator:focus-visible {
      outline: 2px solid #fff;
      outline-offset: 3px;
    }

    /* Chatbot input/button radius */
    .chatbot-input-field { border-radius: 50px 0 0 50px; }
    .chatbot-send-btn {
      border-radius: 0 50px 50px 0;
      background: linear-gradient(to right, var(--primary), var(--secondary));
    }

    /* Gradient button helper */
    .btn-gradient {
      background: linear-gradient(to right, var(--primary), var(--secondary));
    }

    /* Mantém o rodapé visualmente alinhado ao da página de contato */
    #contact {
      background: rgba(141, 152, 235, 0.15);
    }
    #contact .footer-columns {
      gap: 2rem;
      margin-bottom: 2rem;
    }
    #contact .footer-links a:hover,
    #contact .footer-contact a:hover {
      color: #fff;
      padding-left: 0;
      text-decoration: underline;
    }
    #contact .footer-social a:hover {
      color: #ff9800;
    }

    html, body {
      overflow-x: hidden;
      max-width: 100%;
    }

    body {
      min-width: 320px;
    }

    .mobile-user-icon {
      display: none;
      position: fixed;
      top: 1.05rem;
      right: 1rem;
      width: 2.8rem;
      height: 2.8rem;
      border-radius: 999px;
      border: 2px solid rgba(255,255,255,0.9);
      background: linear-gradient(135deg, rgba(110,0,255,0.9), rgba(255,0,170,0.9));
      color: #fff;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.25);
      z-index: 1002;
      text-decoration: none;
    }
    .mobile-user-icon i {
      font-size: 1.1rem;
    }

    /* Responsive: hide desktop nav on mobile */
    @media (max-width: 1024px) {
      .nav-menu { display: none !important; }
      header {
        padding-left: 1rem;
        padding-right: 1rem;
      }
      header > a { font-size: 1.2rem; }
      .hero {
        padding-left: 1rem;
        padding-right: 1rem;
      }
      .hero h1 {
        font-size: clamp(2.3rem, 7vw, 4rem);
      }
    }

    @media (max-width: 768px) {
      header {
        min-height: 72px;
        padding: 0.85rem 0.9rem;
      }
      header > a {
        margin-left: 0;
        margin-right: auto;
        gap: 0.5rem;
        font-size: 1.15rem;
        max-width: calc(100% - 4.5rem);
      }
      header > a p {
        font-size: 1.1rem;
      }
      header > a img {
        width: 38px;
        height: 38px;
      }
      .desktop-auth {
        display: none !important;
      }
      .mobile-user-icon {
        display: none !important;
      }
      .hero {
        min-height: auto;
        padding-top: 7rem;
      }
      .hero h1 {
        line-height: 1.08;
        margin-bottom: 1rem;
      }
      .hero p {
        font-size: 1rem;
        max-width: 100%;
      }
      .search-box {
        flex-direction: column;
        border-radius: 10px;
        width: 100%;
      }
      .search-box input {
        border-radius: 10px 10px 0 0;
        min-height: 48px;
      }
      .search-box button {
        padding: 1rem;
        border-radius: 0 0 10px 10px;
      }
      .carousel-slide img { height: 280px; }
      .carousel-indicators {
        right: 50%;
        bottom: 1rem;
        transform: translateX(50%);
      }
      .slide-content { padding-bottom: 4rem; }
    }

    @media (max-width: 480px) {
      body {
        font-size: 15px;
      }
      header {
        padding-top: 0.8rem;
        padding-bottom: 0.8rem;
      }
      header > a {
        margin-left: 0;
        gap: 0.35rem;
      }
      .hero {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }
      .hero h1 {
        font-size: clamp(2rem, 9vw, 2.8rem);
      }
      .hero p {
        font-size: 0.95rem;
      }
      .hero a {
        width: 100%;
        text-align: center;
      }
      .carousel-slide img { height: 240px; }
      .carousel-slide h3 { font-size: 1.4rem; }
      .carousel-slide p { max-width: 100%; font-size: 0.9rem; }
      .carousel-arrow { padding: 0.5rem 0.75rem; margin: 0 0.5rem; }
      .carousel-indicators { gap: 0.45rem; }
      .indicator { width: 10px; height: 10px; }
      #events-grid {
        grid-template-columns: 1fr !important;
      }
    }
  </style>
</head>

<body class="bg-[#1a1a2e] text-[#f8f9fa] m-0 p-0 leading-relaxed" style="font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif">

  <!-- ===== HEADER ===== -->
  <header class="fixed w-full top-0 z-[1000] flex justify-between items-center px-8 py-4 backdrop-blur-[10px] border-b border-white/10"
          style="background:rgba(26,26,46,0.9)">

    <!-- Logo -->
    <a href="#" class="flex items-center text-white no-underline font-bold text-2xl gap-2">
      <img src="./img/Logo.png" width="50" height="50" alt="Logo ChampionsSports">
      <p>ChampionsSports</p>
    </a>

    <!-- Menu de Navegação desktop -->
    <nav>
      <ul class="nav-menu flex list-none ml-[250px] gap-8">
        <li><a href="#home" class="relative text-[#f8f9fa] no-underline font-medium transition-colors duration-300 hover:text-[#ff00aa]">Início</a></li>
        <li><a href="./php/categorias.php" class="relative text-[#f8f9fa] no-underline font-medium transition-colors duration-300 hover:text-[#ff00aa]">Categorias</a></li>
        <li><a href="./php/eventos.php" class="relative text-[#f8f9fa] no-underline font-medium transition-colors duration-300 hover:text-[#ff00aa]">Eventos</a></li>
        <li><a href="./php/sobre.php" class="relative text-[#f8f9fa] no-underline font-medium transition-colors duration-300 hover:text-[#ff00aa]">Sobre</a></li>
        <li><a href="#chatbot" class="relative text-[#f8f9fa] no-underline font-medium transition-colors duration-300 hover:text-[#ff00aa]">Chatbot</a></li>
        <li><a href="./php/contato.php" class="relative text-[#f8f9fa] no-underline font-medium transition-colors duration-300 hover:text-[#ff00aa]">Contato</a></li>
      </ul>
    </nav>

    <!-- Botões login/logout -->
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
      <div class="desktop-auth flex gap-4 ml-auto z-[1002]">
        <a href="./php/usuario.php" class="no-underline px-5 py-2 rounded-[25px] font-semibold text-base transition-all duration-300 text-white hover:scale-105"
           style="background:linear-gradient(135deg,#6e00ff,#ff00aa);box-shadow:0 4px 10px rgba(0,0,0,0.3)"><?php echo htmlspecialchars($_SESSION['nome']); ?></a>
        <a href="./php/logout.php" class="no-underline px-5 py-2 rounded-[25px] font-semibold text-base transition-all duration-300 bg-transparent border-2 border-[#f8f9fa] text-white hover:bg-[#f8f9fa] hover:text-[#1a1a2e]">Sair</a>
      </div>
      <a href="./php/usuario.php" class="mobile-user-icon" aria-label="Ir para o perfil">
        <i class="fa-solid fa-user"></i>
      </a>
    <?php else: ?>
      <div class="desktop-auth flex gap-4 ml-auto z-[1002]">
        <a href="./html/login.html" class="no-underline inline-block px-5 py-2 rounded-[25px] font-semibold text-base transition-all duration-300 bg-transparent border-2 border-[#f8f9fa] text-white hover:bg-[#f8f9fa] hover:text-[#1a1a2e]">Acessar</a>
        <a href="./html/cadastro.html" class="no-underline inline-block px-5 py-2 rounded-[25px] font-semibold text-base transition-all duration-300 text-white hover:scale-105"
           style="background:linear-gradient(135deg,#6e00ff,#ff00aa);box-shadow:0 4px 10px rgba(0,0,0,0.3)">Cadastrar</a>
      </div>
      <a href="./html/login.html" class="mobile-user-icon" aria-label="Fazer login">
        <i class="fa-solid fa-user"></i>
      </a>
    <?php endif; ?>
  </header>

  <!-- ===== HERO ===== -->
  <section class="hero min-h-screen flex items-center px-8 mt-20 relative overflow-hidden" id="home">
    <div class="max-w-[1200px] mx-auto w-full">
      <h1 class="text-[3.5rem] font-bold mb-4 leading-tight">
        Encontre eventos esportivos na sua região
      </h1>
      <p class="hero-description text-[1.2rem] mb-8 max-w-[600px]">Descubra torneios, campeonatos e entre outros esports mais perto de você. Nunca mais perca um evento do seu jogo favorito!</p>
      <a href="#search" class="inline-block px-8 py-3 text-white no-underline font-bold rounded-[50px] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_10px_20px_rgba(0,0,0,0.2)]"
         style="background:linear-gradient(to right,#6e00ff,#ff00aa)">Buscar Eventos</a>
    </div>
  </section>

  <!-- ===== BUSCA ===== -->
  <section class="py-16 px-8 bg-[#1a1a2e]" id="search">
    <div class="max-w-[1200px] mx-auto text-center">
      <h2 class="text-[2.5rem] font-bold mb-8 text-white">Encontre eventos próximos</h2>
      <form action="./php/eventos.php" method="GET" class="search-box">
        <input type="text" placeholder="Pesquise..." id="location-input" name="q">
        <button type="submit" id="search-btn">Buscar</button>
      </form>
      <p class="text-[#f8f9fa]">ou use o chat abaixo para ajudar na sua busca</p>
    </div>
  </section>

  <!-- ===== EVENTOS ===== -->
  <section class="py-16 px-8 bg-[#23233b]" id="events">
    <div class="max-w-[1200px] mx-auto">
      <h2 class="text-center text-[2.5rem] font-bold mb-12 text-white">O Sucesso não acontece por acaso, Pratique Esportes</h2>

      <!-- CARROSSEL -->
      <div class="carousel-container relative max-w-[900px] mx-auto my-8 overflow-hidden rounded-[15px] text-white"
           style="box-shadow:0 10px 25px rgba(0,0,0,0.2)">
        <div class="carousel-slides">
          <div class="carousel-slide active">
            <img src="./img/futebol.jpg" alt="imagem-futebol">
            <div class="slide-content absolute bottom-0 left-0 right-0 text-white px-5 pb-5 pt-10"
                 style="background:linear-gradient(to top,rgba(0,0,0,0.8),transparent)">
              <h3 class="text-[1.8rem] font-bold mb-2">Futebol</h3>
              <p class="text-base m-0">Cada treino te leva um passo mais perto da vitória.</p>
            </div>
          </div>
          <div class="carousel-slide">
            <img src="./img/capoeira.jpg" alt="imagem-capoeira">
            <div class="slide-content absolute bottom-0 left-0 right-0 text-white px-5 pb-5 pt-10"
                 style="background:linear-gradient(to top,rgba(0,0,0,0.8),transparent)">
              <h3 class="text-[1.8rem] font-bold mb-2">Capoeira</h3>
              <p class="text-base m-0">Os sonhos são construídos com dedicação.</p>
            </div>
          </div>
          <div class="carousel-slide">
            <img src="./img/jiujitsu.jpg" alt="imagem-jiujitsu">
            <div class="slide-content absolute bottom-0 left-0 right-0 text-white px-5 pb-5 pt-10"
                 style="background:linear-gradient(to top,rgba(0,0,0,0.8),transparent)">
              <h3 class="text-[1.8rem] font-bold mb-2">Jiu-jitsu</h3>
              <p class="text-base m-0">Agilidade e paciência.</p>
            </div>
          </div>
          <div class="carousel-slide">
            <img src="./img/remo.jpg" alt="imagem-remo">
            <div class="slide-content absolute bottom-0 left-0 right-0 text-white px-5 pb-5 pt-10"
                 style="background:linear-gradient(to top,rgba(0,0,0,0.8),transparent)">
              <h3 class="text-[1.8rem] font-bold mb-2">Remo</h3>
              <p class="text-base m-0">Quanto mais Treinamos, mais forte ficamos.</p>
            </div>
          </div>
          <div class="carousel-slide">
            <img src="./img/tenis.jpg" alt="imagem-tenis">
            <div class="slide-content absolute bottom-0 left-0 right-0 text-white px-5 pb-5 pt-10"
                 style="background:linear-gradient(to top,rgba(0,0,0,0.8),transparent)">
              <h3 class="text-[1.8rem] font-bold mb-2">Tênis</h3>
              <p class="text-base m-0">Agilidade.</p>
            </div>
          </div>
          <div class="carousel-slide">
            <img src="./img/volei-praia.jpg" alt="imagem-volei-praia">
            <div class="slide-content absolute bottom-0 left-0 right-0 text-white px-5 pb-5 pt-10"
                 style="background:linear-gradient(to top,rgba(0,0,0,0.8),transparent)">
              <h3 class="text-[1.8rem] font-bold mb-2">Vôlei de Praia</h3>
              <p class="text-base m-0">Cooperação e saltos impressionantes.</p>
            </div>
          </div>
          <div class="carousel-slide">
            <img src="./img/natacao.jpg" alt="imagem-natacao">
            <div class="slide-content absolute bottom-0 left-0 right-0 text-white px-5 pb-5 pt-10"
                 style="background:linear-gradient(to top,rgba(0,0,0,0.8),transparent)">
              <h3 class="text-[1.8rem] font-bold mb-2">Natação</h3>
              <p class="text-base m-0">Determinação.</p>
            </div>
          </div>
          <div class="carousel-slide">
            <img src="./img/musculacao.jpg" alt="imagem-musculacao">
            <div class="slide-content absolute bottom-0 left-0 right-0 text-white px-5 pb-5 pt-10"
                 style="background:linear-gradient(to top,rgba(0,0,0,0.8),transparent)">
              <h3 class="text-[1.8rem] font-bold mb-2">Musculação</h3>
              <p class="text-base m-0">Força e resistência.</p>
            </div>
          </div>
        </div>

        <!-- Controles -->
        <div class="carousel-controls absolute top-1/2 w-full flex justify-between -translate-y-1/2 pointer-events-none">
          <button class="carousel-arrow prev pointer-events-auto bg-black/50 text-white border-none px-4 py-2 rounded-full cursor-pointer text-2xl mx-4 transition-colors duration-300 hover:bg-black/80" onclick="prevSlide()">&#10094;</button>
          <button class="carousel-arrow next pointer-events-auto bg-black/50 text-white border-none px-4 py-2 rounded-full cursor-pointer text-2xl mx-4 transition-colors duration-300 hover:bg-black/80" onclick="nextSlide()">&#10095;</button>
        </div>

        <!-- Indicadores (gerados pelo JS) -->
        <div class="carousel-indicators"></div>
      </div>

      <!-- GRID DE EVENTOS -->
      <div class="grid gap-8 mt-4" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr))" id="events-grid">

        <div class="event-card bg-[#2d2d46] rounded-[10px] overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(0,0,0,0.3)]">
          <div class="event-image h-[200px] overflow-hidden">
            <img src="img/imgJudo.png" alt="Arena de campeonato de Judô com tatame e atletas em competição" class="w-full h-full object-cover transition-transform duration-500">
          </div>
          <div class="p-6">
            <h3 class="mb-2 text-white font-bold">🔥 "Suba no tatame, desafie seus limites e conquiste a vitória no Campeonato Regional de Judô"</h3>
            <p class="text-[#aaa] mb-4 text-sm">Participe do maior torneio com prêmios de até R$ 10.000,00!</p>
            <div class="flex justify-between items-center mt-4">
              <span class="bg-[#6e00ff] text-white px-3 py-1 rounded-[50px] text-xs font-bold">15/06/2023</span>
              <span class="text-[#ff00aa] font-bold">São Paulo, SP</span>
            </div>
          </div>
        </div>

        <div class="event-card bg-[#2d2d46] rounded-[10px] overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(0,0,0,0.3)]">
          <div class="event-image h-[200px] overflow-hidden">
            <img src="img/imgBoxing (1).png" alt="Campeonato de Boxe com lutadores no ringue" class="w-full h-full object-cover transition-transform duration-500">
          </div>
          <div class="p-6">
            <h3 class="mb-2 text-white font-bold">🥊 "Suba no ringue, mostre sua força e conquiste a glória no Campeonato Regional"</h3>
            <p class="text-[#aaa] mb-4 text-sm">Com prêmios de até R$ 5.000,00!</p>
            <div class="flex justify-between items-center mt-4">
              <span class="bg-[#6e00ff] text-white px-3 py-1 rounded-[50px] text-xs font-bold">22/07/2023</span>
              <span class="text-[#ff00aa] font-bold">Goiânia, GO</span>
            </div>
          </div>
        </div>

        <div class="event-card bg-[#2d2d46] rounded-[10px] overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(0,0,0,0.3)]">
          <div class="event-image h-[200px] overflow-hidden">
            <img src="img/imgCademia1.png" alt="Competição de musculação em academia" class="w-full h-full object-cover transition-transform duration-500">
          </div>
          <div class="p-6">
            <h3 class="mb-2 text-white font-bold">💪 "Mostre sua força na Academia"</h3>
            <p class="text-[#aaa] mb-4 text-sm">Supere seus limites e conquiste prêmios de até R$ 3.500,00 no Campeonato Regional!</p>
            <div class="flex justify-between items-center mt-4">
              <span class="bg-[#6e00ff] text-white px-3 py-1 rounded-[50px] text-xs font-bold">05/08/2023</span>
              <span class="text-[#ff00aa] font-bold">Brasília, DF</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ===== CHATBOT ===== -->
  <section class="py-16 px-8 bg-[#1a1a2e]" id="chatbot">
    <div class="max-w-[1200px] mx-auto text-center">
      <h2 class="text-[2.5rem] font-bold mb-8 text-white">Assistente Virtual</h2>
      <div class="bg-[#2d2d46] rounded-[10px] max-w-[600px] mx-auto mt-8 overflow-hidden"
           style="box-shadow:0 10px 30px rgba(0,0,0,0.2)">
        <div class="px-4 py-4 text-white font-bold"
             style="background:linear-gradient(to right,#6e00ff,#ff00aa)">
          ChampionsSports
        </div>
        <div class="h-[300px] overflow-y-auto p-4 text-left" id="chatbot-messages">
          <div class="message bot-message mb-4 max-w-[80%] bg-[#3d3d5a] text-white p-3 rounded-[10px_10px_10px_0]">Fale com Assistente Virtual...</div>
        </div>
        <div class="flex p-4 border-t border-[#3d3d5a]">
          <input type="text" placeholder="Digite sua mensagem..." id="user-input"
                 class="chatbot-input-field flex-1 p-3 border-none outline-none bg-[#3d3d5a] text-white">
          <button id="send-btn"
                  class="chatbot-send-btn px-6 text-white border-none cursor-pointer font-bold">Enviar</button>
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
            <li><a href="./index.php">Início</a></li>
            <li><a href="./php/categorias.php">Categorias</a></li>
            <li><a href="./php/eventos.php">Eventos</a></li>
            <li><a href="./php/sobre.php">Sobre</a></li>
            <li><a href="./php/contato.php">Contato</a></li>
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

  <script src="./Js/assistenteV.js"></script>
  <script src="./Js/carrosel.js"></script>
  <script src="teste.js"></script>
</body>

</html>