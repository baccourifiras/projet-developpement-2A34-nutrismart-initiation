(() => {
    const apiUrl = window.API_URL;

    if (!apiUrl) {
        return;
    }

    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const selectedRegimeSection = document.getElementById('selectedRegimeSection');
    const selectedRegimeTitle = document.getElementById('selectedRegimeTitle');
    const selectedRegimeStats = document.getElementById('selectedRegimeStats');
    const suiviStatsSection = document.getElementById('suiviStatsSection');
    const suiviFormSection = document.getElementById('suiviFormSection');
    const suiviTableSection = document.getElementById('suiviTableSection');
    const suiviForm = document.getElementById('suiviForm');
    const suiviFormTitle = document.getElementById('suiviFormTitle');
    const suiviSubmitButton = document.getElementById('suiviSubmitButton');
    const suiviFeedback = document.getElementById('suiviFeedback');
    const suiviTableBody = document.getElementById('suiviTableBody');

    let regimes = [];
    let currentRegime = null;
    let currentSuivis = [];
    let editingSuiviId = null;

    function todayValue() {
        return new Date().toISOString().split('T')[0];
    }

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
        suiviFeedback.textContent = message;
        suiviFeedback.className = `feedback ${type === 'success' ? 'is-success' : 'is-error'}`;
    }

    function clearFeedback() {
        suiviFeedback.textContent = '';
        suiviFeedback.className = 'feedback';
    }

    function formatDate(value) {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }).format(new Date(value));
    }

    function sortSuivisByDate(items) {
        return [...items].sort((left, right) => new Date(right.date) - new Date(left.date));
    }

    function getTypeLabel(type) {
        if (type === 'cut') return 'Cut';
        if (type === 'bulk') return 'Bulk';
        return 'Equilibre';
    }

    function renderSearchResults(query = '') {
        const normalizedQuery = query.trim().toLowerCase();
        const filtered = regimes.filter((regime) => {
            if (!normalizedQuery) {
                return true;
            }

            const idLabel = `r-${String(regime.id_regime).padStart(3, '0')}`;
            return (
                idLabel.includes(normalizedQuery) ||
                String(regime.calories_cible).includes(normalizedQuery) ||
                regime.type_regime.toLowerCase().includes(normalizedQuery)
            );
        });

        if (filtered.length === 0) {
            searchResults.innerHTML = '<div class="empty-state">Aucun regime ne correspond a cette recherche.</div>';
            return;
        }

        searchResults.innerHTML = filtered.map((regime) => `
            <article class="result-card">
                <div class="result-card__top">
                    <span class="tag tag--${escapeHtml(regime.type_regime)}">${escapeHtml(getTypeLabel(regime.type_regime))}</span>
                    <strong>R-${String(regime.id_regime).padStart(3, '0')}</strong>
                </div>
                <div class="result-card__meta">
                    ${escapeHtml(regime.calories_cible)} kcal cible<br>
                    Poids initial: ${escapeHtml(regime.poids_initial)} kg<br>
                    Debut: ${escapeHtml(formatDate(regime.date_debut))}
                </div>
                <div class="button-row">
                    <button class="button button--primary" type="button" data-select-id="${regime.id_regime}">Choisir ce regime</button>
                </div>
            </article>
        `).join('');
    }

    function renderCurrentRegime() {
        if (!currentRegime) {
            selectedRegimeSection.classList.add('is-hidden');
            return;
        }

        selectedRegimeSection.classList.remove('is-hidden');
        selectedRegimeTitle.textContent = `Regime R-${String(currentRegime.id_regime).padStart(3, '0')} - ${getTypeLabel(currentRegime.type_regime)}`;
        selectedRegimeStats.innerHTML = `
            <article class="stat-card">
                <span class="stat-card__label">Calories cible</span>
                <strong>${escapeHtml(currentRegime.calories_cible)} kcal</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Poids initial</span>
                <strong>${escapeHtml(currentRegime.poids_initial)} kg</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Date de debut</span>
                <strong>${escapeHtml(formatDate(currentRegime.date_debut))}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Duree</span>
                <strong>${escapeHtml(currentRegime.duree)} jours</strong>
            </article>
        `;
    }

    function renderSuiviStats() {
        suiviStatsSection.classList.remove('is-hidden');

        if (currentSuivis.length === 0) {
            document.getElementById('statDays').textContent = '0';
            document.getElementById('statWeight').textContent = '-';
            document.getElementById('statChange').textContent = '-';
            document.getElementById('statCalories').textContent = '-';
            return;
        }

        const sorted = sortSuivisByDate(currentSuivis);
        const latest = sorted[0];
        const averageCalories = Math.round(
            currentSuivis.reduce((total, item) => total + Number(item.calories_consommees), 0) / currentSuivis.length
        );
        const variation = (Number(latest.poids) - Number(currentRegime.poids_initial)).toFixed(1);

        document.getElementById('statDays').textContent = String(currentSuivis.length);
        document.getElementById('statWeight').textContent = `${latest.poids} kg`;
        document.getElementById('statChange').textContent = `${variation.startsWith('-') ? '' : '+'}${variation} kg`;
        document.getElementById('statCalories').textContent = `${averageCalories} kcal`;
    }

    function renderSuiviTable() {
        suiviTableSection.classList.remove('is-hidden');

        if (currentSuivis.length === 0) {
            suiviTableBody.innerHTML = '<tr><td colspan="5">Aucune entree de suivi pour ce regime.</td></tr>';
            return;
        }

        const sorted = sortSuivisByDate(currentSuivis);
        suiviTableBody.innerHTML = sorted.map((item) => {
            const delta = Number(item.calories_consommees) - Number(currentRegime.calories_cible);
            const deltaClass = delta > 0 ? 'delta-positive' : delta < 0 ? 'delta-negative' : '';
            const deltaLabel = `${delta > 0 ? '+' : ''}${delta} kcal`;

            return `
                <tr>
                    <td>${escapeHtml(formatDate(item.date))}</td>
                    <td>${escapeHtml(item.poids)} kg</td>
                    <td>${escapeHtml(item.calories_consommees)} kcal</td>
                    <td class="${deltaClass}">${escapeHtml(deltaLabel)}</td>
                    <td>
                        <div class="table-actions">
                            <button class="inline-action" type="button" data-edit-id="${item.id_suivi}">Modifier</button>
                            <button class="inline-action is-danger" type="button" data-delete-id="${item.id_suivi}">Supprimer</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function resetFormState() {
        editingSuiviId = null;
        suiviFormTitle.textContent = 'Nouvelle entree';
        suiviSubmitButton.textContent = 'Enregistrer';
        clearFeedback();
        suiviForm.reset();
        document.getElementById('followDate').value = todayValue();
    }

    async function loadSuivisForCurrentRegime() {
        if (!currentRegime) {
            return;
        }

        const data = await request('suivis');
        currentSuivis = data.filter((item) => Number(item.id_regime) === Number(currentRegime.id_regime));
        renderSuiviStats();
        renderSuiviTable();
    }

    async function selectRegime(regimeId) {
        currentRegime = regimes.find((item) => Number(item.id_regime) === Number(regimeId)) || null;

        if (!currentRegime) {
            return;
        }

        renderCurrentRegime();
        suiviFormSection.classList.remove('is-hidden');
        resetFormState();
        await loadSuivisForCurrentRegime();
    }

    searchResults.addEventListener('click', (event) => {
        const button = event.target.closest('[data-select-id]');

        if (!button) {
            return;
        }

        selectRegime(Number(button.dataset.selectId)).catch((error) => {
            showFeedback('error', error.message);
        });
    });

    searchInput.addEventListener('input', (event) => {
        renderSearchResults(event.target.value);
    });

    suiviForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!currentRegime) {
            showFeedback('error', 'Choisissez un regime avant d enregistrer un suivi.');
            return;
        }

        const payload = {
            id_regime: currentRegime.id_regime,
            date: document.getElementById('followDate').value,
            poids: Number(document.getElementById('followWeight').value),
            calories_consommees: Number(document.getElementById('followCalories').value),
        };

        try {
            if (editingSuiviId) {
                await request('editSuivi', 'POST', { ...payload, id_suivi: editingSuiviId });
                showFeedback('success', 'Suivi mis a jour avec succes.');
            } else {
                await request('suivi', 'POST', payload);
                showFeedback('success', 'Suivi ajoute avec succes.');
            }

            resetFormState();
            await loadSuivisForCurrentRegime();
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    suiviForm.addEventListener('reset', () => {
        window.setTimeout(resetFormState, 0);
    });

    suiviTableBody.addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-edit-id]');
        const deleteButton = event.target.closest('[data-delete-id]');

        if (editButton) {
            const suivi = currentSuivis.find((item) => Number(item.id_suivi) === Number(editButton.dataset.editId));

            if (!suivi) {
                return;
            }

            editingSuiviId = Number(suivi.id_suivi);
            suiviFormTitle.textContent = `Modifier le suivi S-${String(suivi.id_suivi).padStart(3, '0')}`;
            suiviSubmitButton.textContent = 'Mettre a jour';
            document.getElementById('followDate').value = suivi.date;
            document.getElementById('followWeight').value = suivi.poids;
            document.getElementById('followCalories').value = suivi.calories_consommees;
            clearFeedback();
            suiviForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            return;
        }

        if (!deleteButton) {
            return;
        }

        if (!window.confirm('Supprimer ce suivi ?')) {
            return;
        }

        try {
            await request('delete', 'POST', { type: 'suivi', id: Number(deleteButton.dataset.deleteId) });
            await loadSuivisForCurrentRegime();
            showFeedback('success', 'Suivi supprime avec succes.');
            if (editingSuiviId === Number(deleteButton.dataset.deleteId)) {
                resetFormState();
            }
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    document.getElementById('followDate').value = todayValue();

    request('regimes')
        .then((data) => {
            regimes = Array.isArray(data) ? data : [];
            renderSearchResults();
        })
        .catch((error) => {
            searchResults.innerHTML = '<div class="empty-state">Impossible de charger les regimes.</div>';
            showFeedback('error', error.message);
        });
})();
