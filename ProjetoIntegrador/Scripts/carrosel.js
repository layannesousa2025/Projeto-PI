document.addEventListener('DOMContentLoaded', function() {
    // Elementos do carrossel
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevButton = document.querySelector('.prev-button');
    const nextButton = document.querySelector('.next-button');
    
    let currentIndex = 0;
    let interval;
    const autoplayTime = 5000; // 5 segundos
    
    // Iniciar o autoplay
    startAutoplay();
    
    // Função para mostrar um slide específico
    function showSlide(index) {
        // Remover classe active de todos os slides e indicadores
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        // Adicionar classe active ao slide atual e seu indicador
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        
        // Atualizar o índice atual
        currentIndex = index;
    }
    
    // Função para ir para o próximo slide
    function nextSlide() {
        const newIndex = (currentIndex + 1) % slides.length;
        showSlide(newIndex);
    }
    
    // Função para ir para o slide anterior
    function prevSlide() {
        const newIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(newIndex);
    }
    
    // Função para iniciar o autoplay
    function startAutoplay() {
        clearInterval(interval);
        interval = setInterval(nextSlide, autoplayTime);
    }
    
    // Função para pausar o autoplay
    function pauseAutoplay() {
        clearInterval(interval);
    }
    
    // Event listeners para os botões de navegação
    nextButton.addEventListener('click', function() {
        nextSlide();
        pauseAutoplay();
        startAutoplay();
    });
    
    prevButton.addEventListener('click', function() {
        prevSlide();
        pauseAutoplay();
        startAutoplay();
    });
    
    // Event listeners para os indicadores
    indicators.forEach(indicator => {
        indicator.addEventListener('click', function() {
            const slideIndex = parseInt(this.getAttribute('data-index'));
            showSlide(slideIndex);
            pauseAutoplay();
            startAutoplay();
        });
    });
    
    // Pausar o autoplay quando o mouse estiver sobre o carrossel
    const carouselContainer = document.querySelector('.carousel-container');
    carouselContainer.addEventListener('mouseenter', pauseAutoplay);
    carouselContainer.addEventListener('mouseleave', startAutoplay);
    
    // Adicionar suporte para gestos de swipe em dispositivos móveis
    let touchStartX = 0;
    let touchEndX = 0;
    
    carouselContainer.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, false);
    
    carouselContainer.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);
    
    function handleSwipe() {
        // Detectar direção do swipe
        if (touchEndX < touchStartX - 50) {
            // Swipe para a esquerda - próximo slide
            nextSlide();
            pauseAutoplay();
            startAutoplay();
        }
        
        if (touchEndX > touchStartX + 50) {
            // Swipe para a direita - slide anterior
            prevSlide();
            pauseAutoplay();
            startAutoplay();
        }
    }
});