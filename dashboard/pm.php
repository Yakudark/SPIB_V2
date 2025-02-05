<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

// Debug des informations de session
error_log("=== Debug Session ===");
error_log("Session ID: " . session_id());
error_log("User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Non défini'));
error_log("Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'Non défini'));
error_log("Matricule: " . (isset($_SESSION['matricule']) ? $_SESSION['matricule'] : 'Non défini'));

// Récupérer la liste des salariés qui dépendent de ce PM
$query = "SELECT id, nom, prenom, matricule FROM utilisateurs WHERE pm_id = :pm_id AND role = 'salarié' ORDER BY nom, prenom";
$stmt = $pdo->prepare($query);
$stmt->execute(['pm_id' => $_SESSION['user_id']]);
$salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug de la requête
error_log("=== Debug Requête ===");
error_log("Requête SQL: " . $query);
error_log("PM ID utilisé: " . $_SESSION['user_id']);
error_log("Nombre de salariés trouvés: " . count($salaries));

// Vérifier tous les salariés qui ont un PM
$query_check = "SELECT u.*, p.matricule as pm_matricule FROM utilisateurs u LEFT JOIN utilisateurs p ON u.pm_id = p.id WHERE u.role = 'salarié'";
$stmt_check = $pdo->prepare($query_check);
$stmt_check->execute();
$all_salaries = $stmt_check->fetchAll(PDO::FETCH_ASSOC);
error_log("=== Debug Tous les Salariés ===");
error_log("Nombre total de salariés: " . count($all_salaries));
foreach ($all_salaries as $s) {
    error_log("Salarié: {$s['matricule']} - PM ID: {$s['pm_id']} - PM Matricule: {$s['pm_matricule']}");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard PM - STIB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-white">
    <!-- Barre latérale -->
    <div class="fixed left-0 top-0 h-full w-60 bg-white p-4 shadow-lg">
        <!-- En-tête avec photo -->
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold">
                <?php echo strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1)); ?>
            </div>
            <div>
                <div class="font-bold"><?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></div>
                <div class="text-sm text-gray-600">Manager</div>
                <div class="text-sm text-gray-500"><?php echo isset($_SESSION['pool']) ? htmlspecialchars($_SESSION['pool']) : 'Non assigné'; ?></div>
            </div>
        </div>

        <!-- Informations -->
        <div class="space-y-4 mb-8">
            <!-- Actions à planifier -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center">Actions à planifier</div>
                <div class="text-3xl font-bold text-blue-600 text-center" id="actions-count">0</div>
                <div class="text-sm text-gray-500 text-center">En attente</div>
            </div>

            <!-- Entretiens aujourd'hui -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center">Entretiens aujourd'hui</div>
                <div class="text-3xl font-bold text-green-600 text-center" id="today-interviews-count">0</div>
                <div class="text-sm text-gray-500 text-center">À réaliser</div>
            </div>

            <!-- Agents suivis -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center">Agents suivis</div>
                <div class="text-3xl font-bold text-purple-600 text-center" id="agents-count">0</div>
                
            </div>
        </div>

        <!-- Boutons du bas -->
        <div class="space-y-2">
            <button onclick="openActionModal()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
                <i class="fas fa-plus mr-2"></i>Nouvelle action
            </button>
            <button onclick="openAbsenceModal()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
                <i class="fas fa-plus mr-2"></i>Ajouter absence
            </button>
            <a href="/JS/STIB/api/auth/logout.php" class="block w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 border border-red-600 rounded shadow text-center">
                Déconnexion
            </a>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="ml-60">
        <!-- Contenu -->
        <div class="p-8 space-y-6">
            <!-- Section des actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-xl font-bold text-gray-800">Actions à suivre</h2>
                        <select id="selectedAgent" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Tous les salariés</option>
                            <?php foreach ($salaries as $salarie): ?>
                                <option value="<?php echo $salarie['id']; ?>">
                                    <?php echo htmlspecialchars($salarie['nom'] . ' ' . $salarie['prenom'] . ' (' . $salarie['matricule'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
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

            <!-- Section des absences -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-700">Suivi des absences</h3>
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

            <!-- Section des congés -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-semibold text-gray-700">Demandes de congés</h3>
                        <select id="selectedAgentVacation" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Tous les salariés</option>
                        </select>
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
    </div>

    <!-- Modals -->
    <?php include 'modals/action_modal.php'; ?>
    <?php include 'modals/absence_modal.php'; ?>

    <script>
        <?php include 'js/pm_dashboard.js'; ?>

        const agents = <?php echo json_encode($salaries); ?>;
        
        function loadAgentsInSelect(selectElement, includeDefaultOption = true, defaultText = 'Tous les salariés') {
            if (selectElement) {
                selectElement.innerHTML = '';
                
                if (includeDefaultOption) {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = defaultText;
                    selectElement.appendChild(defaultOption);
                }
                
                if (agents && Array.isArray(agents)) {
                    agents.forEach(agent => {
                        const option = document.createElement('option');
                        option.value = agent.id;
                        option.textContent = `${agent.nom} ${agent.prenom} (${agent.matricule})`;
                        selectElement.appendChild(option);
                    });
                }
            }
        }

        const originalLoadAgents = loadAgents;
        loadAgents = function() {
            const selectActions = document.getElementById('selectedAgent');
            if (selectActions) {
                loadAgentsInSelect(selectActions);
            }
            
            const selectVacations = document.getElementById('selectedAgentVacation');
            if (selectVacations) {
                loadAgentsInSelect(selectVacations);
            }

            const agentsCount = document.getElementById('agents-count');
            if (agentsCount && agents && Array.isArray(agents)) {
                agentsCount.textContent = agents.length;
            }
        };

        const originalLoadAgentsForAbsence = loadAgentsForAbsence;
        loadAgentsForAbsence = function() {
            const select = document.getElementById('absenceAgent');
            if (select) {
                loadAgentsInSelect(select, true, 'Sélectionner un agent');
            }
        };

        const originalLoadAgentsForModal = loadAgentsForModal;
        loadAgentsForModal = function() {
            const select = document.querySelector('select[name="agent_id"]');
            if (select) {
                loadAgentsInSelect(select, true, 'Sélectionner un agent');
            }
        };

        document.removeEventListener('DOMContentLoaded', originalLoadAgents);
        document.addEventListener('DOMContentLoaded', function() {
            try {
                loadAgents();
                loadActions();
                loadConges();
                loadAbsences();
            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
            }
        });
    </script>
</body>
</html>
