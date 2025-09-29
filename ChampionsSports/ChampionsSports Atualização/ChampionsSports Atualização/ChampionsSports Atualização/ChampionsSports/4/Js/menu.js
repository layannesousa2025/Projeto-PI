  // Pega todos os itens do menu
    const menuItems = document.querySelectorAll(".slide-menu ul li");

    // Adiciona evento de clique
    menuItems.forEach(item => {
      item.addEventListener("click", () => {
        const link = item.getAttribute("data-link");
        window.location.href = link; // redireciona para a página
      });
    });
  