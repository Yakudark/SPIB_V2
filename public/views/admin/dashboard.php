<?php
require_once '../../../middleware/auth.php';
$user = checkRole(['super_admin']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPIB - Administration</title>
    <link href="/JS/SPIB/public/css/tailwind.min.css" rel="stylesheet">
    <link href="/JS/SPIB/public/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-primary text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="text-xl font-bold">SPIB - Administration</div>
                <div class="hidden md:flex space-x-6">
                    <a href="#" class="hover:text-gray-200" data-page="dashboard">Tableau de bord</a>
                    <a href="#" class="hover:text-gray-200" data-page="users">Utilisateurs</a>
                    <a href="#" class="hover:text-gray-200" data-page="roles">Rôles & Permissions</a>
                    <a href="#" class="hover:text-gray-200" data-page="settings">Paramètres</a>
                    <a href="#" class="hover:text-gray-200" data-page="logs">Logs</a>
                </div>
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
        <!-- Tableau de bord -->
        <div id="dashboard" class="page-content">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Système</h3>
                    <div id="systemStats" class="space-y-4">
                        <!-- Stats système seront chargées ici -->
                    </div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Utilisateurs actifs</h3>
                    <div id="activeUsers" class="space-y-4">
                        <!-- Utilisateurs actifs seront chargés ici -->
                    </div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Erreurs récentes</h3>
                    <div id="recentErrors" class="space-y-4">
                        <!-- Erreurs récentes seront chargées ici -->
                    </div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Maintenance</h3>
                    <div class="space-y-4">
                        <button class="btn btn-primary w-full" onclick="clearCache()">
                            Vider le cache
                        </button>
                        <button class="btn btn-secondary w-full" onclick="backupDatabase()">
                            Sauvegarder BDD
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestion des utilisateurs -->
        <div id="users" class="page-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestion des utilisateurs</h2>
                <div class="flex space-x-4">
                    <input type="text" id="searchUser" class="input" placeholder="Rechercher...">
                    <button class="btn btn-primary" onclick="openUserModal()">
                        Nouvel utilisateur
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-4 text-left">ID</th>
                            <th class="p-4 text-left">Nom</th>
                            <th class="p-4 text-left">Email</th>
                            <th class="p-4 text-left">Rôle</th>
                            <th class="p-4 text-left">Statut</th>
                            <th class="p-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersList">
                        <!-- Liste des utilisateurs sera chargée ici -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gestion des rôles -->
        <div id="roles" class="page-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Rôles</h3>
                    <div id="rolesList" class="space-y-4">
                        <!-- Liste des rôles sera chargée ici -->
                    </div>
                    <button class="btn btn-primary mt-4" onclick="openRoleModal()">
                        Nouveau rôle
                    </button>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Permissions</h3>
                    <div id="permissionsList" class="space-y-4">
                        <!-- Liste des permissions sera chargée ici -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Paramètres système -->
        <div id="settings" class="page-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Paramètres généraux</h3>
                    <form id="generalSettings" class="space-y-4">
                        <div>
                            <label class="label">Nom de l'application</label>
                            <input type="text" class="input" name="app_name">
                        </div>
                        <div>
                            <label class="label">URL de base</label>
                            <input type="text" class="input" name="base_url">
                        </div>
                        <div>
                            <label class="label">Fuseau horaire</label>
                            <select class="input" name="timezone">
                                <option value="Europe/Paris">Europe/Paris</option>
                                <!-- Autres options -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Sauvegarder
                        </button>
                    </form>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Paramètres email</h3>
                    <form id="emailSettings" class="space-y-4">
                        <div>
                            <label class="label">Serveur SMTP</label>
                            <input type="text" class="input" name="smtp_host">
                        </div>
                        <div>
                            <label class="label">Port SMTP</label>
                            <input type="number" class="input" name="smtp_port">
                        </div>
                        <div>
                            <label class="label">Email expéditeur</label>
                            <input type="email" class="input" name="smtp_from">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Sauvegarder
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Logs système -->
        <div id="logs" class="page-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Logs système</h2>
                <div class="flex space-x-4">
                    <select id="logType" class="input">
                        <option value="all">Tous les logs</option>
                        <option value="error">Erreurs</option>
                        <option value="warning">Avertissements</option>
                        <option value="info">Informations</option>
                    </select>
                    <button class="btn btn-secondary" onclick="downloadLogs()">
                        Télécharger
                    </button>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div id="logsList" class="font-mono text-sm whitespace-pre-wrap">
                    <!-- Logs seront chargés ici -->
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    <div id="userModal" class="modal hidden">
        <!-- Modal content -->
    </div>

    <div id="roleModal" class="modal hidden">
        <!-- Modal content -->
    </div>

    <script>
        // Navigation
        document.querySelectorAll('[data-page]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const pageId = e.target.dataset.page;
                showPage(pageId);
            });
        });

        function showPage(pageId) {
            document.querySelectorAll('.page-content').forEach(page => {
                page.classList.add('hidden');
            });
            document.getElementById(pageId).classList.remove('hidden');
            loadPageData(pageId);
        }

        // Chargement des données
        async function loadPageData(pageId) {
            switch (pageId) {
                case 'dashboard':
                    await Promise.all([
                        loadSystemStats(),
                        loadActiveUsers(),
                        loadRecentErrors()
                    ]);
                    break;
                case 'users':
                    await loadUsers();
                    break;
                case 'roles':
                    await Promise.all([
                        loadRoles(),
                        loadPermissions()
                    ]);
                    break;
                case 'settings':
                    await loadSettings();
                    break;
                case 'logs':
                    await loadLogs();
                    break;
            }
        }

        // Statistiques système
        async function loadSystemStats() {
            try {
                const response = await fetch('/JS/SPIB/api/admin/system-stats');
                const data = await response.json();
                
                document.getElementById('systemStats').innerHTML = `
                    <div class="flex justify-between items-center">
                        <span>CPU</span>
                        <span class="font-semibold">${data.cpu_usage}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Mémoire</span>
                        <span class="font-semibold">${data.memory_usage}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Espace disque</span>
                        <span class="font-semibold">${data.disk_usage}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Temps de réponse</span>
                        <span class="font-semibold">${data.response_time}ms</span>
                    </div>
                `;
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Maintenance système
        async function clearCache() {
            try {
                const response = await fetch('/JS/SPIB/api/admin/clear-cache', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (response.ok) {
                    alert('Cache vidé avec succès');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors du vidage du cache');
            }
        }

        async function backupDatabase() {
            try {
                const response = await fetch('/JS/SPIB/api/admin/backup-db', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (response.ok) {
                    alert('Sauvegarde effectuée avec succès');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la sauvegarde');
            }
        }

        // Gestion des utilisateurs
        async function loadUsers() {
            try {
                const response = await fetch('/JS/SPIB/api/admin/users');
                const users = await response.json();
                
                document.getElementById('usersList').innerHTML = users.map(user => `
                    <tr class="border-t">
                        <td class="p-4">${user.id}</td>
                        <td class="p-4">${user.prenom} ${user.nom}</td>
                        <td class="p-4">${user.email}</td>
                        <td class="p-4">${user.role}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-sm ${user.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${user.active ? 'Actif' : 'Inactif'}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex space-x-2">
                                <button class="btn btn-secondary text-sm" 
                                        onclick="editUser(${user.id})">
                                    Éditer
                                </button>
                                <button class="btn btn-danger text-sm"
                                        onclick="deleteUser(${user.id})">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Déconnexion
        document.getElementById('logoutBtn').addEventListener('click', async () => {
            try {
                await fetch('/JS/SPIB/api/auth/logout', { method: 'POST' });
                window.location.href = '/JS/SPIB/public/login.php';
            } catch (error) {
                console.error('Erreur de déconnexion:', error);
            }
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            showPage('dashboard');
        });
    </script>
</body>
</html>
