   // Pega todos os itens do menu
    const menuItems = document.querySelectorAll(".slide-menu ul li");
    const menuToggle = document.getElementById('menu-toggle'); // Pega o checkbox do menu

    // Adiciona evento de clique
    menuItems.forEach(item => {
      item.addEventListener("click", (event) => {
        // Encontra o elemento 'a' pai do 'li' que foi clicado
        const anchor = item.closest('a');
        if (!anchor) return; // Se não encontrar a tag <a>, não faz nada

        const href = anchor.getAttribute('href');

        // Se for um link de âncora (começa com #), apenas fecha o menu
        if (href.startsWith('#')) {
          menuToggle.checked = false; // Fecha o menu desmarcando o checkbox
        } else {
          // Se for um link para outra página, redireciona
          window.location.href = href;
        }
      });
    });
  
  