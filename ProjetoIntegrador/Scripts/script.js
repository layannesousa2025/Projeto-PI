document.addEventListener("DOMContentLoaded", () => {
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  const mobileMenuOverlay = document.querySelector(".mobile-menu-overlay")
  const mobileMenu = document.querySelector(".mobile-menu")
  const mobileMenuClose = document.querySelector(".mobile-menu-close")

  // Open mobile menu
  mobileMenuToggle.addEventListener("click", () => {
    mobileMenuOverlay.style.display = "block"
    setTimeout(() => {
      mobileMenu.classList.add("active")
    }, 10)
  })

  // Close mobile menu
  function closeMobileMenu() {
    mobileMenu.classList.remove("active")
    setTimeout(() => {
      mobileMenuOverlay.style.display = "none"
    }, 300)
  }

  mobileMenuClose.addEventListener("click", closeMobileMenu)

  // Close menu when clicking overlay
  mobileMenuOverlay.addEventListener("click", (e) => {
    if (e.target === mobileMenuOverlay) {
      closeMobileMenu()
    }
  })

  // Close menu on escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && mobileMenu.classList.contains("active")) {
      closeMobileMenu()
    }
  })

  // Search functionality
  const searchInput = document.querySelector(".search-input")
  const searchBtn = document.querySelector(".search-btn")

  searchBtn.addEventListener("click", () => {
    const searchTerm = searchInput.value.trim()
    if (searchTerm) {
      console.log("Searching for:", searchTerm)
      // Implement search functionality here
    }
  })

  searchInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      searchBtn.click()
    }
  })
})
