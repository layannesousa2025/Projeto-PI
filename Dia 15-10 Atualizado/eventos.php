<?php
session_start(); // Inicia a sessão
$game_filter = isset($_GET['game']) ? htmlspecialchars($_GET['game']) : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Esportivos</title>
    <link rel="stylesheet" href="Css/eventos.css"> <!-- Supondo que você tenha ou criará este CSS -->
    <style>
        /* Estilos básicos para funcionar. Mova para eventos.css depois */
        body { background-color: #1a1a2e; color: white; font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 900px; margin: auto; }
        .filters { background-color: #2d2d46; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; }
        .filters input, .filters select, .filters button { padding: 10px; border-radius: 5px; border: none; }
        .results-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .card { background-color: #2d2d46; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .card .title { font-size: 1.2em; font-weight: bold; color: #ffc107; }
        .card .muted { color: #ccc; font-size: 0.9em; margin: 5px 0; }
        .card .inline { margin-top: 10px; }
        .card .btn { background-color: #007bff; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none; margin-right: 10px; }
        #empty { text-align: center; padding: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Encontre Eventos</h1>
        <div class="filters">
            <input type="search" id="q" placeholder="Pesquisar por nome, cidade...">
            <select id="game">
                <option value="">Todas as categorias</option>
                <option value="Caminhada">Caminhada</option>
                <option value="Academia">Academia</option>
                <option value="Ciclismo">Ciclismo</option>
                <option value="Natacao">Natação</option>
                <option value="Voleibol">Voleibol</option>
                <option value="PCD">PCD</option>
            </select>
            <input type="date" id="from" title="Data inicial">
            <input type="date" id="to" title="Data final">
            <button id="clearFilters">Limpar Filtros</button>
            <button id="useLoc">Usar minha localização</button>
        </div>

        <div class="results-header">
            <span id="locHint"></span>
            <span><span id="count">0</span> eventos encontrados</span>
        </div>

        <div id="results"></div>
        <div id="empty" style="display: none;">Nenhum evento encontrado com os filtros aplicados.</div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Dados de exemplo (substitua por sua API) ---
        const events = [
            { id: 1, title: "Caminhada na Praça", game: "Caminhada", cidade: "Brasília/DF", venue: "PRAÇA DO BURITI, Brasília, DF, Brasil", date: "2025-11-30", lat: -15.7999, lon: -47.8642 },
            { id: 2, title: "Desafio de Força", game: "Academia", cidade: "Brasília/DF", venue: "St. N QNN, 09 Ceilândia Norte - Ceilândia, Brasília - DF", date: "2025-08-15", lat: -15.8196, lon: -48.1153 },
            { id: 3, title: "Pedal na Torre Digital", game: "Ciclismo", cidade: "Brasília/DF", venue: "SIT Torre de TV Digital de Brasília", date: "2025-05-13", lat: -15.6969, lon: -47.7883 },
            { id: 4, title: "Festival Aquático", game: "Natacao", cidade: "Brasília/DF", venue: "Dentro do Sindilegis - 610, lote 70", date: "2025-06-25", lat: -15.8183, lon: -47.8925 },
            { id: 5, title: "Copa de Vôlei", game: "Voleibol", cidade: "Brasília/DF", venue: "St. F Norte - Taguatinga, Brasília", date: "2025-09-21", lat: -15.8315, lon: -48.0569 },
            { id: 6, title: "Paradesporto em Ação", game: "PCD", cidade: "Brasília/DF", venue: "Centro Olímpico e Paralímpico", date: "2025-09-30", lat: -15.8351, lon: -47.9297 }
        ];

        // --- Estado ---
        let userLoc = null; // {lat, lon}

        // --- Utilidades ---
        const $ = sel => document.querySelector(sel);
        const gameFilterFromURL = '<?php echo $game_filter; ?>';

        function toDate(s) { const d = new Date(s + 'T00:00:00'); d.setHours(12); return d; }

        // Haversine
        function distKm(a, b) {
            if (!a || !b) return Infinity;
            const R = 6371;
            const dLat = (b.lat - a.lat) * Math.PI / 180;
            const dLon = (b.lon - a.lon) * Math.PI / 180;
            const s1 = Math.sin(dLat / 2) ** 2 + Math.cos(a.lat * Math.PI / 180) * Math.cos(b.lat * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
            return 2 * R * Math.asin(Math.sqrt(s1));
        }

        function render() {
            const q = $('#q').value.trim().toLowerCase();
            const game = $('#game').value;
            const from = $('#from').value ? toDate($('#from').value) : null;
            const to = $('#to').value ? toDate($('#to').value) : null;

            const filtered = events
                .filter(ev => !q || `${ev.title} ${ev.city} ${ev.game}`.toLowerCase().includes(q))
                .filter(ev => !game || ev.game === game)
                .filter(ev => !from || toDate(ev.date) >= from)
                .filter(ev => !to || toDate(ev.date) <= to)
                .map(ev => ({
                    ...ev,
                    distance: userLoc ? distKm(userLoc, { lat: ev.lat, lon: ev.lon }) : null
                }));

            filtered.sort((a, b) => {
                const da = a.distance ?? Infinity, db = b.distance ?? Infinity;
                if (da === db) return toDate(a.date) - toDate(b.date);
                return da - db;
            });

            $('#count').textContent = filtered.length;
            $('#empty').style.display = filtered.length ? 'none' : 'block';

            const res = $('#results');
            res.innerHTML = '';

            for (const ev of filtered) {
                const card = document.createElement('article');
                card.className = 'card';
                const distanceText = ev.distance !== null ? `· 📍 ${ev.distance.toFixed(1)} km` : '';
                card.innerHTML = `
                    <div class="title">${ev.title}</div>
                    <div class="muted">🗓️ ${new Date(ev.date).toLocaleDateString('pt-BR')} · ${ev.venue}, ${ev.city} ${distanceText}</div>
                    <div class="inline">
                        <a class="btn" href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(ev.venue + ' ' + ev.city)}" target="_blank" rel="noopener">Ver no mapa</a>
                        <a class="btn" href="#" onclick="alert('Conecte a página do evento aqui.'); return false;">Detalhes</a>
                    </div>`;
                res.appendChild(card);
            }

            $('#locHint').textContent = userLoc ? `Sua localização está ativa.` : 'Dica: ative sua localização para ver distâncias';
        }

        // --- Eventos UI ---
        ['q', 'game', 'from', 'to'].forEach(id => {
            document.getElementById(id).addEventListener('input', render);
            document.getElementById(id).addEventListener('change', render);
        });

        $('#clearFilters').addEventListener('click', () => {
            $('#q').value = '';
            $('#game').value = '';
            $('#from').value = '';
            $('#to').value = '';
            render();
        });

        $('#useLoc').addEventListener('click', () => {
            if (!('geolocation' in navigator)) {
                alert('Geolocalização não suportada neste navegador.');
                return;
            }
            navigator.geolocation.getCurrentPosition((pos) => {
                userLoc = { lat: pos.coords.latitude, lon: pos.coords.longitude };
                render();
            }, (err) => {
                console.warn('Geo erro', err);
                alert('Não foi possível obter sua localização. Verifique permissões do navegador.');
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
        });

        // Inicializa
        if (gameFilterFromURL) {
            $('#game').value = gameFilterFromURL;
        }
        render();
    });
</script>

</body>
</html>