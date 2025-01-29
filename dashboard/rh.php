<?php
require_once '../middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STIB - Dashboard RH</title>
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
            margin-right: 5px;
        }
        .btn-primary { background: #0066cc; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard RH</h1>
            <div class="actions">
                <button onclick="planifierEntretien()" class="btn btn-success">Planifier Entretien</button>
                <button onclick="logout()" class="btn btn-danger">Déconnexion</button>
            </div>
        </div>

        <div class="user-info" id="userInfo">
            <!-- Les informations de l'utilisateur seront injectées ici -->
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value" id="totalEntretiens">-</div>
                <div>Entretiens Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="entretiensEnAttente">-</div>
                <div>En Attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="entretiensDuJour">-</div>
                <div>Aujourd'hui</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="mesuresRH">-</div>
                <div>Mesures RH</div>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="upcomingInterviews">
                        <!-- Les entretiens seront injectés ici -->
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Mesures RH en cours</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="currentMeasures">
                        <!-- Les mesures seront injectées ici -->
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Appels Bienveillants</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employé</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="welcomeCalls">
                        <!-- Les appels seront injectés ici -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../public/js/auth.js"></script>
    <script>
        // Vérifier l'authentification
        checkAuth();
        
        // Afficher les informations de l'utilisateur
        const user = JSON.parse(localStorage.getItem('user'));
        document.getElementById('userInfo').innerHTML = `
            <h2>Bienvenue ${user.prenom} ${user.nom}</h2>
            <p><strong>Matricule:</strong> ${user.matricule}</p>
            <p><strong>Rôle:</strong> ${user.role}</p>
        `;

        // Fonction pour charger les données du tableau de bord
        async function loadDashboardData() {
            try {
                const response = await fetch('../api/dashboard/rh_stats.php');
                const data = await response.json();

                // Mettre à jour les statistiques
                document.getElementById('totalEntretiens').textContent = data.totalEntretiens || '0';
                document.getElementById('entretiensEnAttente').textContent = data.entretiensEnAttente || '0';
                document.getElementById('entretiensDuJour').textContent = data.entretiensDuJour || '0';
                document.getElementById('mesuresRH').textContent = data.totalMesures || '0';

                // Mettre à jour la liste des entretiens
                const interviewsHtml = data.upcomingInterviews?.map(interview => `
                    <tr>
                        <td>${interview.date}</td>
                        <td>${interview.employee}</td>
                        <td>${interview.type}</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewInterview(${interview.id})">Voir</button>
                            <button class="btn btn-warning" onclick="editInterview(${interview.id})">Modifier</button>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="4">Aucun entretien planifié</td></tr>';
                document.getElementById('upcomingInterviews').innerHTML = interviewsHtml;

                // Mettre à jour la liste des mesures
                const measuresHtml = data.currentMeasures?.map(measure => `
                    <tr>
                        <td>${measure.date}</td>
                        <td>${measure.employee}</td>
                        <td>${measure.type}</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewMeasure(${measure.id})">Voir</button>
                            <button class="btn btn-warning" onclick="updateMeasure(${measure.id})">Mettre à jour</button>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="4">Aucune mesure en cours</td></tr>';
                document.getElementById('currentMeasures').innerHTML = measuresHtml;

                // Mettre à jour la liste des appels
                const callsHtml = data.welcomeCalls?.map(call => `
                    <tr>
                        <td>${call.date}</td>
                        <td>${call.employee}</td>
                        <td>${call.status}</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewCall(${call.id})">Voir</button>
                            <button class="btn btn-success" onclick="markCallDone(${call.id})">Fait</button>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="4">Aucun appel planifié</td></tr>';
                document.getElementById('welcomeCalls').innerHTML = callsHtml;

            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
            }
        }

        // Charger les données initiales
        loadDashboardData();

        // Fonctions pour les actions
        function planifierEntretien() {
            // À implémenter : planifier un nouvel entretien
            console.log('Planifier entretien');
        }

        function viewInterview(id) {
            // À implémenter : voir les détails d'un entretien
            console.log('Voir entretien:', id);
        }

        function editInterview(id) {
            // À implémenter : modifier un entretien
            console.log('Modifier entretien:', id);
        }

        function viewMeasure(id) {
            // À implémenter : voir les détails d'une mesure
            console.log('Voir mesure:', id);
        }

        function updateMeasure(id) {
            // À implémenter : mettre à jour une mesure
            console.log('Mettre à jour mesure:', id);
        }

        function viewCall(id) {
            // À implémenter : voir les détails d'un appel
            console.log('Voir appel:', id);
        }

        function markCallDone(id) {
            // À implémenter : marquer un appel comme effectué
            console.log('Marquer appel comme fait:', id);
        }
    </script>
</body>
</html>
