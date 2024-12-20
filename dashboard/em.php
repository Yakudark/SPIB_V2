<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Vérification supplémentaire du rôle
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'EM') {
    header('Location: /JS/SPIB/public/views/login.php');
    exit;
}

$database = new Database();
$pdo = $database->getConnection();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard EM - SPIB</title>
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

        <!-- Tableau des actions -->
        <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
    <div class="flex items-center space-x-4">
        <h2 class="text-xl font-bold text-gray-800">Actions à suivre</h2>
        <!-- Filtres -->
        <div class="flex space-x-4">
            <select id="selectedPool" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Tous les pools</option>
            </select>
            <select id="selectedAgent" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Tous les salariés</option>
            </select>
        </div>
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

        <!-- Tableau des demandes de vacances -->
        <div class="bg-white rounded-lg shadow p-6 mt-8">
        <div class="flex justify-between items-center mb-6">
    <div class="flex items-center space-x-4">
        <h3 class="text-lg font-semibold text-gray-700">Listes des demandes de congés</h3>
        <!-- Filtres -->
        <div class="flex space-x-4">
            <select id="selectedPoolVacation" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Tous les pools</option>
            </select>
            <select id="selectedAgentVacation" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Tous les salariés</option>
            </select>
            <select id="statusFilter" class="block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="approuve">Approuvé</option>
                <option value="refuse">Refusé</option>
            </select>
        </div>
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
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
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

    <script>
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
            fetch('/JS/SPIB/api/em/action_types.php')
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
            fetch('/JS/SPIB/api/em/agents.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const data = result.agents;
                        const select = document.getElementById('agent_id');
                        select.innerHTML = '<option value="">Sélectionnez un agent</option>';
                        
                        data.forEach(agent => {
                            const option = document.createElement('option');
                            option.value = agent.id;
                            option.textContent = `${agent.prenom} ${agent.nom}`;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour charger les agents dans le sélecteur principal
        function loadAgents() {
            fetch('/JS/SPIB/api/em/agents.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const data = result.agents;
                        const selects = [
                            document.getElementById('selectedAgent'),
                            document.getElementById('selectedAgentVacation')
                        ];
                        
                        selects.forEach(select => {
                            if (select) {
                                select.innerHTML = '<option value="">Tous les salariés</option>';
                                data.forEach(agent => {
                                    const option = document.createElement('option');
                                    option.value = agent.id;
                                    option.textContent = `${agent.prenom} ${agent.nom}`;
                                    select.appendChild(option);
                                });
                            }
                        });
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour charger les actions
        function loadActions() {
    const selectedPool = document.getElementById('selectedPool').value;
    const selectedAgent = document.getElementById('selectedAgent').value;
    let url = '/JS/SPIB/api/em/actions.php';
    
    const params = new URLSearchParams();
    if (selectedPool) params.append('pool', selectedPool);
    if (selectedAgent) params.append('agent_id', selectedAgent);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }

            fetch(url)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const data = result.actions;
                        const tbody = document.querySelector('#actions-table tbody');
                        tbody.innerHTML = '';
                        
                        data.forEach(action => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${action.agent_name}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${action.type_action}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${new Date(action.date_action).toLocaleDateString('fr-FR')}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(action.statut)}">
                                        ${action.statut}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${action.commentaire ? 
                                        `<button onclick="showComment('${action.commentaire}')" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-comment"></i>
                                        </button>` 
                                        : ''}
                                </td>
                            `;
                            tbody.appendChild(row);
                        });

                        // Mettre à jour le compteur
                        document.getElementById('actions-count').textContent = data.length;
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour charger les demandes de congés
        function loadConges() {
            const selectedPool = document.getElementById('selectedPoolVacation').value;
            const selectedAgent = document.getElementById('selectedAgentVacation').value;
            const statusFilter = document.getElementById('statusFilter').value;
            
            const params = new URLSearchParams();
            if (selectedPool) params.append('pool', selectedPool);
            if (selectedAgent) params.append('agent_id', selectedAgent);
            if (statusFilter) params.append('status', statusFilter);
            
            let url = '/JS/SPIB/api/em/conges.php';
            if (params.toString()) {
                url += '?' + params.toString();
            }

            fetch(url)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const conges = result.conges;
                        const tbody = document.querySelector('#demandes-table tbody');
                        tbody.innerHTML = '';
                        
                        conges.forEach(demande => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                    ${new Date(demande.date_demande).toLocaleDateString('fr-FR')}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                    ${new Date(demande.date_debut).toLocaleDateString('fr-FR')}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                    ${new Date(demande.date_fin).toLocaleDateString('fr-FR')}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                    ${demande.nb_jours}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                    Congés
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(demande.statut)}">
                                        ${demande.statut}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                    ${demande.statut === 'en_attente' ? `
                                        <div class="flex space-x-2">
                                            <button onclick="updateCongeStatus('${demande.id}', 'approuve')" 
                                                class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="updateCongeStatus('${demande.id}', 'refuse')" 
                                                class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    ` : ''}
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour mettre à jour le statut d'une demande de congés
        function updateCongeStatus(congeId, newStatus) {
            console.log('Mise à jour du congé:', congeId, 'avec le statut:', newStatus);
            
            fetch('/JS/SPIB/api/em/update_conge.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    conge_id: congeId,
                    status: newStatus
                })
            })
            .then(response => {
                console.log('Réponse reçue:', response);
                return response.json();
            })
            .then(result => {
                console.log('Résultat:', result);
                if (result.success) {
                    // Recharger la liste des congés
                    loadConges();
                } else {
                    alert('Erreur lors de la mise à jour du statut: ' + (result.error || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour du statut');
            });
        }

        function getStatusClass(status) {
            switch (status) {
                case 'en_attente':
                    return 'bg-yellow-100 text-yellow-800';
                case 'approuve':
                    return 'bg-green-100 text-green-800';
                case 'refuse':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function showComment(comment) {
            alert(comment);
        }

        // Gestionnaire de soumission du formulaire d'action
        document.getElementById('actionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                agent_id: formData.get('agent_id'),
                type_action_id: formData.get('action_type'),
                date_action: formData.get('date_action'),
                commentaire: formData.get('commentaire')
            };

            fetch('/JS/SPIB/api/em/create_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    closeActionModal();
                    loadActions();
                    this.reset();
                } else {
                    alert(result.error || 'Erreur lors de la création de l\'action');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la création de l\'action');
            });
        });

        // Event listeners pour les changements d'agent
        document.getElementById('selectedAgent').addEventListener('change', loadActions);
        document.getElementById('selectedAgentVacation').addEventListener('change', loadConges);
        document.getElementById('statusFilter').addEventListener('change', loadConges);

        // Chargement initial
        document.addEventListener('DOMContentLoaded', function() {
            loadPools();
            loadActions();
            loadConges();
        });
        // Fonction pour charger les pools
function loadPools() {
    fetch('/JS/SPIB/api/em/pools.php')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const pools = result.pools;
                const poolSelects = [
                    document.getElementById('selectedPool'),
                    document.getElementById('selectedPoolVacation')
                ];
                
                poolSelects.forEach(select => {
                    if (select) {
                        select.innerHTML = '<option value="">Tous les pools</option>';
                        pools.forEach(pool => {
                            const option = document.createElement('option');
                            option.value = pool.pool;
                            option.textContent = `${pool.pool} - ${pool.pm_name}`;
                            select.appendChild(option);
                        });
                    }
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Fonction pour charger les agents en fonction du pool sélectionné
function loadAgentsByPool(poolSelect, agentSelect) {
    const selectedPool = poolSelect.value;
    let url = '/JS/SPIB/api/em/agents.php';
    
    if (selectedPool) {
        url += `?pool=${encodeURIComponent(selectedPool)}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const agents = result.agents;
                agentSelect.innerHTML = '<option value="">Tous les salariés</option>';
                
                agents.forEach(agent => {
                    const option = document.createElement('option');
                    option.value = agent.id;
                    option.textContent = `${agent.prenom} ${agent.nom}`;
                    agentSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Event listeners pour les changements de pool
document.getElementById('selectedPool').addEventListener('change', function() {
    loadAgentsByPool(this, document.getElementById('selectedAgent'));
    loadActions();
});

document.getElementById('selectedPoolVacation').addEventListener('change', function() {
    loadAgentsByPool(this, document.getElementById('selectedAgentVacation'));
    loadConges();
});
    </script>
</body>
</html>
