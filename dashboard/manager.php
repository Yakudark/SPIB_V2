<?php
require_once '../middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STIB - Dashboard Manager</title>
    <link href="../public/css/style.css" rel="stylesheet">
    <style>
        .container { padding: 20px; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background: #f8f9fa;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background: #0066cc; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 id="managerDashboardTitle" data-i18n="dashboard">Dashboard Manager</h1>
            <button id="langSwitchBtnManager" class="btn btn-secondary text-sm"><img id="langFlagManager" src="/JS/STIB/public/assets/nl.svg" alt="Changer la langue" style="width:24px;height:16px;vertical-align:middle;"></button>
            <button onclick="logout()" class="btn btn-danger" id="logoutBtnManager" data-i18n="logout">Déconnexion</button>
        </div>

        <div class="user-info" id="userInfo">
            <!-- Les informations de l'utilisateur seront injectées ici -->
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value" id="totalEmployees">-</div>
                <div>Employés</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalInterviews">-</div>
                <div>Entretiens</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="pendingInterviews">-</div>
                <div>En attente</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h2>Entretiens à venir</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="upcomingInterviews">
                        <!-- Les entretiens seront injectés ici -->
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Mes équipes</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pool</th>
                            <th>Manager</th>
                            <th>Effectif</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="teamsList">
                        <!-- Les équipes seront injectées ici -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../public/js/auth.js"></script>
    <script src="/JS/STIB/public/js/lang.js"></script>
    <script>
        // Vérifier l'authentification
        checkAuth();
        
        // Afficher les informations de l'utilisateur
        const user = JSON.parse(localStorage.getItem('user'));
        document.getElementById('userInfo').innerHTML = `
            <h2>Bienvenue ${user.prenom} ${user.nom}</h2>
            <p><strong>Matricule:</strong> ${user.matricule}</p>
            <p><strong>Rôle:</strong> ${user.role}</p>
            <p><strong>Pool:</strong> ${user.pool || 'N/A'}</p>
        `;

        // Fonction pour charger les données du tableau de bord
        async function loadDashboardData() {
            try {
                // Simuler le chargement des données (à remplacer par de vraies requêtes API)
                const response = await fetch('../api/dashboard/manager_stats.php');
                const data = await response.json();

                // Mettre à jour les statistiques
                document.getElementById('totalEmployees').textContent = data.totalEmployees || '0';
                document.getElementById('totalInterviews').textContent = data.totalInterviews || '0';
                document.getElementById('pendingInterviews').textContent = data.pendingInterviews || '0';

                // Mettre à jour la liste des entretiens
                const interviewsHtml = data.upcomingInterviews?.map(interview => `
                    <tr>
                        <td>${interview.date}</td>
                        <td>${interview.employee}</td>
                        <td>${interview.type}</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewInterview(${interview.id})">Voir</button>
                        </td>
                    </tr>
                `).join('') || '<tr><td  colspan="4">Aucun entretien planifié</td></tr>';
                document.getElementById('upcomingInterviews').innerHTML = interviewsHtml;

                // Mettre à jour la liste des équipes
                const teamsHtml = data.teams?.map(team => `
                    <tr>
                        <td>${team.pool}</td>
                        <td>${team.manager}</td>
                        <td>${team.employees}</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewTeam(${team.id})">Détails</button>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="4">Aucune équipe trouvée</td></tr>';
                document.getElementById('teamsList').innerHTML = teamsHtml;

            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
            }
        }

        // Charger les données initiales
        loadDashboardData();

        // Fonctions pour les actions
        function viewInterview(id) {
            // À implémenter : afficher les détails de l'entretien
            console.log('Voir entretien:', id);
        }

        function viewTeam(id) {
            // À implémenter : afficher les détails de l'équipe
            console.log('Voir équipe:', id);
        }

        document.getElementById('langSwitchBtnManager').addEventListener('click', function() {
            currentLang = currentLang === 'fr' ? 'nl' : 'fr';
            updateAllTexts();
            updateLangFlagManager();
        });
        function updateLangFlagManager() {
            const flag = document.getElementById('langFlagManager');
            if (!flag) return;
            flag.src = currentLang === 'fr' ? '/JS/STIB/public/assets/nl.svg' : '/JS/STIB/public/assets/fr.svg';
            flag.alt = currentLang === 'fr' ? 'Néerlandais' : 'Français';
        }
        document.addEventListener('DOMContentLoaded', function() {
            updateAllTexts();
            updateLangFlagManager();
        });
    </script>
</body>
</html>
