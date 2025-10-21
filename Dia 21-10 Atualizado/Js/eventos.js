const qInput = document.getElementById('q');
const gameSelect = document.getElementById('game');
const fromInput = document.getElementById('from');
const toInput = document.getElementById('to');
const clearBtn = document.getElementById('clearFilters');
const resultsGrid = document.getElementById('results-grid'); // Alterado de tbody para grid
const countSpan = document.getElementById('count');
const emptyDiv = document.getElementById('empty');

// Elementos do Modal
const modal = document.getElementById('event-modal');
const modalCloseBtn = document.querySelector('.close-button');
const modalBody = document.getElementById('modal-body');

let allEventos = []; // Armazena todos os eventos para consulta

let eventos = []; // <- Sempre deve ser array

async function fetchEventos() {
  try {
    const response = await fetch('eventos.php'); // Linha 14
    const data = await response.json();

    console.log("Resposta da API:", data); // ← AJUDA A DEBUGAR

    if (data && data.success && Array.isArray(data.data)) {
      eventos = data.data;
      allEventos = data.data; // Guarda uma cópia de todos os eventos
      aplicarFiltroInicialDaURL();
      aplicarFiltros();
    } else {
      console.error("Resposta inesperada:", data);
      eventos = []; // ← evita erro no .filter
      updateGrid([]);
    }

  } catch (error) {
    console.error("Erro ao buscar eventos:", error);
    alert('Erro ao carregar eventos: ' + error.message);
  }
}

function updateGrid(lista) {
  resultsGrid.innerHTML = '';

  if (lista.length === 0) {
    emptyDiv.style.display = 'block';
    countSpan.textContent = '0';
    return;
  }
  emptyDiv.style.display = 'none';
  countSpan.textContent = lista.length;

  lista.forEach(ev => {
    const card = document.createElement('div');
    card.className = 'event-card';

    // Formata a data para o padrão brasileiro
    const dataFormatada = new Date(ev.data_eventos + 'T00:00:00').toLocaleDateString('pt-BR');

    card.innerHTML = `
      <div class="event-info">
        <h3>${ev.tipo_eventos}</h3>
        <p class="event-category">${ev.categorias_esportes}</p>
        <p>${ev.informacao || 'Mais detalhes em breve.'}</p>
        <div class="event-meta">
          <span class="event-date">📅 ${dataFormatada}</span>
          <span class="event-location">📍 ${ev.localizacao}</span>
        </div>
      </div>
      <div class="event-actions">
        <button onclick="showDetails(${ev.id_eventos})">Ver Detalhes</button>
      </div>
    `;

    resultsGrid.appendChild(card);
  });
}

function showDetails(eventId) {
  const evento = allEventos.find(ev => ev.id_eventos == eventId);
  if (!evento) {
    alert('Evento não encontrado!');
    return;
  }

  // Formata a data para o padrão brasileiro
  const dataFormatada = new Date(evento.data_eventos + 'T00:00:00').toLocaleDateString('pt-BR');

  // Preenche o conteúdo do modal
  document.getElementById('modal-title').textContent = evento.tipo_eventos;
  document.getElementById('modal-category').textContent = evento.categorias_esportes;
  document.getElementById('modal-info').textContent = evento.informacao || 'Mais detalhes em breve.';
  document.getElementById('modal-date-location').innerHTML = `📅 ${dataFormatada} &nbsp;&nbsp; | &nbsp;&nbsp; 📍 ${evento.localizacao}`;
  
  const mapContainer = document.getElementById('modal-map-container');
  console.log("Link do mapa a ser incorporado:", evento.link); // Adicione esta linha para depuração
  if (evento.link) {
    // Usa o link do Google Maps (geralmente um iframe)
    mapContainer.innerHTML = `<iframe src="${evento.link}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
  } else {
    mapContainer.innerHTML = '<p>Localização no mapa não disponível.</p>';
  }

  // Exibe o modal
  modal.style.display = 'block';
}

// Fecha o modal ao clicar no 'X'
modalCloseBtn.onclick = function() {
  modal.style.display = "none";
  // Limpa o iframe para parar qualquer carregamento
  document.getElementById('modal-map-container').innerHTML = '';
};

function aplicarFiltros() {
  const q = qInput.value.toLowerCase();
  const game = gameSelect.value.toLowerCase();
  const from = fromInput.value;
  const to = toInput.value;

  // Proteção: se eventos não for array, não aplica filtro
  if (!Array.isArray(eventos)) {
    console.error("eventos não é um array:", eventos);
    return;
  }

  const filtrados = eventos.filter(ev => {
    const nomeCidade = (ev.tipo_eventos + ' ' + ev.localizacao).toLowerCase();
    if (q && !nomeCidade.includes(q)) return false;

    // Filtro por categoria
    if (game && game !== '' && ev.categorias_esportes.toLowerCase() !== game) return false;

    if (from && ev.data_eventos < from) return false;
    if (to && ev.data_eventos > to) return false;

    return true;
  });

  updateGrid(filtrados);
}

function aplicarFiltroInicialDaURL() {
  const params = new URLSearchParams(window.location.search);
  const gameFromURL = params.get('game');

  if (gameFromURL) {
    // Encontra a opção com um valor que corresponda a gameFromURL (ignorando maiúsculas/minúsculas)
    const matchingOption = Array.from(gameSelect.options).find(option => option.value.toLowerCase() === gameFromURL.toLowerCase());
    if (matchingOption) {
      gameSelect.value = matchingOption.value; // Define o valor do select para o valor real da opção
    }
  }
}

// Atualiza ao mudar qualquer filtro
[qInput, gameSelect, fromInput, toInput].forEach(el => {
  el.addEventListener('input', aplicarFiltros);
});

clearBtn.addEventListener('click', () => {
  qInput.value = '';
  gameSelect.value = '';
  fromInput.value = '';
  toInput.value = '';
  updateGrid(eventos); // ← volta tudo
});

// Inicialização
document.addEventListener('DOMContentLoaded', fetchEventos);

// Fecha o modal se o usuário clicar fora da área de conteúdo
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
    document.getElementById('modal-map-container').innerHTML = '';
  }
};