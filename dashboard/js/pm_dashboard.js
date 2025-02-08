// Déclarer les fonctions au début du script
function openAbsenceModal() {
    document.getElementById('absenceModal').classList.remove('hidden');
    loadAgentsForAbsence();
}

function closeAbsenceModal() {
    document.getElementById('absenceModal').classList.add('hidden');
    document.getElementById('absenceForm').reset();
}

async function loadAgentsForAbsence() {
    try {
        const response = await fetch('/JS/STIB/api/pm/agents.php');
        const agents = await response.json();
        
        const select = document.getElementById('absenceAgent');
        select.innerHTML = '<option value="">Sélectionner un agent</option>';
        
        agents.forEach(agent => {
            const option = document.createElement('option');
            option.value = agent.id;
            option.textContent = `${agent.prenom} ${agent.nom}`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Erreur:', error);
    }
}

function openActionModal() {
    document.getElementById('actionModal').classList.remove('hidden');
    loadAgentsForModal();
    loadActionTypes();
}

function closeActionModal() {
    document.getElementById('actionModal').classList.add('hidden');
    document.getElementById('actionForm').reset();
}

function loadActionTypes() {
    fetch('/JS/STIB/api/pm/action_types.php')
        .then(response => response.json())
        .then(data => {
            const select = document.querySelector('select[name="action_type"]');
            select.innerHTML = '<option value="">Sélectionner un type</option>';
            data.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = type.nom;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur:', error));
}

function loadAgentsForModal() {
    fetch('/JS/STIB/api/pm/agents.php')
        .then(response => response.json())
        .then(agents => {
            const select = document.querySelector('select[name="agent_id"]');
            select.innerHTML = '<option value="">Sélectionner un agent</option>';
            agents.forEach(agent => {
                const option = document.createElement('option');
                option.value = agent.id;
                option.textContent = `${agent.nom} ${agent.prenom}`;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur:', error));
}

// Gérer la soumission du formulaire d'action
document.addEventListener('DOMContentLoaded', function() {
    const actionForm = document.getElementById('actionForm');
    if (actionForm) {
        actionForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                agent_id: this.agent_id.value,
                action_type: this.action_type.value,
                date_action: this.date_action.value,
                commentaire: this.commentaire.value
            };

            try {
                const response = await fetch('/JS/STIB/api/pm/create_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                
                if (data.success) {
                    closeActionModal();
                    loadActions();
                } else {
                    alert(data.error || 'Erreur lors de la création de l\'action');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la création de l\'action');
            }
        });
    }
});

async function submitAction(event) {
    event.preventDefault();
    
    const formData = {
        agent_id: document.getElementById('actionAgent').value,
        action_type: document.getElementById('actionType').value,
        date_action: document.getElementById('actionDate').value,
        commentaire: document.getElementById('actionCommentaire').value
    };

    try {
        const response = await fetch('/JS/STIB/api/pm/create_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        
        if (data.success) {
            closeActionModal();
            loadActions();
        } else {
            alert('Erreur lors de la création de l\'action');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    }
}

function loadAgents() {
    fetch('/JS/STIB/api/pm/agents.php')
        .then(response => response.json())
        .then(agents => {
            // Remplir le sélecteur pour les actions
            const selectActions = document.getElementById('selectedAgent');
            selectActions.innerHTML = '<option value="">Tous les salariés</option>';
            
            agents.forEach(agent => {
                // Pour le sélecteur des actions
                const optionActions = document.createElement('option');
                optionActions.value = agent.id;
                optionActions.textContent = `${agent.nom} ${agent.prenom}`;
                selectActions.appendChild(optionActions);
            });
        })
        .catch(error => console.error('Erreur lors du chargement des agents:', error));
}

function loadActions() {
    const selectedAgent = document.getElementById('selectedAgent').value;
    const url = selectedAgent 
        ? `/JS/STIB/api/pm/actions.php?agent_id=${selectedAgent}`
        : '/JS/STIB/api/pm/actions.php';

    fetch(url)
        .then(response => response.json())
        .then(actions => {
            const tbody = document.querySelector('#actions-table tbody');
            tbody.innerHTML = '';
            
            actions.forEach(action => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">${action.agent_nom} ${action.agent_prenom}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${action.type_action}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${new Date(action.date_action).toLocaleDateString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            ${action.statut === 'effectue' ? 'bg-green-100 text-green-800' : 
                              action.statut === 'planifie' ? 'bg-blue-100 text-blue-800' : 
                              'bg-red-100 text-red-800'}">
                            ${action.statut}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button onclick="showActionDetails(${action.id})" class="text-blue-600 hover:text-blue-800 mr-3">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${action.statut === 'planifie' ? `
                            <button onclick="markActionComplete(${action.id})" class="text-green-600 hover:text-green-800 mr-3">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="deleteAction(${action.id})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Mettre à jour les compteurs
            document.getElementById('actions-count').textContent = actions.filter(a => a.statut === 'planifie').length;
            document.getElementById('today-interviews-count').textContent = actions.filter(a => 
                a.statut === 'planifie' && 
                new Date(a.date_action).toDateString() === new Date().toDateString()
            ).length;
        })
        .catch(error => console.error('Erreur lors du chargement des actions:', error));
}

function loadDashboardData() {
    // Charger le nombre d'agents et les informations du PM
    fetch('/JS/STIB/api/pm/dashboard_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('agents-count').textContent = data.agents_count;
                document.getElementById('pool-name').textContent = 'Pool: ' + (data.pool || 'Non assigné');
            }
        });

    // Charger les actions
    loadActions();
}

async function submitAbsence(event) {
    event.preventDefault();
    
    const agent_id = document.getElementById('absenceAgent').value;
    const date_debut = document.getElementById('dateDebut').value;
    const date_fin = document.getElementById('dateFin').value;
    const commentaire = document.getElementById('commentaire').value;

    try {
        const response = await fetch('/JS/STIB/api/pm/absences.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                agent_id,
                date_debut,
                date_fin: date_fin || null,
                commentaire
            })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            alert(data.error || 'Erreur lors de l\'ajout de l\'absence');
            return;
        }
        
        closeAbsenceModal();
        loadAbsences(document.getElementById('selectedAgent').value);
    } catch (error) {
        console.error('Erreur réseau:', error);
        alert('Une erreur réseau est survenue');
    }
}

function loadAbsences(agentId) {
    const url = agentId 
        ? `/JS/STIB/api/pm/absences.php?agent_id=${agentId}`
        : '/JS/STIB/api/pm/absences.php';

    fetch(url)
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                if (data && data.error) {
                    throw new Error(data.error);
                }
                throw new Error('Erreur lors du chargement des absences');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('absencesTableBody');
                tbody.innerHTML = '';
                
                data.absences.forEach(absence => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${absence.agent_prenom} ${absence.agent_nom}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${new Date(absence.date_debut).toLocaleDateString()}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${absence.date_fin === '2999-12-31' ? 'Non définie' : new Date(absence.date_fin).toLocaleDateString()}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${absence.nombre_jours || ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="font-medium">${absence.periodes_12_mois}</span>
                            <span class="text-xs text-gray-500 ml-1">périodes sur 12 mois</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${absence.commentaire || ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <button onclick="deleteAbsence(${absence.id})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        })
        .catch(error => {
            if (!(error instanceof Error && error.message)) {
                console.error('Erreur réseau:', error);
            }
        });
}

async function markActionComplete(id) {
    if (!confirm('Marquer cette action comme effectuée ?')) {
        return;
    }

    try {
        const response = await fetch('/JS/STIB/api/pm/update_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                statut: 'effectue'
            })
        });

        const result = await response.json();
        
        if (result.success) {
            loadActions();
        } else {
            alert(result.error || 'Erreur lors de la mise à jour');
        }
    } catch (error) {
        alert('Erreur lors de la mise à jour');
    }
}

async function deleteAction(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette action ?')) {
        return;
    }

    try {
        const response = await fetch('/JS/STIB/api/pm/delete_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });

        const result = await response.json();
        
        if (result.success) {
            loadActions();
        } else {
            alert(result.error || 'Erreur lors de la suppression');
        }
    } catch (error) {
        alert('Erreur lors de la suppression');
    }
}

async function deleteAbsence(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette absence ?')) {
        try {
            const response = await fetch('/JS/STIB/api/pm/absences.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            
            const data = await response.json();
            if (data.success) {
                loadAbsences();
            } else {
                alert(data.error || 'Erreur lors de la suppression');
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }
}

// Ajouter les écouteurs d'événements
document.addEventListener('DOMContentLoaded', function() {
    // Charger les données initiales
    loadAgents();
    loadActions();
    loadAbsences();
    loadDashboardData();

    // Récupérer les éléments
    const actionForm = document.getElementById('actionForm');
    const absenceForm = document.getElementById('absenceForm');
    const selectedAgent = document.getElementById('selectedAgent');

    // Gérer le formulaire d'action
    if (actionForm) {
        actionForm.addEventListener('submit', submitAction);
    }

    // Gérer le formulaire d'absence
    if (absenceForm) {
        absenceForm.addEventListener('submit', submitAbsence);
    }

    // Gérer le changement d'agent sélectionné
    if (selectedAgent) {
        selectedAgent.addEventListener('change', function() {
            loadAbsences(this.value);
        });
    }
});
