<?php
require_once '../middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPIB - Dashboard Super Admin</title>
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
            <h1>Dashboard Super Admin</h1>
            <div class="actions">
                <button onclick="showAddUserModal()" class="btn btn-success">Ajouter Utilisateur</button>
                <button onclick="logout()" class="btn btn-danger">Déconnexion</button>
            </div>
        </div>

        <div class="user-info" id="userInfo">
            <!-- Les informations de l'utilisateur seront injectées ici -->
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value" id="totalUsers">-</div>
                <div>Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalDepartments">-</div>
                <div>Départements</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalServices">-</div>
                <div>Services</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalInterviews">-</div>
                <div>Entretiens</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h2>Gestion des Utilisateurs</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersList">
                        <!-- Les utilisateurs seront injectés ici -->
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Départements et Services</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Responsable</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="departmentsList">
                        <!-- Les départements seront injectés ici -->
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Activité Récente</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Utilisateur</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody id="activityLog">
                        <!-- Les activités seront injectées ici -->
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
                const response = await fetch('../api/dashboard/admin_stats.php');
                const data = await response.json();

                // Mettre à jour les statistiques
                document.getElementById('totalUsers').textContent = data.totalUsers || '0';
                document.getElementById('totalDepartments').textContent = data.totalDepartments || '0';
                document.getElementById('totalServices').textContent = data.totalServices || '0';
                document.getElementById('totalInterviews').textContent = data.totalInterviews || '0';

                // Mettre à jour la liste des utilisateurs
                const usersHtml = data.users?.map(user => `
                    <tr>
                        <td>${user.matricule}</td>
                        <td>${user.prenom} ${user.nom}</td>
                        <td>${user.role}</td>
                        <td>
                            <button class="btn btn-primary" onclick="editUser(${user.id})">Éditer</button>
                            <button class="btn btn-danger" onclick="deleteUser(${user.id})">Supprimer</button>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="4">Aucun utilisateur trouvé</td></tr>';
                document.getElementById('usersList').innerHTML = usersHtml;

                // Mettre à jour la liste des départements
                const deptsHtml = data.departments?.map(dept => `
                    <tr>
                        <td>${dept.nom}</td>
                        <td>${dept.type}</td>
                        <td>${dept.responsable}</td>
                        <td>
                            <button class="btn btn-primary" onclick="editDepartment(${dept.id})">Éditer</button>
                            <button class="btn btn-danger" onclick="deleteDepartment(${dept.id})">Supprimer</button>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="4">Aucun département trouvé</td></tr>';
                document.getElementById('departmentsList').innerHTML = deptsHtml;

                // Mettre à jour le journal d'activité
                const activityHtml = data.activities?.map(activity => `
                    <tr>
                        <td>${activity.date}</td>
                        <td>${activity.action}</td>
                        <td>${activity.user}</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewActivity(${activity.id})">Voir</button>
                        </td>
                    </tr>
                `).join('') || '<tr><td colspan="4">Aucune activité trouvée</td></tr>';
                document.getElementById('activityLog').innerHTML = activityHtml;

            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
            }
        }

        // Charger les données initiales
        loadDashboardData();

        // Fonctions pour les actions
        function showAddUserModal() {
            // À implémenter : afficher le modal d'ajout d'utilisateur
            console.log('Ajouter utilisateur');
        }

        function editUser(id) {
            // À implémenter : éditer un utilisateur
            console.log('Éditer utilisateur:', id);
        }

        function deleteUser(id) {
            // À implémenter : supprimer un utilisateur
            console.log('Supprimer utilisateur:', id);
        }

        function editDepartment(id) {
            // À implémenter : éditer un département
            console.log('Éditer département:', id);
        }

        function deleteDepartment(id) {
            // À implémenter : supprimer un département
            console.log('Supprimer département:', id);
        }

        function viewActivity(id) {
            // À implémenter : voir les détails d'une activité
            console.log('Voir activité:', id);
        }
    </script>
</body>
</html>
