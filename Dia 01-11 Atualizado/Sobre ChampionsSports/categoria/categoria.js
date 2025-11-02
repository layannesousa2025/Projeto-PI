document.addEventListener("DOMContentLoaded", () => {
  const categoryCards = document.querySelectorAll(".category-card")
  const searchBar = document.querySelector(".search-bar")

  // Atualiza as estrelas de favoritos
  const updateStars = (favorites = []) => {
    categoryCards.forEach((card) => {
      const star = card.querySelector(".favorite-star")
      star.classList.toggle("favorited", favorites.includes(card.dataset.category))
    })
  }

  // Toggle favorito
  const toggleFavorite = async (category, star) => {
    const isFavorited = star.classList.contains("favorited")
    const action = isFavorited ? "remove" : "add"

    try {
      const response = await fetch("categorias.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ categoria: category, acao: action }),
      })

      const result = await response.json()
      if (response.ok && result.success) {
        star.classList.toggle("favorited")
      } else {
        alert(result.message || "Erro ao atualizar favorito.")
      }
    } catch (err) {
      console.error("Erro:", err)
      alert("Não foi possível conectar ao servidor.")
    }
  }

  // Carrega favoritos do servidor
  const loadFavorites = async () => {
    try {
      const response = await fetch("get_favoritos.php")
      const data = await response.json()
      if (data.success) {
        updateStars(data.favorites)
      }
    } catch (err) {
      console.error("Erro ao carregar favoritos:", err)
    }
  }

  // Event listeners para cada card
  categoryCards.forEach((card) => {
    const category = card.dataset.category
    const star = card.querySelector(".favorite-star")

    // Click na estrela
    star.addEventListener("click", (e) => {
      e.stopPropagation()
      toggleFavorite(category, star)
    })

    // Click no card
    card.addEventListener("click", () => {
      window.location.href = `eventos.html?game=${category}`
    })
  })

  // Busca de categorias
  searchBar.addEventListener("input", (e) => {
    const term = e.target.value.toLowerCase()
    categoryCards.forEach((card) => {
      const name = card.dataset.category.toLowerCase()
      card.style.display = name.includes(term) ? "" : "none"
    })
  })

  // Carrega favoritos ao iniciar
  loadFavorites()
})
