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

function loadAgents() {
    fetch('/JS/STIB/api/pm/agents.php')
        .then(response => response.json())
        .then(agents => {
            // Remplir le sélecteur pour les actions
            const selectActions = document.getElementById('selectedAgent');
            selectActions.innerHTML = '<option value="">Tous les salariés</option>';
            
            // Remplir le sélecteur pour les vacances
            const selectVacations = document.getElementById('selectedAgentVacation');
            selectVacations.innerHTML = '<option value="">Tous les salariés</option>';
            
            agents.forEach(agent => {
                // Pour le sélecteur des actions
                const optionActions = document.createElement('option');
                optionActions.value = agent.id;
                optionActions.textContent = `${agent.nom} ${agent.prenom}`;
                selectActions.appendChild(optionActions);

                // Pour le sélecteur des vacances
                const optionVacations = document.createElement('option');
                optionVacations.value = agent.id;
                optionVacations.textContent = `${agent.nom} ${agent.prenom}`;
                selectVacations.appendChild(optionVacations);
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

function loadConges() {
    const selectedAgent = document.getElementById('selectedAgentVacation').value;
    const statusFilter = document.getElementById('statusFilter').value;
    let url = '/JS/STIB/api/pm/conges.php';
    
    // Ajout des paramètres à l'URL
    const params = new URLSearchParams();
    if (selectedAgent) params.append('agent_id', selectedAgent);
    if (statusFilter) params.append('status', statusFilter);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }

    fetch(url)
        .then(response => response.json())
        .then(conges => {
            const tbody = document.querySelector('#demandes-table tbody');
            tbody.innerHTML = '';
            
            conges.forEach(conge => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-3 py-2 whitespace-nowrap">${new Date(conge.date_demande).toLocaleDateString()}</td>
                    <td class="px-3 py-2 whitespace-nowrap">${new Date(conge.date_debut).toLocaleDateString()}</td>
                    <td class="px-3 py-2 whitespace-nowrap">${new Date(conge.date_fin).toLocaleDateString()}</td>
                    <td class="px-3 py-2 whitespace-nowrap">${conge.nb_jours}</td>
                    <td class="px-3 py-2 whitespace-nowrap">${conge.type || 'Congé'}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(conge.statut)}">
                            ${conge.statut}
                        </span>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => console.error('Erreur lors du chargement des congés:', error));
}

function getStatusClass(status) {
    switch(status) {
        case 'En attente':
            return 'bg-yellow-100 text-yellow-800';
        case 'Approuvé':
            return 'bg-green-100 text-green-800';
        case 'Refusé':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
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

// Gestion des absences
async function loadAbsences() {
    try {
        const response = await fetch('/JS/STIB/api/pm/absences.php');
        const data = await response.json();
        
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
                        ${absence.date_fin === '2999-12-31' ? 
                            '<span class="text-red-600">Non définie</span>' : 
                            new Date(absence.date_fin).toLocaleDateString()}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${absence.nombre_jours}
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
    } catch (error) {
        console.error('Erreur:', error);
    }
}

async function submitAbsence(event) {
    event.preventDefault();
    
    const agent_id = document.getElementById('absenceAgent').value;
    const date_debut = document.getElementById('dateDebut').value;
    const date_fin = document.getElementById('dateFin').value;
    const commentaire = document.getElementById('commentaire').value;

    // Si pas de date de fin, demander confirmation
    if (!date_fin) {
        if (!confirm("Aucune date de fin n'a été spécifiée. Voulez-vous en ajouter une ?")) {
            // Si non, continuer avec la date par défaut (31/12/2999)
            try {
                const response = await fetch('/JS/STIB/api/pm/absences.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        agent_id,
                        date_debut,
                        commentaire
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    closeAbsenceModal();
                    loadAbsences();
                } else {
                    alert(data.error || 'Erreur lors de l\'ajout de l\'absence');
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
            return;
        }
        return;
    }

    // Si une date de fin est spécifiée
    try {
        const response = await fetch('/JS/STIB/api/pm/absences.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                agent_id,
                date_debut,
                date_fin,
                commentaire
            })
        });
        
        const data = await response.json();
        if (data.success) {
            closeAbsenceModal();
            loadAbsences();
        } else {
            alert(data.error || 'Erreur lors de l\'ajout de l\'absence');
        }
    } catch (error) {
        console.error('Erreur:', error);
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
    loadDashboardData();
    loadActions();
    loadConges();
    loadAbsences();

    // Ajouter les écouteurs pour les sélecteurs
    const selectedAgent = document.getElementById('selectedAgent');
    if (selectedAgent) {
        selectedAgent.addEventListener('change', loadActions);
    }

    const selectedAgentVacation = document.getElementById('selectedAgentVacation');
    if (selectedAgentVacation) {
        selectedAgentVacation.addEventListener('change', loadConges);
    }
});
