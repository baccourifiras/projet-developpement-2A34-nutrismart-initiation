(() => {
    const apiUrl = window.API_URL;

    if (!apiUrl) {
        return;
    }

    const form = document.getElementById('regimeForm');
    const feedback = document.getElementById('regimeFeedback');
    const title = document.getElementById('regimeFormTitle');
    const submitButton = document.getElementById('regimeSubmitButton');
    const dateInput = document.getElementById('dateDebut');
    const filterContainer = document.getElementById('regimeFilters');
    const listContainer = document.getElementById('regimeList');
    const deleteModal = document.getElementById('deleteModal');
    const deleteCancelButton = document.getElementById('deleteCancelButton');
    const deleteConfirmButton = document.getElementById('deleteConfirmButton');

    let regimes = [];
    let activeFilter = 'tous';
    let editingId = null;
    let deletingId = null;

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
        feedback.textContent = message;
        feedback.className = `feedback ${type === 'success' ? 'is-success' : 'is-error'}`;
    }

    function clearFeedback() {
        feedback.textContent = '';
        feedback.className = 'feedback';
    }

    function formatDate(value) {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }).format(new Date(value));
    }

    function calculateProgress(dateDebut, duree) {
        const start = new Date(dateDebut);
        const now = new Date();
        const days = Math.floor((now - start) / 86400000);
        const progress = Math.round((days / Number(duree || 1)) * 100);

        return Math.max(0, Math.min(progress, 100));
    }

    function getTypeLabel(type) {
        if (type === 'cut') return 'Cut';
        if (type === 'bulk') return 'Bulk';
        return 'Equilibre';
    }

    function renderStats() {
        const totals = regimes.reduce((carry, regime) => {
            carry.total += 1;
            carry[regime.type_regime] += 1;
            return carry;
        }, { total: 0, cut: 0, bulk: 0, equilibre: 0 });

        document.getElementById('statTotal').textContent = String(totals.total);
        document.getElementById('statCut').textContent = String(totals.cut);
        document.getElementById('statBulk').textContent = String(totals.bulk);
        document.getElementById('statEquilibre').textContent = String(totals.equilibre);
    }

    function renderEmptyState(message) {
        listContainer.innerHTML = `<div class="empty-state">${escapeHtml(message)}</div>`;
    }

    function renderList() {
        const visibleItems = activeFilter === 'tous'
            ? regimes
            : regimes.filter((regime) => regime.type_regime === activeFilter);

        if (visibleItems.length === 0) {
            renderEmptyState('Aucun regime ne correspond au filtre actuel.');
            return;
        }

        listContainer.innerHTML = visibleItems.map((regime) => {
            const progress = calculateProgress(regime.date_debut, regime.duree);

            return `
                <article class="regime-card">
                    <div class="regime-card__header">
                        <span class="tag tag--${escapeHtml(regime.type_regime)}">${escapeHtml(getTypeLabel(regime.type_regime))}</span>
                        <span class="regime-card__id">R-${String(regime.id_regime).padStart(3, '0')}</span>
                    </div>
                    <div class="regime-card__meta">
                        <div class="regime-card__row"><span>Calories cible</span><strong>${escapeHtml(regime.calories_cible)} kcal</strong></div>
                        <div class="regime-card__row"><span>Poids initial</span><strong>${escapeHtml(regime.poids_initial)} kg</strong></div>
                        <div class="regime-card__row"><span>Date de debut</span><strong>${escapeHtml(formatDate(regime.date_debut))}</strong></div>
                        <div class="regime-card__row"><span>Duree</span><strong>${escapeHtml(regime.duree)} jours</strong></div>
                    </div>
                    <div class="progress">
                        <div class="progress__label">
                            <span>Progression</span>
                            <span>${progress}%</span>
                        </div>
                        <div class="progress__track">
                            <div class="progress__fill" style="width:${progress}%"></div>
                        </div>
                    </div>
                    <div class="regime-card__footer">
                        <button class="inline-action" type="button" data-action="edit" data-id="${regime.id_regime}">Modifier</button>
                        <button class="inline-action is-danger" type="button" data-action="delete" data-id="${regime.id_regime}">Supprimer</button>
                    </div>
                </article>
            `;
        }).join('');
    }

    function resetFormState() {
        editingId = null;
        title.textContent = 'Nouveau regime';
        submitButton.textContent = 'Enregistrer';
        clearFeedback();
        form.reset();
        dateInput.value = todayValue();
    }

    function openDeleteModal(id) {
        deletingId = id;
        deleteModal.hidden = false;
    }

    function closeDeleteModal() {
        deletingId = null;
        deleteModal.hidden = true;
    }

    function populateForm(regime) {
        editingId = regime.id_regime;
        title.textContent = `Modifier le regime R-${String(regime.id_regime).padStart(3, '0')}`;
        submitButton.textContent = 'Mettre a jour';
        document.getElementById('typeRegime').value = regime.type_regime;
        document.getElementById('caloriesCible').value = regime.calories_cible;
        document.getElementById('dateDebut').value = regime.date_debut;
        document.getElementById('poidsInitial').value = regime.poids_initial;
        document.getElementById('duree').value = regime.duree;
        clearFeedback();
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    async function loadRegimes() {
        regimes = await request('regimes');
        renderStats();
        renderList();
    }

    filterContainer.addEventListener('click', (event) => {
        const button = event.target.closest('[data-filter]');

        if (!button) {
            return;
        }

        activeFilter = button.dataset.filter || 'tous';
        filterContainer.querySelectorAll('[data-filter]').forEach((item) => {
            item.classList.toggle('is-active', item === button);
        });
        renderList();
    });

    listContainer.addEventListener('click', (event) => {
        const button = event.target.closest('[data-action]');

        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        const regime = regimes.find((item) => item.id_regime === id);

        if (!regime) {
            return;
        }

        if (button.dataset.action === 'edit') {
            populateForm(regime);
            return;
        }

        openDeleteModal(id);
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            type_regime: document.getElementById('typeRegime').value,
            calories_cible: Number(document.getElementById('caloriesCible').value),
            date_debut: document.getElementById('dateDebut').value,
            poids_initial: Number(document.getElementById('poidsInitial').value),
            duree: Number(document.getElementById('duree').value),
        };
        const wasEditing = editingId !== null;

        try {
            if (wasEditing) {
                await request('editRegime', 'POST', { ...payload, id_regime: editingId });
            } else {
                await request('regime', 'POST', payload);
            }

            await loadRegimes();
            resetFormState();
            showFeedback('success', wasEditing ? 'Regime mis a jour avec succes.' : 'Regime cree avec succes.');
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    form.addEventListener('reset', () => {
        window.setTimeout(resetFormState, 0);
    });

    deleteCancelButton.addEventListener('click', closeDeleteModal);
    deleteModal.addEventListener('click', (event) => {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });

    deleteConfirmButton.addEventListener('click', async () => {
        if (!deletingId) {
            return;
        }

        try {
            await request('delete', 'POST', { type: 'regime', id: deletingId });
            closeDeleteModal();
            await loadRegimes();
            showFeedback('success', 'Regime supprime avec succes.');

            if (editingId === deletingId) {
                resetFormState();
            }
        } catch (error) {
            showFeedback('error', error.message);
        }
    });

    dateInput.value = todayValue();
    loadRegimes().catch((error) => {
        renderEmptyState('Impossible de charger les regimes.');
        showFeedback('error', error.message);
    });
})();
