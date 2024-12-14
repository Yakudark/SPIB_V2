// Fonction pour mettre à jour les statistiques dans l'interface
function updateStats(data) {
    document.getElementById('entretiens-count').textContent = data.stats?.entretiens?.total || '0';
    document.getElementById('conges-count').textContent = data.stats?.conges?.restant || '0';
    document.getElementById('formations-count').textContent = data.stats?.formations?.total || '0';
    document.getElementById('documents-count').textContent = data.stats?.documents?.total || '0';
    document.getElementById('demandes-count').textContent = data.stats?.demandes?.total || '0';
}

// Fonction pour charger les statistiques
async function loadDashboardStats() {
    try {
        const response = await fetch('/JS/SPIB/api/dashboard/employee_stats.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Erreur lors du chargement des statistiques');
        }

        const data = await response.json();
        updateStats(data);
        
        // Charger les données des tableaux
        if (data.entretiens) updateEntretiensTable(data.entretiens);
        if (data.formations) updateFormationsTable(data.formations);
        if (data.demandes) updateDemandesTable(data.demandes);
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// Fonction pour formater une date
function formatDate(dateString) {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Fonction pour créer un bouton d'action
function createActionButton(text, onClick, color = 'blue') {
    const button = document.createElement('button');
    button.textContent = text;
    button.className = `text-${color}-600 hover:text-${color}-800 font-medium text-sm px-2 py-1`;
    button.onclick = onClick;
    return button;
}

// Fonction pour mettre à jour le tableau des entretiens
function updateEntretiensTable(entretiens) {
    const tbody = document.querySelector('#entretiens-table tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    entretiens.forEach(entretien => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';
        
        tr.innerHTML = `
            <td class="px-3 py-2 text-sm">${formatDate(entretien.date)}</td>
            <td class="px-3 py-2 text-sm">${entretien.type}</td>
            <td class="px-3 py-2 text-sm">${entretien.avec}</td>
            <td class="px-3 py-2 text-sm flex gap-2">
                <button class="text-blue-600 hover:text-blue-800">Voir</button>
                <button class="text-red-600 hover:text-red-800">Annuler</button>
            </td>
        `;
        
        tbody.appendChild(tr);
    });
}

// Fonction pour mettre à jour le tableau des formations
function updateFormationsTable(formations) {
    const tbody = document.querySelector('#formations-table tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    formations.forEach(formation => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';
        
        tr.innerHTML = `
            <td class="px-3 py-2 text-sm">${formatDate(formation.date)}</td>
            <td class="px-3 py-2 text-sm">${formation.nom}</td>
            <td class="px-3 py-2 text-sm">
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    ${formation.statut === 'En cours' ? 'bg-yellow-100 text-yellow-800' : 
                    formation.statut === 'Terminée' ? 'bg-green-100 text-green-800' : 
                    'bg-gray-100 text-gray-800'}">
                    ${formation.statut}
                </span>
            </td>
            <td class="px-3 py-2 text-sm flex gap-2">
                <button class="text-blue-600 hover:text-blue-800">Détails</button>
            </td>
        `;
        
        tbody.appendChild(tr);
    });
}

// Fonction pour mettre à jour le tableau des demandes
function updateDemandesTable(demandes) {
    const tbody = document.querySelector('#demandes-table tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    demandes.forEach(demande => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';
        
        tr.innerHTML = `
            <td class="px-3 py-2 text-sm">${formatDate(demande.date)}</td>
            <td class="px-3 py-2 text-sm">${demande.type}</td>
            <td class="px-3 py-2 text-sm">
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    ${demande.statut === 'En attente' ? 'bg-yellow-100 text-yellow-800' : 
                    demande.statut === 'Approuvée' ? 'bg-green-100 text-green-800' : 
                    demande.statut === 'Refusée' ? 'bg-red-100 text-red-800' : 
                    'bg-gray-100 text-gray-800'}">
                    ${demande.statut}
                </span>
            </td>
            <td class="px-3 py-2 text-sm flex gap-2">
                <button class="text-blue-600 hover:text-blue-800">Voir</button>
                ${demande.statut === 'En attente' ? 
                    '<button class="text-red-600 hover:text-red-800">Annuler</button>' : ''}
            </td>
        `;
        
        tbody.appendChild(tr);
    });
}

// Charger les statistiques au chargement de la page
document.addEventListener('DOMContentLoaded', loadDashboardStats);
