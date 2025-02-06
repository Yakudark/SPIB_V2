// Fonction pour charger les entretiens (à venir et historique)
async function loadEntretiens() {
    try {
        const selectedAgent = document.getElementById('filterAgent').value;
        
        // Charger les entretiens à venir
        const upcomingResponse = await fetch(`/JS/STIB/api/pm/entretiens.php?type=upcoming${selectedAgent ? '&agent_id=' + selectedAgent : ''}`);
        const upcomingData = await upcomingResponse.json();
        
        const upcomingTableBody = document.getElementById('upcoming-interviews');
        upcomingTableBody.innerHTML = '';
        
        if (upcomingData.success && upcomingData.entretiens.length > 0) {
            upcomingData.entretiens.forEach(entretien => {
                upcomingTableBody.innerHTML += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.date}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.agent}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.type}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.avec}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.commentaire}</td>
                    </tr>
                `;
            });
        } else {
            upcomingTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun entretien à venir</td>
                </tr>
            `;
        }

        // Charger l'historique des entretiens
        const historyResponse = await fetch(`/JS/STIB/api/pm/entretiens.php?type=history${selectedAgent ? '&agent_id=' + selectedAgent : ''}`);
        const historyData = await historyResponse.json();
        
        const historyTableBody = document.getElementById('history-interviews');
        historyTableBody.innerHTML = '';
        
        if (historyData.success && historyData.entretiens.length > 0) {
            historyData.entretiens.forEach(entretien => {
                historyTableBody.innerHTML += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.date}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.agent}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.type}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.avec}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${entretien.commentaire}</td>
                    </tr>
                `;
            });
        } else {
            historyTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun entretien dans l'historique</td>
                </tr>
            `;
        }
        
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// Fonction pour charger la liste des agents dans le filtre
async function loadAgentsForFilter() {
    try {
        const select = document.getElementById('filterAgent');
        if (select) {
            loadAgentsInSelect(select, true, 'Tous les agents');
            // Ajouter l'événement change pour recharger automatiquement les entretiens
            select.addEventListener('change', loadEntretiens);
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// Fonction pour switcher entre les onglets
function switchTab(tab) {
    // Mettre à jour les classes des onglets
    document.getElementById('upcoming-tab').classList.toggle('text-blue-600', tab === 'upcoming');
    document.getElementById('upcoming-tab').classList.toggle('border-blue-600', tab === 'upcoming');
    document.getElementById('history-tab').classList.toggle('text-blue-600', tab === 'history');
    document.getElementById('history-tab').classList.toggle('border-blue-600', tab === 'history');
    
    // Afficher/masquer le contenu approprié
    document.getElementById('upcoming-content').classList.toggle('hidden', tab !== 'upcoming');
    document.getElementById('history-content').classList.toggle('hidden', tab !== 'history');
}

// Ajouter au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadAgentsForFilter();
    loadEntretiens();
});
