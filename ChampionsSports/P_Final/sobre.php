<?php
session_start(); // Inicia a sessão

// ====== VERIFICAÇÃO DE LOGIN ======
if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre - ChampionsSports</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="Css/sobre.css">

    <!-- Ícones FontAwesome -->
    <link 
        rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer"
    />
</head>

<body>

    <!-- ====== HEADER ====== -->
    <header class="header">
        <a href="telaPrincipal.php" class="logo" aria-label="Voltar à página inicial">
            <img src="img/Logo.png" alt="Logo ChampionsSports" width="50" height="50">
            <p>ChampionsSports</p>
        </a>

        <nav class="nav" aria-label="Menu principal">
            <ul>
                <li><a href="telaPrincipal.php">Início</a></li>
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
            <h1 class="hero-title">ChampionsSports</h1>
            <p class="hero-subtitle">Sua paixão por esportes começa aqui</p>
            <a href="#contato" class="btn-primary">Entre em Contato</a>
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
                <div class="about-image">
                    <img src="img/Logo.png" alt="Imagem representando a ChampionsSports">
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
                    <p>Para facilitar o acesso, oferecemos a opção de cadastro e login utilizando apenas o número de telefone, sem a necessidade de e-mail.</p>
                </div>

                <div class="feature-card">
                    <i class="fa-solid fa-list feature-icon"></i>
                    <h3 class="feature-title">Categorias</h3>
                    <p>Listagem das modalidades esportivas como Futebol, Vôlei, Basquete, Natação e Ciclismo.</p>
                </div>

                <div class="feature-card">
                    <i class="fa-solid fa-calendar-days feature-icon"></i>
                    <h3 class="feature-title">Eventos</h3>
                    <p>Apresenta os eventos esportivos mais próximos da sua localização.</p>
                </div>

                <div class="feature-card">
                    <i class="fa-solid fa-robot feature-icon"></i>
                    <h3 class="feature-title">Chatbot</h3>
                    <p>Ajuda você com dúvidas sobre esportes e categorias disponíveis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== CONTATO ====== -->
    <section id="contato" class="section">
        <div class="container">
            <h2 class="section-title">Entre em Contato</h2>
            <p class="section-intro">Estamos aqui para ajudar! Fale conosco pelos canais abaixo.</p>

            <div class="contact-wrapper">
                <div class="contact-info">

                    <div class="contact-item">
                        <i class="fa-solid fa-phone contact-icon"></i>
                        <div>
                            <h4>Telefone</h4>
                            <p>(11) 3456-7890</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fa-solid fa-envelope contact-icon"></i>
                        <div>
                            <h4>E-mail</h4>
                            <p><a href="mailto:contato@championssports.com.br">contato@championssports.com.br</a></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fa-solid fa-location-dot contact-icon"></i>
                        <div>
                            <h4>Endereço</h4>
                            <p>Av. Paulista, 1000 - São Paulo, SP</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fa-solid fa-clock contact-icon"></i>
                        <div>
                            <h4>Horário de Atendimento</h4>
                            <p>Seg - Sex: 9h às 18h<br>Sáb: 9h às 13h</p>
                        </div>
                    </div>
                </div>

                <form class="contact-form" method="post" action="enviar_mensagem.php">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input 
                            type="tel" 
                            id="telefone" 
                            name="telefone" 
                            placeholder="(XX) XXXXX-XXXX"
                            pattern="(\([0-9]{2}\)\s?)?([0-9]{4,5}-?[0-9]{4})"
                            title="Digite um número de telefone válido, com ou sem formatação. Ex: (61) 99999-8888 ou 61999998888">
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" rows="5" required></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Enviar Mensagem</button>
                </form>
            </div>
        </div>
    </section>

    <!-- ====== FOOTER ====== -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-columns">

                <div class="footer-about">
                    <h4>Sobre Nós</h4>
                    <p>A ChampionsSports conecta você aos melhores eventos esportivos e promoções na sua região.</p>
                </div>

                <div class="footer-links">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="telaPrincipal.php">Início</a></li>
                        <li><a href="eventos.php">Eventos</a></li>
                        <li><a href="categorias.php">Categorias</a></li>
                        <li><a href="#">Termos de Uso</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h4>Contato</h4>
                    <ul>
                        <li><a href="mailto:contato@championssports.com">contato@championssports.com</a></li>
                        <li>Telefone: (61) 99999-9999</li>
                        <li>Endereço: QNL 5657 - Brasília, DF</li>
                    </ul>
                </div>

                <div class="footer-social">
                    <h4>Siga-nos</h4>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">© <?= date('Y') ?> ChampionsSports. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="Js/sobre.js"></script>
</body>
</html>