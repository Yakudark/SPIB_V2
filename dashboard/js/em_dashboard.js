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
    fetch('/JS/STIB/api/em/action_types.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.querySelector('select[name="action_type"]');
                select.innerHTML = '<option value="">Sélectionner un type</option>';
                data.types.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.id;
                    option.textContent = type.nom;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function loadAgentsForModal() {
    fetch('/JS/STIB/api/em/agents.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const agents = data.agents;
                
                // Mettre à jour le sélecteur dans la modale
                const modalSelect = document.querySelector('#actionModal select[name="agent_id"]');
                if (modalSelect) {
                    modalSelect.innerHTML = '<option value="">Sélectionner un agent</option>';
                    agents.forEach(agent => {
                        const option = document.createElement('option');
                        option.value = agent.id;
                        option.textContent = `${agent.nom} ${agent.prenom} (${agent.matricule})`;
                        modalSelect.appendChild(option);
                    });
                }

                // Mettre à jour le sélecteur dans le tableau des actions
                const actionSelect = document.getElementById('selectedAgent');
                if (actionSelect) {
                    actionSelect.innerHTML = '<option value="">Tous les agents</option>';
                    agents.forEach(agent => {
                        const option = document.createElement('option');
                        option.value = agent.id;
                        option.textContent = `${agent.nom} ${agent.prenom} (${agent.matricule})`;
                        actionSelect.appendChild(option);
                    });
                }

                // Mettre à jour le sélecteur dans les entretiens
                const entretienSelect = document.getElementById('selectedAgentEntretiens');
                if (entretienSelect) {
                    entretienSelect.innerHTML = '<option value="">Tous les agents</option>';
                    agents.forEach(agent => {
                        const option = document.createElement('option');
                        option.value = agent.id;
                        option.textContent = `${agent.nom} ${agent.prenom} (${agent.matricule})`;
                        entretienSelect.appendChild(option);
                    });
                }
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function loadActions() {
    const selectedAgent = document.getElementById('selectedAgent').value;
    let url = '/JS/STIB/api/em/actions.php';
    
    if (selectedAgent) {
        url += `?agent_id=${selectedAgent}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const actionsCount = document.getElementById('actions-count');
                if (actionsCount) {
                    actionsCount.textContent = result.actions.length;
                }

                const tbody = document.getElementById('actions-table').querySelector('tbody');
                tbody.innerHTML = '';
                
                result.actions.forEach(action => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${action.agent_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${action.type_action}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(action.date_action).toLocaleDateString('fr-FR')}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                action.statut === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                            }">
                                ${action.statut === 'completed' ? 'Terminée' : 'En cours'}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="markActionComplete(${action.id})" class="text-indigo-600 hover:text-indigo-900 mr-2" title="Marquer comme terminée">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="deleteAction(${action.id})" class="text-red-600 hover:text-red-900" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function markActionComplete(id) {
    if (confirm('Voulez-vous marquer cette action comme terminée ?')) {
        fetch('/JS/STIB/api/em/complete_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action_id: id })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadActions();
            } else {
                alert(result.error || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    }
}

function deleteAction(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette action ?')) {
        fetch('/JS/STIB/api/em/delete_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action_id: id })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadActions();
            } else {
                alert(result.error || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadAgentsForModal();
    // Gérer la soumission du formulaire d'action
    const actionForm = document.getElementById('actionForm');
    if (actionForm) {
        actionForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                agent_id: this.agent_id.value,
                type_action_id: this.action_type.value,
                date_action: this.date_action.value,
                commentaire: this.commentaire.value
            };

            try {
                const response = await fetch('/JS/STIB/api/em/create_action.php', {
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
                    alert(data.error || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            }
        });
    }

    // Gérer le changement d'agent sélectionné
    const selectedAgent = document.getElementById('selectedAgent');
    if (selectedAgent) {
        selectedAgent.addEventListener('change', loadActions);
    }

    // Charger les données initiales
    loadActions();
});
