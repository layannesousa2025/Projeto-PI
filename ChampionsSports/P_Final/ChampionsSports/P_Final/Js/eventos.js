const qInput = document.getElementById('q');
const gameSelect = document.getElementById('game');
const fromInput = document.getElementById('from');
const toInput = document.getElementById('to');
const clearBtn = document.getElementById('clearFilters');
const resultsGrid = document.getElementById('results-grid');
const countSpan = document.getElementById('count');
const emptyDiv = document.getElementById('empty');

// --- Elementos do Modal ---
const modal = document.getElementById('event-modal');
const modalCloseBtn = document.querySelector('.close-button');

// Armazena todos os eventos para consulta rápida nos detalhes
let allEventos = [];

/**
 * Busca os eventos da API (get_eventos.php) e inicia a renderização.
 */
async function fetchEventos() {
    // Mostra um indicador de carregamento
    resultsGrid.innerHTML = `<p style="color: #ccc; text-align: center;">Carregando eventos...</p>`;
    emptyDiv.style.display = 'none';
    try {
        const response = await fetch('get_eventos.php');
        if (!response.ok) {
            throw new Error(`Erro na requisição: ${response.statusText}`);
        }
        const data = await response.json();

        if (data && data.success && Array.isArray(data.data)) {
            allEventos = data.data; // Guarda cópia de todos os eventos
            aplicarFiltroInicialDaURL(); // Verifica se veio filtro da pág. de categorias
            aplicarFiltros(); // Aplica filtros (ou mostra todos)
        } else {
            throw new Error(data.message || 'A resposta da API não continha dados de eventos válidos.');
        }
    } catch (error) {
        console.error("Erro ao buscar eventos:", error);
        resultsGrid.innerHTML = `<p style="color: #ff8a80; text-align: center;">Falha ao carregar eventos. Tente novamente mais tarde.</p>`;
        emptyDiv.style.display = 'none';
        countSpan.textContent = '0';
    }
}

/**
 * Atualiza o grid de eventos na tela com a lista fornecida.
 * @param {Array} lista - A lista de eventos a ser exibida.
 */
function updateGrid(lista) {
    resultsGrid.innerHTML = ''; // Limpa o grid atual

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

        // Formata a data para o padrão brasileiro (dd/mm/aaaa)
        // CORREÇÃO: Usar o campo 'data_eventos' que vem diretamente da API.
        const dataFormatada = new Date(ev.data_eventos + 'T00:00:00').toLocaleDateString('pt-BR');

        card.innerHTML = `
            <div class="event-content">
                <span class="event-category">${ev.categorias_esportes}</span> <!-- CORREÇÃO: Usar nome original do campo -->
                <h3 class="event-title">${ev.tipo_eventos}</h3> <!-- CORREÇÃO: Usar nome original do campo -->
                <div class="event-info">
                    <div><i class="fas fa-calendar"></i> ${dataFormatada}</div>
                    <div><i class="fas fa-map-marker-alt"></i> ${ev.localizacao}</div> <!-- CORREÇÃO: Usar nome original do campo -->
                </div>
            </div>
            <div class="event-actions">
                <button onclick="showDetails(${ev.id_eventos})">Ver Detalhes</button>
            </div>
        `;
        resultsGrid.appendChild(card);
    });
}

/**
 * Exibe o modal com os detalhes de um evento específico.
 * @param {number} eventId - O ID do evento a ser exibido.
 */
function showDetails(eventId) {
    const evento = allEventos.find(ev => ev.id_eventos == eventId);
    if (!evento) return;

    const dataFormatada = new Date(evento.data_eventos + 'T00:00:00').toLocaleDateString('pt-BR');
    
    // CORREÇÃO: Usar os nomes de campos originais do banco de dados
    document.getElementById('modal-title').textContent = evento.tipo_eventos;
    document.getElementById('modal-category').textContent = evento.categorias_esportes;
    document.getElementById('modal-info').textContent = evento.informacao || 'Nenhuma informação adicional disponível.';
    document.getElementById('modal-date-location').innerHTML = `
        <i class="fas fa-calendar"></i> ${dataFormatada} &nbsp;&nbsp; | &nbsp;&nbsp; <i class="fas fa-map-marker-alt"></i> ${evento.localizacao}
    `;

    const mapContainer = document.getElementById('modal-map-container');
    // Verifica se o link é um URL HTTP/HTTPS válido antes de usar no iframe
    if (evento.link_localizacao && (evento.link_localizacao.startsWith('http://') || evento.link_localizacao.startsWith('https://'))) {
        // O link já deve ser um URL de incorporação do Google Maps
        mapContainer.innerHTML = `<iframe src="${evento.link_localizacao}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
    } else {
        mapContainer.innerHTML = '<p>Localização no mapa não disponível.</p>';
    }

    modal.classList.add('active');
}

/**
 * Filtra a lista de eventos com base nos valores dos inputs e atualiza o grid.
 */
function aplicarFiltros() {
    const q = qInput.value.toLowerCase().trim();
    const game = gameSelect.value;
    const from = fromInput.value; // Formato 'YYYY-MM-DD'
    const to = toInput.value;     // Formato 'YYYY-MM-DD'

    const filtrados = allEventos.filter(ev => {
        // CORREÇÃO: Filtrar usando os nomes de campos corretos
        const nomeLocal = `${ev.tipo_eventos} ${ev.localizacao}`.toLowerCase();
        if (q && !nomeLocal.includes(q)) return false; 
        if (game && ev.categorias_esportes !== game) return false;
        if (from && ev.data_eventos < from) return false;
        if (to && ev.data_eventos > to) return false;
        return true;
    });

    updateGrid(filtrados);
}

/**
 * Verifica se a URL contém um parâmetro 'game' e pré-seleciona o filtro.
 */
function aplicarFiltroInicialDaURL() {
    const params = new URLSearchParams(window.location.search);
    const gameFromURL = params.get('game');
    if (gameFromURL) {
        gameSelect.value = gameFromURL;
    }
}

// --- Event Listeners ---

// Filtros
[qInput, gameSelect, fromInput, toInput].forEach(el => {
    el.addEventListener('input', aplicarFiltros);
});

// Botão de limpar filtros
clearBtn.addEventListener('click', () => {
    qInput.value = '';
    gameSelect.value = '';
    fromInput.value = '';
    toInput.value = '';
    aplicarFiltros(); // Re-aplica os filtros (que agora estão vazios)
});

// Modal
modalCloseBtn.addEventListener('click', () => modal.classList.remove('active'));
modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.remove('active');
    }
});

// Fechar menu mobile ao clicar em um link
const menuToggle = document.getElementById('menu-toggle');
const navLinks = document.querySelectorAll('.nav a');
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            menuToggle.checked = false;
        }
    });
});

// --- Inicialização ---
document.addEventListener('DOMContentLoaded', fetchEventos);