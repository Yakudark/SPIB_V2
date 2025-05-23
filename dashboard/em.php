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

// Compter le nombre total de salariés sous la responsabilité de l'EM (des deux tables)
$query = "
    SELECT COUNT(*) as nb_salaries FROM (
        SELECT id FROM employee WHERE em_id = :em_id
        UNION
        SELECT id FROM utilisateurs WHERE em_id = :em_id AND role = 'salarié'
    ) as total
";
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
<body class="bg-white">
    <!-- Barre latérale -->
    <div class="fixed left-0 top-0 h-full w-60 bg-white p-4 shadow-lg">
        <!-- En-tête avec photo -->
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold">
                <?php echo strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1)); ?>
            </div>
            <div>
                <div class="font-bold" id="emName"><?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></div>
                <div class="text-sm text-gray-600" id="emRole" data-i18n="employeeManager">Employee Manager</div>
            </div>
        </div>
        <button id="langSwitchBtnEM" class="btn btn-secondary text-sm w-full mb-4"><img id="langFlagEM" src="/JS/STIB/public/assets/nl.svg" alt="Changer la langue" style="width:24px;height:16px;vertical-align:middle;"></button>
        <!-- Informations -->
        <div class="space-y-4 mb-8">
            <!-- Actions à planifier -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center" id="actionsToPlan" data-i18n="actionsToPlan">Actions à planifier</div>
                <div class="text-3xl font-bold text-blue-600 text-center" id="actions-count">0</div>
                <div class="text-sm text-gray-500 text-center" id="pending" data-i18n="pending">En attente</div>
            </div>

            <!-- Entretiens aujourd'hui -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center" id="interviewsToday" data-i18n="interviewsToday">Entretiens aujourd'hui</div>
                <div class="text-3xl font-bold text-green-600 text-center" id="today-interviews-count">0</div>
                <div class="text-sm text-gray-500 text-center">À réaliser</div>
            </div>

            <!-- Agents suivis -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center">Agents suivis</div>
                <div class="text-3xl font-bold text-purple-600 text-center"><?php echo $nb_salaries; ?></div>
                <div class="text-sm text-gray-500 text-center">Total</div>
            </div>
        </div>

        <!-- Boutons du bas -->
        <div class="space-y-2">
            <button onclick="openActionModal()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
                <i class="fas fa-plus mr-2"></i>Nouvelle action
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
            <!-- Section des prochains entretiens -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-4">
                        <h2 id="prochEntretien" class="text-xl font-bold text-gray-800" data-i18n="prochEntretien">Prochains entretiens</h2>
                        <select id="selectedAgentEntretiens" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option data-i18n="allAgents" value="">Tous les agents</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <!-- Table des entretiens -->
                </div>
            </div>

            <!-- Section des actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-xl font-bold text-gray-800">Actions à suivre</h2>
                        <select id="selectedAgent" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option data-i18n="allAgents" value="">Tous les agents</option>
                        </select>
                    </div>
                    <button onclick="openActionModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
                        <i class="fas fa-plus mr-2"></i>Nouvelle action
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table id="actions-table" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Les actions seront chargées dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <?php include 'modals/action_modal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        <?php include 'js/em_dashboard.js'; ?>
    </script>
    <script src="/JS/STIB/public/js/lang.js"></script>
    <script>
        function updateLangFlagEM() {
            const flag = document.getElementById('langFlagEM');
            if (!flag) return;
            flag.src = currentLang === 'fr' ? '/JS/STIB/public/assets/nl.svg' : '/JS/STIB/public/assets/fr.svg';
            flag.alt = currentLang === 'fr' ? 'Néerlandais' : 'Français';
        }
        setupLangSwitcher('langSwitchBtnEM', updateLangFlagEM);
        document.addEventListener('DOMContentLoaded', function() {
            updateAllTexts();
            updateLangFlagEM();
        });
    </script>
</body>
</html>
