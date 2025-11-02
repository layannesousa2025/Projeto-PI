<?php
session_start(); // Inicia a sessão

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tela-Contato</title>
  <link rel="stylesheet" href="Css/contato.css">
  <!-- Adicionado Font Awesome para os ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
  <header class="header">
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
        <li><a href="categoria.php">Categorias</a></li>
        <li><a href="eventos.php">Eventos</a></li>
        <li><a href="sobre.php">Sobre</a></li>
        <li><a href="contato.php" class="active">Contato</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="contato-section">
      <div class="container">
        <h1 class="page-title">Entre em Contato</h1>
        <p class="subtitle">Quer adicionar seu evento em nosso site? Fale conosco!</p>

        <div class="contato-grid">
          <!-- Card de Contato -->
          <div class="contato-card">
            <div class="qr-code-container">
              <img src="img/Qr Code Jose.jpeg" alt="QR Code para contato via WhatsApp">
              <p class="qr-label">Escaneie para contato rápido</p>
            </div>

            <div class="contato-info-list">
              <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div>
                  <h3>Email</h3>
                  <a href="mailto:ChampionsSports@gmail.com">ChampionsSports@gmail.com</a>
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
              contato@ChampionsSports.com
            </li>
            <li><i class="fas fa-phone"></i>
              (61) 99999-9999
            </li>
            <li><i class="fas fa-map-marker-alt"></i> QNL-5657575 - Taguatinga, Brasília-DF</li>
          </ul>
        </div>

        <div class="footer-social">
          <h4>Siga-nos</h4>
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p class="copyright">© 2025 ChampionsSports. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>


</body>

</html>
