// Fonction pour mettre à jour les statistiques dans l'interface
function updateStats(data) {
    // Mise à jour des compteurs
    document.getElementById('entretiens-count').textContent = data.stats?.entretiens?.total || '0';
    document.getElementById('conges-count').textContent = data.stats?.conges?.restant || '0';
    document.getElementById('formations-count').textContent = data.stats?.formations?.en_cours || '0';
    document.getElementById('documents-count').textContent = data.stats?.documents?.total || '0';
    document.getElementById('demandes-count').textContent = data.stats?.demandes?.en_cours || '0';
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
    button.className = `text-${color}-600 hover:text-${color}-800 font-medium text-sm`;
    button.onclick = onClick;
    return button;
}

// Charger les statistiques au chargement de la page
document.addEventListener('DOMContentLoaded', loadDashboardStats);
