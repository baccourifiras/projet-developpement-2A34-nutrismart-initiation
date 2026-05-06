(() => {
    const apiUrl = window.API_URL;

    if (!apiUrl) {
        return;
    }

    const historiqueForm = document.getElementById('historiqueForm');
    const historiqueFeedback = document.getElementById('historiqueFeedback');
    const historiqueSelect = document.getElementById('histoRegime');
    const historiqueFilter = document.getElementById('historiqueFilter');
    const historiqueSearch = document.getElementById('historiqueSearch');
    const historiqueList = document.getElementById('historiqueList');
    const viewButtons = document.getElementById('historyViewButtons');

    let regimes = [];
    let histos = [];
    let currentView = 'cards';

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    async function request(action, method = 'GET', data = null) {
        const url = `${apiUrl}?action=${encodeURIComponent(action)}`;
        const options = { method };

        if (method !== 'GET' && data) {
            options.headers = { 'Content-Type': 'application/json' };
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(payload.error || 'Erreur serveur.');
        }

        return payload;
    }

    function showFeedback(type, message) {
        historiqueFeedback.textContent = message;
        historiqueFeedback.className = `feedback ${type === 'success' ? 'is-success' : 'is-error'}`;
    }

    function clearFeedback() {
        historiqueFeedback.textContent = '';
        historiqueFeedback.className = 'feedback';
    }

    function formatDate(value) {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }).format(new Date(value));
    }

    function getRegimeById(idRegime) {
        return regimes.find((item) => Number(item.id_regime) === Number(idRegime)) || null;
    }

    function getTypeLabel(type) {
        if (type === 'cut') return 'Cut';
        if (type === 'bulk') return 'Bulk';
        return 'Equilibre';
    }

    function populateRegimeSelect() {
        historiqueSelect.innerHTML = '<option value="">Selectionner</option>' +
            regimes.map((regime) => (
                `<option value="${regime.id_regime}">R-${String(regime.id_regime).padStart(3, '0')} - ${escapeHtml(getTypeLabel(regime.type_regime))}</option>`
            )).join('');
    }

    function renderStats() {
        const totals = histos.reduce((carry, histo) => {
            const regime = getRegimeById(histo.id_regime);
            const type = regime ? regime.type_regime : 'equilibre';
            carry.total += 1;
            carry[type] += 1;
            return carry;
        }, { total: 0, cut: 0, bulk: 0, equilibre: 0 });

        document.getElementById('historyStatTotal').textContent = String(totals.total);
        document.getElementById('historyStatCut').textContent = String(totals.cut);
        document.getElementById('historyStatBulk').textContent = String(totals.bulk);
        document.getElementById('historyStatEquilibre').textContent = String(totals.equilibre);
    }

    function filteredHistos() {
        const filterValue = historiqueFilter.value;
        const searchValue = historiqueSearch.value.trim().toLowerCase();

        return histos.filter((histo) => {
            const regime = getRegimeById(histo.id_regime);
            const type = regime ? regime.type_regime : '';
            const matchesFilter = filterValue === 'tous' || type === filterValue;
            const matchesSearch = !searchValue || histo.recommandation.toLowerCase().includes(searchValue);

            return matchesFilter && matchesSearch;
        });
    }

    function renderCards(items) {
        return `
            <div class="history-grid">
                ${items.map((item) => {
                    const regime = getRegimeById(item.id_regime);
                    const type = regime ? regime.type_regime : 'equilibre';

                    return `
                        <article class="history-card">
                            <div class="history-card__top">
                                <span class="tag tag--${escapeHtml(type)}">${escapeHtml(getTypeLabel(type))}</span>
                                <span class="history-card__meta">H-${String(item.id_historique).padStart(3, '0')}</span>
                            </div>
                            <p class="history-card__copy">${escapeHtml(item.recommandation)}</p>
                            <div class="history-card__bottom">
                                <span class="history-card__meta">R-${String(item.id_regime).padStart(3, '0')} - ${escapeHtml(formatDate(item.date))}</span>
                                <button class="inline-action is-danger" type="button" data-delete-id="${item.id_historique}">Supprimer</button>
                            </div>
                        </article>
                    `;
                }).join('')}
            </div>
        `;
    }

    function renderTimeline(items) {
        return `
            <div class="timeline">
                ${items.map((item) => {
                    const regime = getRegimeById(item.id_regime);
                    const type = regime ? regime.type_regime : 'equilibre';

                    return `
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <article class="timeline-card">
                                <div class="history-card__top">
                                    <span class="tag tag--${escapeHtml(type)}">${escapeHtml(getTypeLabel(type))}</span>
                                    <span class="history-card__meta">${escapeHtml(formatDate(item.date))}</span>
                                </div>
                                <p class="history-card__copy">${escapeHtml(item.recommandation)}</p>
                                <div class="history-card__bottom">
                                    <span class="history-card__meta">R-${String(item.id_regime).padStart(3, '0')}</span>
                                    <button class="inline-action is-danger" type="button" data-delete-id="${item.id_historique}">Supprimer</button>
                                </div>
                            </article>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    function renderList() {
        const items = filteredHistos();

        if (items.length === 0) {
            historiqueList.innerHTML = '<div class="empty-state">Aucune recommandation ne correspond a votre filtre.</div>';
            return;
        }

        historiqueList.innerHTML = currentView === 'cards' ? renderCards(items) : renderTimeline(items);
    }

    async function loadData() {
        const [regimeData, histoData] = await Promise.all([request('regimes'), request('histos')]);
        regimes = Array.isArray(regimeData) ? regimeData : [];
        histos = Array.isArray(histoData) ? histoData : [];
        populateRegimeSelect();
        renderStats();
        renderList();
    }

    historiqueForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            id_regime: Number(historiqueSelect.value),
            recommandation: document.getElementById('historiqueTexte').value.trim(),
        };

        try {
            await request('histo', 'POST', payload);
            historiqueForm.reset();
            clearFeedback();
            await loadData();
            showFeedback('success', 'Recommandation ajoutee avec succes.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    historiqueFilter.addEventListener('change', renderList);
    historiqueSearch.addEventListener('input', renderList);

    viewButtons.addEventListener('click', (event) => {
        const button = event.target.closest('[data-view]');

        if (!button) {
            return;
        }

        currentView = button.dataset.view || 'cards';
        viewButtons.querySelectorAll('[data-view]').forEach((item) => {
            item.classList.toggle('is-active', item === button);
        });
        renderList();
    });

    historiqueList.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-delete-id]');

        if (!button) {
            return;
        }

        if (!window.confirm('Supprimer cette recommandation ?')) {
            return;
        }

        try {
            await request('delete', 'POST', { type: 'histo', id: Number(button.dataset.deleteId) });
            await loadData();
            showFeedback('success', 'Recommandation supprimee avec succes.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    loadData().catch((error) => {
        historiqueList.innerHTML = '<div class="empty-state">Impossible de charger les recommandations.</div>';
        showFeedback('error', error.message);
    });
})();
