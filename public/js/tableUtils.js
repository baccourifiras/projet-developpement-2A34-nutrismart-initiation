/**
 * NutriSmart BackOffice — Table Utilities
 * Handles: column sorting (asc/desc toggle) + live search filtering
 */

// Track sort state per table: { colIndex, direction }
var sortState = {};

/**
 * Filter table rows by a search query (all visible text columns)
 * @param {string} tableId
 * @param {string} query
 */
function filterTable(tableId, query) {
    var table = document.getElementById(tableId);
    if (!table) return;

    var rows = table.tBodies[0].querySelectorAll('tr');
    var q = query.trim().toLowerCase();
    var visible = 0;

    rows.forEach(function (row) {
        // Skip the status-select column text (col 5) to avoid noise
        var cells = Array.from(row.querySelectorAll('td'));
        var text = cells.map(function (td) {
            return td.innerText || td.textContent;
        }).join(' ').toLowerCase();

        if (text.includes(q)) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    updateCount(tableId, visible, rows.length);
}

/**
 * Sort table by column index, toggling asc/desc on each click
 * @param {string} tableId
 * @param {number} colIndex
 */
function sortTable(tableId, colIndex) {
    var table = document.getElementById(tableId);
    if (!table) return;

    var key = tableId + '_' + colIndex;
    var currentDir = (sortState[key] === 'asc') ? 'desc' : 'asc';
    sortState[key] = currentDir;

    // Update header icons
    var headers = table.querySelectorAll('th.sortable');
    headers.forEach(function (th, i) {
        var icon = th.querySelector('.sort-icon');
        if (!icon) return;
        if (i === colIndex) {
            icon.textContent = currentDir === 'asc' ? '↑' : '↓';
            th.classList.add('sorted');
        } else {
            icon.textContent = '↕';
            th.classList.remove('sorted');
        }
    });

    // Collect and sort rows
    var tbody = table.tBodies[0];
    var rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort(function (a, b) {
        var aText = getCellText(a, colIndex);
        var bText = getCellText(b, colIndex);

        // Numeric sort for ID, Quantité, Prix columns
        var aNum = parseFloat(aText.replace(/[^0-9.]/g, ''));
        var bNum = parseFloat(bText.replace(/[^0-9.]/g, ''));

        var result;
        if (!isNaN(aNum) && !isNaN(bNum)) {
            result = aNum - bNum;
        } else {
            result = aText.localeCompare(bText, 'fr');
        }

        return currentDir === 'asc' ? result : -result;
    });

    rows.forEach(function (row) {
        tbody.appendChild(row);
    });
}

/**
 * Get clean text from a cell (ignores badges/buttons inner HTML noise)
 */
function getCellText(row, colIndex) {
    var cell = row.querySelectorAll('td')[colIndex];
    if (!cell) return '';
    return (cell.innerText || cell.textContent).trim().toLowerCase();
}

/**
 * Update the row count label below the table
 */
function updateCount(tableId, visible, total) {
    var countEl = document.getElementById(
        tableId === 'produitTable' ? 'produitCount' : 'commandeCount'
    );
    if (!countEl) return;
    if (visible === total) {
        countEl.textContent = total + ' résultat(s)';
    } else {
        countEl.textContent = visible + ' résultat(s) sur ' + total;
    }
}

/**
 * Init: show total count on page load
 */
function initTableUtils(tableId, countId, type) {
    var table = document.getElementById(tableId);
    if (!table) return;
    var rows = table.tBodies[0].querySelectorAll('tr');
    var countEl = document.getElementById(countId);
    if (countEl) countEl.textContent = rows.length + ' résultat(s)';
}
