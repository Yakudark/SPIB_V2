<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$database = new Database();
$pdo = $database->getConnection();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard PM - SPIB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Carte des actions à planifier -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Actions à planifier</h3>
                    <span class="text-2xl font-bold text-blue-600" id="actions-count">0</span>
                </div>
                <p class="text-sm text-gray-600">Actions nécessitant votre attention</p>
            </div>

            <!-- Carte des entretiens du jour -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Entretiens aujourd'hui</h3>
                    <span class="text-2xl font-bold text-green-600" id="today-interviews-count">0</span>
                </div>
                <p class="text-sm text-gray-600">Entretiens prévus pour aujourd'hui</p>
            </div>

            <!-- Carte des agents suivis -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Agents suivis</h3>
                        <p class="text-sm text-gray-500" id="pool-name">Pool: Chargement...</p>
                    </div>
                    <span class="text-2xl font-bold text-purple-600" id="agents-count">0</span>
                </div>
                <p class="text-sm text-gray-600">Nombre total d'agents sous votre responsabilité</p>
            </div>
            
        </div>
<!-- Section des absences -->
<div class="bg-white rounded-lg shadow p-6 mt-8 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Suivi des absences</h3>
        <button onclick="openAbsenceModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
            Ajouter une absence
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date début</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de jours</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="absencesTableBody" class="bg-white divide-y divide-gray-200">
                <!-- Les absences seront ajoutées ici dynamiquement -->
            </tbody>
        </table>
    </div>
</div>
        <!-- Tableau des actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-bold text-gray-800">Actions à suivre</h2>
                    <!-- Sélecteur de salarié -->
                    <select id="selectedAgent" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Tous les salariés</option>
                    </select>
                </div>
                <button onclick="openActionModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Nouvelle action
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="actions-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type d'action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Les données seront insérées ici dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>
    

        <!-- Modal pour les détails de vacances -->
     <!-- Tableau des demandes de vacances -->
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <h3 class="text-lg font-semibold text-gray-700">Listes des demandes de congés</h3>
             
             <!-- Sélecteur de salarié -->
            <select id="selectedAgentVacation" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Tous les salariés</option>
            </select>
            <!-- Filtre de statut -->
        <select id="statusFilter" class="block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="loadConges()">
            <option value="">Tous les statuts</option>
            <option value="en_attente">En attente</option>
            <option value="approuve">Approuvé</option>
            <option value="refuse">Refusé</option>
        </select>
        </div>
        
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="demandes-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date demande</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nb jours</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <!-- Les données seront insérées ici dynamiquement -->
            </tbody>
        </table>
    </div>
    
</div>

</div>
    <!-- Modal pour nouvelle action -->
    <div id="actionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Nouvelle action</h3>
                <form id="actionForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Agent</label>
                        <select name="agent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <!-- Options seront chargées dynamiquement -->
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type d'action</label>
                        <select name="action_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <!-- Options seront chargées dynamiquement -->
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="date_action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Commentaire</label>
                        <textarea name="commentaire" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeActionModal()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                            Annuler
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
<!-- Modal pour ajouter une absence -->
<div id="absenceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Ajouter une absence</h2>
            <form id="absenceForm" onsubmit="submitAbsence(event)">
                <div class="mb-4">
                    <label for="absenceAgent" class="block text-sm font-medium text-gray-700">Agent</label>
                    <select id="absenceAgent" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <!-- Les agents seront ajoutés ici dynamiquement -->
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="dateDebut" class="block text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" id="dateDebut" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                
                <div class="mb-4">
                    <label for="dateFin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="date" id="dateFin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                
                <div class="mb-4">
                    <label for="commentaire" class="block text-sm font-medium text-gray-700">Commentaire</label>
                    <textarea id="commentaire" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAbsenceModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg text-sm">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    <script>
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
                const response = await fetch('/JS/SPIB/api/pm/agents.php');
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
            // Charger les agents et les types d'actions
            loadAgentsForModal();
            loadActionTypes();
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('actionForm').reset();
        }

        function loadActionTypes() {
            fetch('/JS/SPIB/api/pm/action_types.php')
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
            fetch('/JS/SPIB/api/pm/agents.php')
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

        // Gérer la soumission du formulaire
        document.getElementById('actionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                agent_id: this.agent_id.value,
                action_type: this.action_type.value,
                date_action: this.date_action.value,
                commentaire: this.commentaire.value
            };

            try {
                const response = await fetch('/JS/SPIB/api/pm/create_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                
                if (data.success) {
                    closeActionModal();
                    loadActions(); // Recharger la liste des actions
                } else {
                    alert(data.error || 'Erreur lors de la création de l\'action');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la création de l\'action');
            }
        });

        function loadAgents() {
            fetch('/JS/SPIB/api/pm/agents.php')
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
                ? `/JS/SPIB/api/pm/actions.php?agent_id=${selectedAgent}`
                : '/JS/SPIB/api/pm/actions.php';

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
            fetch('/JS/SPIB/api/pm/dashboard_info.php')
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

        // Fonction pour charger les demandes de congés
        function loadConges() {
            const selectedAgent = document.getElementById('selectedAgentVacation').value;
    const statusFilter = document.getElementById('statusFilter').value;
    let url = '/JS/SPIB/api/pm/conges.php';
    
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
                const response = await fetch('/JS/SPIB/api/pm/update_action.php', {
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
                const response = await fetch('/JS/SPIB/api/pm/delete_action.php', {
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

        // Ajouter un écouteur d'événements pour le changement de salarié
        document.getElementById('selectedAgent').addEventListener('change', loadActions);

        // Ajouter l'écouteur d'événements pour le sélecteur de congés
        document.getElementById('selectedAgentVacation').addEventListener('change', loadConges);

        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', () => {
            loadAgents();
            loadDashboardData();
            loadActions();
            loadConges();
        });
        // Fonctions pour les absences
async function loadAbsences() {
    try {
        const response = await fetch('/JS/SPIB/api/pm/absences.php');
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
                const response = await fetch('/JS/SPIB/api/pm/absences.php', {
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
        const response = await fetch('/JS/SPIB/api/pm/absences.php', {
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
            const response = await fetch('/JS/SPIB/api/pm/absences.php', {
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

// Charger les absences au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadAbsences();
});
    </script>
</body>
</html>
