(() => {
    const apiUrl = window.API_URL;

    if (!apiUrl) {
        return;
    }

    const adminFeedback = document.getElementById('adminFeedback');
    const adminRegimeForm = document.getElementById('adminRegimeForm');
    const adminSuiviForm = document.getElementById('adminSuiviForm');
    const adminHistoForm = document.getElementById('adminHistoForm');

    let regimes = [];
    let suivis = [];
    let histos = [];

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
        adminFeedback.textContent = message;
        adminFeedback.className = `feedback ${type === 'success' ? 'is-success' : 'is-error'}`;
    }

    function clearFeedback() {
        adminFeedback.textContent = '';
        adminFeedback.className = 'feedback';
    }

    function formatDate(value) {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }).format(new Date(value));
    }

    function getTypeLabel(type) {
        if (type === 'cut') return 'Cut';
        if (type === 'bulk') return 'Bulk';
        return 'Equilibre';
    }

    function getRegimeLabel(idRegime) {
        const regime = regimes.find((item) => Number(item.id_regime) === Number(idRegime));
        return regime ? `R-${String(regime.id_regime).padStart(3, '0')} - ${getTypeLabel(regime.type_regime)}` : '-';
    }

    function populateRegimeSelects() {
        const options = '<option value="">Selectionner</option>' + regimes.map((regime) => (
            `<option value="${regime.id_regime}">R-${String(regime.id_regime).padStart(3, '0')} - ${escapeHtml(getTypeLabel(regime.type_regime))}</option>`
        )).join('');

        document.getElementById('adminSuiviRegime').innerHTML = options;
        document.getElementById('adminHistoRegime').innerHTML = options;
    }

    function renderStats(stats) {
        document.getElementById('adminStatRegimes').textContent = String(stats.regimes || 0);
        document.getElementById('adminStatSuivis').textContent = String(stats.suivis || 0);
        document.getElementById('adminStatHistos').textContent = String(stats.histos || 0);
        document.getElementById('adminStatCalories').textContent = stats.avg_calories || '-';
    }

    function renderRegimeTable() {
        const tbody = document.getElementById('adminRegimeTableBody');

        if (regimes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7">Aucun regime enregistre.</td></tr>';
            return;
        }

        tbody.innerHTML = regimes.map((regime) => `
            <tr>
                <td>R-${String(regime.id_regime).padStart(3, '0')}</td>
                <td><span class="tag tag--${escapeHtml(regime.type_regime)}">${escapeHtml(getTypeLabel(regime.type_regime))}</span></td>
                <td>${escapeHtml(regime.calories_cible)} kcal</td>
                <td>${escapeHtml(formatDate(regime.date_debut))}</td>
                <td>${escapeHtml(regime.poids_initial)} kg</td>
                <td>${escapeHtml(regime.duree)} jours</td>
                <td>
                    <div class="table-actions">
                        <button class="inline-action" type="button" data-regime-edit="${regime.id_regime}">Modifier</button>
                        <button class="inline-action is-danger" type="button" data-regime-delete="${regime.id_regime}">Supprimer</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderSuiviTable() {
        const tbody = document.getElementById('adminSuiviTableBody');

        if (suivis.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">Aucun suivi enregistre.</td></tr>';
            return;
        }

        tbody.innerHTML = suivis.map((suivi) => `
            <tr>
                <td>S-${String(suivi.id_suivi).padStart(3, '0')}</td>
                <td>${escapeHtml(getRegimeLabel(suivi.id_regime))}</td>
                <td>${escapeHtml(formatDate(suivi.date))}</td>
                <td>${escapeHtml(suivi.poids)} kg</td>
                <td>${escapeHtml(suivi.calories_consommees)} kcal</td>
                <td>
                    <div class="table-actions">
                        <button class="inline-action" type="button" data-suivi-edit="${suivi.id_suivi}">Modifier</button>
                        <button class="inline-action is-danger" type="button" data-suivi-delete="${suivi.id_suivi}">Supprimer</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderHistoTable() {
        const tbody = document.getElementById('adminHistoTableBody');

        if (histos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">Aucune recommandation enregistree.</td></tr>';
            return;
        }

        tbody.innerHTML = histos.map((histo) => `
            <tr>
                <td>H-${String(histo.id_historique).padStart(3, '0')}</td>
                <td>${escapeHtml(getRegimeLabel(histo.id_regime))}</td>
                <td>${escapeHtml(histo.recommandation)}</td>
                <td>${escapeHtml(formatDate(histo.date))}</td>
                <td>
                    <div class="table-actions">
                        <button class="inline-action" type="button" data-histo-edit="${histo.id_historique}">Modifier</button>
                        <button class="inline-action is-danger" type="button" data-histo-delete="${histo.id_historique}">Supprimer</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function resetRegimeForm() {
        document.getElementById('adminRegimeId').value = '';
        document.getElementById('adminRegimeTitle').textContent = 'Ajouter un regime';
        adminRegimeForm.reset();
        document.getElementById('adminRegimeDate').value = todayValue();
    }

    function resetSuiviForm() {
        document.getElementById('adminSuiviId').value = '';
        document.getElementById('adminSuiviTitle').textContent = 'Ajouter un suivi';
        adminSuiviForm.reset();
        document.getElementById('adminSuiviDate').value = todayValue();
    }

    function resetHistoForm() {
        document.getElementById('adminHistoId').value = '';
        document.getElementById('adminHistoTitle').textContent = 'Ajouter une recommandation';
        adminHistoForm.reset();
    }

    function fillRegimeForm(regimeId) {
        const regime = regimes.find((item) => Number(item.id_regime) === Number(regimeId));

        if (!regime) {
            return;
        }

        document.getElementById('adminRegimeId').value = String(regime.id_regime);
        document.getElementById('adminRegimeType').value = regime.type_regime;
        document.getElementById('adminRegimeCalories').value = regime.calories_cible;
        document.getElementById('adminRegimeDate').value = regime.date_debut;
        document.getElementById('adminRegimePoids').value = regime.poids_initial;
        document.getElementById('adminRegimeDuree').value = regime.duree;
        document.getElementById('adminRegimeTitle').textContent = `Modifier le regime R-${String(regime.id_regime).padStart(3, '0')}`;
        adminRegimeForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function fillSuiviForm(suiviId) {
        const suivi = suivis.find((item) => Number(item.id_suivi) === Number(suiviId));

        if (!suivi) {
            return;
        }

        document.getElementById('adminSuiviId').value = String(suivi.id_suivi);
        document.getElementById('adminSuiviRegime').value = suivi.id_regime;
        document.getElementById('adminSuiviDate').value = suivi.date;
        document.getElementById('adminSuiviPoids').value = suivi.poids;
        document.getElementById('adminSuiviCalories').value = suivi.calories_consommees;
        document.getElementById('adminSuiviTitle').textContent = `Modifier le suivi S-${String(suivi.id_suivi).padStart(3, '0')}`;
        adminSuiviForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function fillHistoForm(histoId) {
        const histo = histos.find((item) => Number(item.id_historique) === Number(histoId));

        if (!histo) {
            return;
        }

        document.getElementById('adminHistoId').value = String(histo.id_historique);
        document.getElementById('adminHistoRegime').value = histo.id_regime;
        document.getElementById('adminHistoTexte').value = histo.recommandation;
        document.getElementById('adminHistoTitle').textContent = `Modifier la recommandation H-${String(histo.id_historique).padStart(3, '0')}`;
        adminHistoForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    async function loadAll() {
        const [stats, regimeData, suiviData, histoData] = await Promise.all([
            request('stats'),
            request('regimes'),
            request('suivis'),
            request('histos'),
        ]);

        regimes = Array.isArray(regimeData) ? regimeData : [];
        suivis = Array.isArray(suiviData) ? suiviData : [];
        histos = Array.isArray(histoData) ? histoData : [];

        renderStats(stats);
        populateRegimeSelects();
        renderRegimeTable();
        renderSuiviTable();
        renderHistoTable();
    }

    adminRegimeForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const regimeId = document.getElementById('adminRegimeId').value;
        const payload = {
            type_regime: document.getElementById('adminRegimeType').value,
            calories_cible: Number(document.getElementById('adminRegimeCalories').value),
            date_debut: document.getElementById('adminRegimeDate').value,
            poids_initial: Number(document.getElementById('adminRegimePoids').value),
            duree: Number(document.getElementById('adminRegimeDuree').value),
        };

        try {
            await request(regimeId ? 'editRegime' : 'regime', 'POST', regimeId ? { ...payload, id_regime: Number(regimeId) } : payload);
            await loadAll();
            resetRegimeForm();
            showFeedback('success', regimeId ? 'Regime mis a jour.' : 'Regime ajoute.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    adminSuiviForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const suiviId = document.getElementById('adminSuiviId').value;
        const payload = {
            id_regime: Number(document.getElementById('adminSuiviRegime').value),
            date: document.getElementById('adminSuiviDate').value,
            poids: Number(document.getElementById('adminSuiviPoids').value),
            calories_consommees: Number(document.getElementById('adminSuiviCalories').value),
        };

        try {
            await request(suiviId ? 'editSuivi' : 'suivi', 'POST', suiviId ? { ...payload, id_suivi: Number(suiviId) } : payload);
            await loadAll();
            resetSuiviForm();
            showFeedback('success', suiviId ? 'Suivi mis a jour.' : 'Suivi ajoute.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    adminHistoForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const histoId = document.getElementById('adminHistoId').value;
        const payload = {
            id_regime: Number(document.getElementById('adminHistoRegime').value),
            recommandation: document.getElementById('adminHistoTexte').value.trim(),
        };

        try {
            await request(histoId ? 'editHisto' : 'histo', 'POST', histoId ? { ...payload, id_historique: Number(histoId) } : payload);
            await loadAll();
            resetHistoForm();
            showFeedback('success', histoId ? 'Recommandation mise a jour.' : 'Recommandation ajoutee.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    adminRegimeForm.addEventListener('reset', () => window.setTimeout(resetRegimeForm, 0));
    adminSuiviForm.addEventListener('reset', () => window.setTimeout(resetSuiviForm, 0));
    adminHistoForm.addEventListener('reset', () => window.setTimeout(resetHistoForm, 0));

    document.getElementById('adminRegimeTableBody').addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-regime-edit]');
        const deleteButton = event.target.closest('[data-regime-delete]');

        if (editButton) {
            fillRegimeForm(Number(editButton.dataset.regimeEdit));
            clearFeedback();
            return;
        }

        if (!deleteButton) {
            return;
        }

        if (!window.confirm('Supprimer ce regime ?')) {
            return;
        }

        try {
            await request('delete', 'POST', { type: 'regime', id: Number(deleteButton.dataset.regimeDelete) });
            await loadAll();
            showFeedback('success', 'Regime supprime.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    document.getElementById('adminSuiviTableBody').addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-suivi-edit]');
        const deleteButton = event.target.closest('[data-suivi-delete]');

        if (editButton) {
            fillSuiviForm(Number(editButton.dataset.suiviEdit));
            clearFeedback();
            return;
        }

        if (!deleteButton) {
            return;
        }

        if (!window.confirm('Supprimer ce suivi ?')) {
            return;
        }

        try {
            await request('delete', 'POST', { type: 'suivi', id: Number(deleteButton.dataset.suiviDelete) });
            await loadAll();
            showFeedback('success', 'Suivi supprime.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    document.getElementById('adminHistoTableBody').addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-histo-edit]');
        const deleteButton = event.target.closest('[data-histo-delete]');

        if (editButton) {
            fillHistoForm(Number(editButton.dataset.histoEdit));
            clearFeedback();
            return;
        }

        if (!deleteButton) {
            return;
        }

        if (!window.confirm('Supprimer cette recommandation ?')) {
            return;
        }

        try {
            await request('delete', 'POST', { type: 'histo', id: Number(deleteButton.dataset.histoDelete) });
            await loadAll();
            showFeedback('success', 'Recommandation supprimee.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    document.getElementById('resetAllButton').addEventListener('click', async () => {
        if (!window.confirm('Supprimer toutes les donnees ?')) {
            return;
        }

        try {
            await request('reset', 'POST', {});
            await loadAll();
            resetRegimeForm();
            resetSuiviForm();
            resetHistoForm();
            showFeedback('success', 'Toutes les donnees ont ete reinitialisees.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    document.getElementById('adminRegimeDate').value = todayValue();
    document.getElementById('adminSuiviDate').value = todayValue();

    loadAll().catch((error) => {
        showFeedback('error', error.message);
    });
})();
