<?php
session_start(); // Inicia a sessão para verificar o login
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tela-Principal</title>
  <link rel="stylesheet" href="Css/telaPrincipal.css">
  <link rel="stylesheet" href="Css/carrosel.css">
  <!-- Adicione o link para o Font Awesome para exibir os ícones sociais -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="teste.js"></script>
  <style>
    .hero {
      background-image: url('./img/Imagem-Champions02.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      color: white;
      /* Efeito de contorno rosa com brilho branco para melhor legibilidade e estilo */
      text-shadow:
        -1px -1px 0 #f3f1f2ff,
        1px -1px 0 #ff00aa,
        -1px 1px 0 #ff00aa,
        1px 1px 0 #ff00aa,
        0 0 15px rgba(255, 255, 255, 0.5);
    }
  </style>
</head>

<body>
  <header>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="menu-btn">
      <span></span>
      <span></span>
      <span></span>
    </label>

    <div class="slide-menu">
      <div class="logo-menu">
        <img src="./img/Logo (1).png" alt="Logo ChampionsSports">
        <p>ChampionsSports</p>
      </div>

      <ul>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
          <a href="./php/usuario.php">
            <li><?php echo htmlspecialchars($_SESSION['nome']); ?></li>
          </a>
        <?php endif; ?>

        <a href="./index.php">
          <li>Início</li>
        </a>
        <a href="./php/categorias.php">
          <li>Categorias</li>
        </a>
        <a href="./php/eventos.php">
          <li>Eventos</li>
        </a>
        <a href="./php/sobre.php">
          <li>Sobre</li>
        </a>
        <a href="#chatbot">
          <li>Chatbot</li>
        </a>
        <a href="./php/contato.php">
          <li>Contato</li>
        </a>
      </ul>
    </div>

    <!-- Logo -->
    <a href="#" class="logo">
      <img src="./img/Logo.png" width="50" height="50" alt="Logo ChampionsSports">
      <p>ChampionsSports</p>
    </a>

    <!-- Menu de Navegação -->
    <nav>
      <ul class="nav-menu">
        <li><a href="#home">Início</a></li>
        <li><a href="./php/categorias.php">Categorias</a></li>
        <li><a href="./php/eventos.php">Eventos</a></li>
        <li><a href="./php/sobre.php">Sobre</a></li>
        <li><a href="#chatbot">Chatbot</a></li>
        <li><a href="./php/contato.php">Contato</a></li>
      </ul>
    </nav>

    <div class="overlay"></div>

    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
      <!-- Mostra isso se o usuário estiver logado -->
      <div class="login-button">
        <a href="./php/usuario.php" class="btn-primary" style="text-decoration: none; padding: 10px 20px;"><?php echo htmlspecialchars($_SESSION['nome']); ?></a>
        <a href="./php/logout.php" class="btn-outline" style="text-decoration: none; padding: 10px 20px; color: white;">Sair</a>
      </div>
    <?php else: ?>
      <!-- Mostra isso se o usuário NÃO estiver logado -->
      <div class="login-button">
        <button class="btn-outline"><a href="./html/login.html">Acessar</a></button>
        <button class="btn-primary"><a href="./html/cadastro.html">Cadastrar</a></button>
      </div>
    <?php endif; ?>
  </header>

  <section class="hero" id="home">
    <div class="hero-content">
      <h1>Encontre eventos esportivos na sua região</h1>
      <p>Descubra torneios, campeonatos e entre outros esports mais perto de você. Nunca mais perca um
        evento do seu jogo favorito!</p>
      <a href="#search" class="btn">Buscar Eventos</a>
    </div>
  </section>

  <section class="search-section" id="search">
    <div class="search-container">
      <h2>Encontre eventos próximos</h2>
      <form action="./php/eventos.php" method="GET" class="search-box">
        <input type="text" placeholder="Pesquise..." id="location-input" name="q">
        <button type="submit" id="search-btn">Buscar</button>
      </form>
      <p>ou use o chat abaixo para ajudar na sua busca</p>
    </div>
  </section>

  <section class="events-section" id="events">
    <div class="events-container">
      <h2 class="section-title">O Sucesso não acontece por acaso, Pratique Esportes</h2>

      <!-- ===== CARROSSEL ===== -->
      <div class="carousel-container">
        <div class="carousel-slides">
          <div class="carousel-slide active">
            <img src="./img/futebol.jpg" alt="imagem-futebol">
            <div class="slide-content">
              <h3>Futebol</h3>
              <p>Cada treino te leva um passo mais perto da vitória.</p>
            </div>
          </div>

          <div class="carousel-slide">
            <img src="./img/capoeira.jpg" alt="imagem-capoeira">
            <div class="slide-content">
              <h3>Capoeira</h3>
              <p>Os sonhos são construídos com dedicação.</p>
            </div>
          </div>

          <div class="carousel-slide">
            <img src="./img/jiujitsu.jpg" alt="imagem-jiujitsu">
            <div class="slide-content">
              <h3>Jiu-jitsu</h3>
              <p>Agilidade e paciência.</p>
            </div>
          </div>

          <div class="carousel-slide">
            <img src="./img/remo.jpg" alt="imagem-remo">
            <div class="slide-content">
              <h3>Remo</h3>
              <p>Quanto mais Treinamos, mais forte ficamos.</p>
            </div>
          </div>

          <div class="carousel-slide">
            <img src="./img/tenis.jpg" alt="imagem-tenis">
            <div class="slide-content">
              <h3>Tênis</h3>
              <p>Agilidade.</p>
            </div>
          </div>

          <div class="carousel-slide">
            <img src="./img/volei-praia.jpg" alt="imagem-volei-praia">
            <div class="slide-content">
              <h3>Vôlei de Praia</h3>
              <p>Cooperação e saltos impressionantes.</p>
            </div>
          </div>

          <div class="carousel-slide">
            <img src="./img/natacao.jpg" alt="imagem-natacao">
            <div class="slide-content">
              <h3>Natação</h3>
              <p>Determinação.</p>
            </div>
          </div>

          <div class="carousel-slide">
            <img src="./img/musculacao.jpg" alt="imagem-musculacao">
            <div class="slide-content">
              <h3>Musculação</h3>
              <p>Força e resistência.</p>
            </div>
          </div>
        </div>

        <!-- Controles -->
        <div class="carousel-controls">
          <button class="carousel-arrow prev" onclick="prevSlide()">&#10094;</button>
          <button class="carousel-arrow next" onclick="nextSlide()">&#10095;</button>
        </div>

        <!-- Indicadores -->
        <div class="carousel-indicators">
          <!-- Indicadores serão gerados dinamicamente pelo JavaScript -->
        </div>
      </div>

      <!-- ===== GRID DE EVENTOS ===== -->
      <div class="events-grid" id="events-grid">
        <div class="event-card">
          <div class="event-image">
            <img src="img/imgJudo.png" alt="Arena de campeonato de Judô com tatame e atletas em competição">
          </div>
          <div class="event-info">
            <h3>🔥 "Suba no tatame, desafie seus limites e conquiste a vitória no Campeonato Regional de Judô"</h3>
            <p>Participe do maior torneio com prêmios de até R$ 10.000,00!</p>
            <div class="event-meta">
              <span class="event-date">15/06/2023</span>
              <span class="event-location">São Paulo, SP</span>
            </div>
          </div>
        </div>

        <div class="event-card">
          <div class="event-image">
            <img src="img/imgBoxing (1).png" alt="Campeonato de Boxe com lutadores no ringue">
          </div>
          <div class="event-info">
            <h3>🥊 "Suba no ringue, mostre sua força e conquiste a glória no Campeonato Regional"</h3>
            <p>Com prêmios de até R$ 5.000,00!</p>
            <div class="event-meta">
              <span class="event-date">22/07/2023</span>
              <span class="event-location">Goiânia, GO</span>
            </div>
          </div>
        </div>

        <div class="event-card">
          <div class="event-image">
            <img src="img/imgCademia1.png" alt="Competição de musculação em academia">
          </div>
          <div class="event-info">
            <h3>💪 "Mostre sua força na Academia"</h3>
            <p>Supere seus limites e conquiste prêmios de até R$ 3.500,00 no Campeonato Regional!</p>
            <div class="event-meta">
              <span class="event-date">05/08/2023</span>
              <span class="event-location">Brasília, DF</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== ChatBot- manutenção ===== -->
  <section class="chatbot-section" id="chatbot">
    <div class="chatbot-container">
      <h2 class="section-title">Assistente Virtual</h2>
      <div class="chatbot">
        <div class="chatbot-header">
          ChampionsSports
        </div>
        <div class="chatbot-messages" id="chatbot-messages">
          <div class="message bot-message">Fale com Assistente Virtual...</div>
        </div>
        <div class="chatbot-input">
          <input type="text" placeholder="Digite sua mensagem..." id="user-input">
          <button id="send-btn">Enviar</button>
        </div>
      </div>
    </div>
  </section>

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
            <li><a href="./php/eventos.php">Eventos</a></li>
            <li><a href="./php/sobre.php">Sobre</a></li>
            <li><a href="#">Termos de Uso</a></li>
            <li><a href="#">Política de Privacidade</a></li>
          </ul>
        </div>

        <div class="footer-contact">
          <h4>Contato</h4>
          <ul>
            <li><i class="fas fa-envelope"></i>
              pichampionssport@gmail.com
            </li>
            <li><i class="fas fa-phone"></i>
              (61) 99999-9999
            </li>
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

  <script src="./Js/menu.js"></script>
  <script src="./Js/assistenteV.js"></script>
  <script src="./Js/carrosel.js"></script>
</body>

</html>