<?php
require_once '../../../middleware/auth.php';
$user = checkRole(['salarié']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STIB - Espace Employé</title>
    <link href="/public/css/tailwind.min.css" rel="stylesheet">
    <link href="/public/css/style.css" rel="stylesheet">
    <!-- Ajouter SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-primary text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="text-xl font-bold">STIB</div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">
                        <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                    </span>
                    <button id="logoutBtn" class="btn btn-secondary text-sm">Déconnexion</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Carte des congés -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Congés</h3>
                    <button onclick="ouvrirPopupConges()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                        Demander
                    </button>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total :</span>
                        <span class="font-medium" id="conges-total">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pris :</span>
                        <span class="font-medium" id="conges-pris">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Restant :</span>
                        <span class="font-medium" id="conges-restant">-</span>
                    </div>
                    <div class="flex justify-between mt-2 pt-2 border-t">
                        <span class="text-gray-600">Demandes en cours :</span>
                        <span class="font-medium" id="demandes-en-cours">-</span>
                    </div>
                </div>
            </div>

            <!-- Carte des entretiens -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Entretiens</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total :</span>
                        <span class="font-medium" id="entretiens-total">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">À venir :</span>
                        <span class="font-medium" id="entretiens-upcoming">-</span>
                    </div>
                </div>
            </div>

            <!-- Carte des formations -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Formations</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">En cours :</span>
                        <span class="font-medium" id="formations-en-cours">-</span>
                    </div>
                </div>
            </div>

            <!-- Carte des documents -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Documents</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total :</span>
                        <span class="font-medium" id="documents-total">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entretiens -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Entretiens à venir</h3>
                <div id="upcoming-interviews" class="space-y-4">
                    <!-- Les entretiens seront ajoutés ici dynamiquement -->
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Historique des entretiens</h3>
                <div id="interview-history" class="space-y-4">
                    <!-- L'historique sera ajouté ici dynamiquement -->
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="/public/js/conges.js"></script>
    <script>
        // Mettre à jour l'affichage des statistiques
        function updateStats(data) {
            // Congés
            document.getElementById('conges-total').textContent = data.stats.conges.total;
            document.getElementById('conges-pris').textContent = data.stats.conges.pris;
            document.getElementById('conges-restant').textContent = data.stats.conges.restant;
            document.getElementById('demandes-en-cours').textContent = data.stats.demandes.en_cours;

            // Entretiens
            document.getElementById('entretiens-total').textContent = data.stats.entretiens.total;
            document.getElementById('entretiens-upcoming').textContent = data.stats.entretiens.upcoming;

            // Formations
            document.getElementById('formations-en-cours').textContent = data.stats.formations.en_cours;

            // Documents
            document.getElementById('documents-total').textContent = data.stats.documents.total;
        }

        // Charger les entretiens à venir
        async function loadUpcomingInterviews() {
            try {
                const response = await fetch('/api/interviews/upcoming.php', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error);

                const container = document.getElementById('upcoming-interviews');
                container.innerHTML = data.interviews.length ? data.interviews.map(interview => `
                    <div class="p-4 border rounded">
                        <div class="font-medium">${interview.type}</div>
                        <div class="text-sm text-gray-600">Date: ${new Date(interview.date_action).toLocaleDateString()}</div>
                    </div>
                `).join('') : '<p class="text-gray-500">Aucun entretien à venir</p>';
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Charger l'historique des entretiens
        async function loadInterviewHistory() {
            try {
                const response = await fetch('/api/interviews/history.php', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error);

                const container = document.getElementById('interview-history');
                container.innerHTML = data.interviews.length ? data.interviews.map(interview => `
                    <div class="p-4 border rounded">
                        <div class="font-medium">${interview.type}</div>
                        <div class="text-sm text-gray-600">Date: ${new Date(interview.date_action).toLocaleDateString()}</div>
                    </div>
                `).join('') : '<p class="text-gray-500">Aucun historique disponible</p>';
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour charger les statistiques
        async function chargerStatistiques() {
            try {
                const response = await fetch('/api/dashboard/employee_stats.php', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Erreur lors du chargement des statistiques');
                updateStats(data);
            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message
                });
            }
        }

        // Gérer la déconnexion
        document.getElementById('logoutBtn').addEventListener('click', () => {
            localStorage.removeItem('token');
            window.location.href = '/public/login.php';
        });

        // Initialiser la page
        document.addEventListener('DOMContentLoaded', () => {
            chargerStatistiques();
            loadUpcomingInterviews();
            loadInterviewHistory();
        });
    </script>
</body>
</html>
