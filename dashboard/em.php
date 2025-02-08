<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Vérification supplémentaire du rôle
if (!isset($_SESSION['role']) || strtoupper(trim($_SESSION['role'])) !== 'EM') {
    header('Location: ../public/login.php');
    exit;
}

$database = new Database();
$pdo = $database->getConnection();

// Compter le nombre de salariés sous la responsabilité de l'EM
$query = "SELECT COUNT(*) as nb_salaries 
          FROM utilisateurs 
          WHERE em_id = :em_id 
          AND UPPER(TRIM(role)) IN ('SALARIE', 'SALARIÉ')";
$stmt = $pdo->prepare($query);
$stmt->execute(['em_id' => $_SESSION['user_id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$nb_salaries = $result['nb_salaries'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard EM - STIB</title>
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
                        <div class="text-sm text-gray-500"><?php echo isset($_SESSION['pool']) ? htmlspecialchars($_SESSION['pool']) : 'Non assigné'; ?></div>
                    </div>
                    <span class="text-2xl font-bold text-purple-600"><?php echo $nb_salaries; ?></span>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commentaire</th>
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
        document.addEventListener('DOMContentLoaded', function() {
            const actionForm = document.getElementById('actionForm');
            if (actionForm) {
                actionForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    try {
                        const response = await fetch('/JS/STIB/api/em/actions.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            closeActionModal();
                            loadActions();
                        } else {
                            alert(result.error || 'Une erreur est survenue');
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                    }
                });
            }

            loadPools();
            loadAgents();
            loadActions();
        });

        function loadPools() {
            fetch('/JS/STIB/api/em/pools.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const pools = result.pools;
                        const selectPools = document.getElementById('selectedPool');
                        if (selectPools) {
                            selectPools.innerHTML = '<option value="">Tous les pools</option>';
                            pools.forEach(pool => {
                                selectPools.innerHTML += `<option value="${pool}">${pool}</option>`;
                            });
                        }
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        function loadAgents() {
            fetch('/JS/STIB/api/em/agents.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const agents = result.agents;
                        const selectAgents = document.getElementById('selectedAgent');
                        if (selectAgents) {
                            selectAgents.innerHTML = '<option value="">Tous les salariés</option>';
                            agents.forEach(agent => {
                                selectAgents.innerHTML += `<option value="${agent.id}">${agent.nom} ${agent.prenom}</option>`;
                            });
                        }
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        function loadActions() {
            const selectedPool = document.getElementById('selectedPool').value;
            const selectedAgent = document.getElementById('selectedAgent').value;
            let url = '/JS/STIB/api/em/actions.php';
            
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${action.commentaire || ''}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button onclick="viewComment('${action.commentaire || ''}')" class="text-blue-600 hover:text-blue-900 mr-2">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="deleteAction(${action.id})" class="text-red-600 hover:text-red-900">
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

        function openActionModal() {
            const modal = document.getElementById('actionModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeActionModal() {
            const modal = document.getElementById('actionModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function viewComment(comment) {
            alert(comment);
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
                        loadActions(); // Recharger la liste des actions
                        alert('Action supprimée avec succès');
                    } else {
                        alert(result.error || 'Erreur lors de la suppression de l\'action');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression de l\'action');
                });
            }
        }
    </script>
</body>
</html>
