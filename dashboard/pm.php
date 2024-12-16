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

        <!-- Tableau des actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Actions à suivre</h2>
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
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
        }

        function loadActionTypes() {
            fetch('/JS/SPIB/api/pm/action_types.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.querySelector('select[name="action_type"]');
                    select.innerHTML = '';
                    data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.nom;
                        select.appendChild(option);
                    });
                });
        }

        function loadAgents() {
            fetch('/JS/SPIB/api/pm/agents.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.querySelector('select[name="agent_id"]');
                    select.innerHTML = '';
                    data.forEach(agent => {
                        const option = document.createElement('option');
                        option.value = agent.id;
                        option.textContent = `${agent.prenom} ${agent.nom}`;
                        select.appendChild(option);
                    });
                });
        }

        function loadActions() {
            fetch('/JS/SPIB/api/pm/actions.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#actions-table tbody');
                    tbody.innerHTML = '';
                    
                    data.forEach(action => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${action.agent_nom} ${action.agent_prenom}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${action.type_action}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${new Date(action.date_action).toLocaleDateString('fr-FR')}
                            </td>
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
                    document.getElementById('actions-count').textContent = data.filter(a => a.statut === 'planifie').length;
                    document.getElementById('today-interviews-count').textContent = data.filter(a => 
                        a.statut === 'planifie' && 
                        new Date(a.date_action).toDateString() === new Date().toDateString()
                    ).length;
                });
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

        document.getElementById('actionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/JS/SPIB/api/pm/create_action.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    closeActionModal();
                    loadActions();
                    this.reset();
                } else {
                    alert(result.error || 'Erreur lors de la création de l\'action');
                }
            } catch (error) {
                alert('Erreur lors de la création de l\'action');
            }
        });

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

        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', () => {
            loadActionTypes();
            loadAgents();
            loadDashboardData();
        });
    </script>
</body>
</html>
