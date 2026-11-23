<?php
// TailwindCSS via CDN compartilhado para todas as páginas PHP
?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary: '#6e00ff',
          secondary: '#ff00aa',
          dark: '#1a1a2e',
          darkCard: '#2d2d46',
          darkAlt: '#23233b',
          darkInput: '#3d3d5a'
        },
        fontFamily: {
          sans: ['Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif']
        }
      }
    }
  }
</script>
