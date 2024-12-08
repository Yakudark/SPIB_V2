<?php
require_once '../../../middleware/auth.php';
$user = checkRole(['RH']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPIB - Espace RH</title>
    <link href="/JS/SPIB/public/css/tailwind.min.css" rel="stylesheet">
    <link href="/JS/SPIB/public/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-primary text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="text-xl font-bold">SPIB - Ressources Humaines</div>
                <div class="hidden md:flex space-x-6">
                    <a href="#" class="hover:text-gray-200" data-page="overview">Vue d'ensemble</a>
                    <a href="#" class="hover:text-gray-200" data-page="services">Services</a>
                    <a href="#" class="hover:text-gray-200" data-page="employees">Employés</a>
                    <a href="#" class="hover:text-gray-200" data-page="reports">Rapports</a>
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
        <!-- Vue d'ensemble -->
        <div id="overview" class="page-content">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Statistiques globales</h3>
                    <div id="globalStats" class="grid grid-cols-2 gap-4">
                        <!-- Stats seront chargées ici -->
                    </div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Entretiens en attente</h3>
                    <div id="pendingInterviews" class="space-y-4">
                        <!-- Entretiens seront chargés ici -->
                    </div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Alertes</h3>
                    <div id="alerts" class="space-y-4">
                        <!-- Alertes seront chargées ici -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestion des services -->
        <div id="services" class="page-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestion des services</h2>
                <button class="btn btn-primary" onclick="openServiceModal()">
                    Nouveau service
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="servicesList"></div>
        </div>

        <!-- Gestion des employés -->
        <div id="employees" class="page-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestion des employés</h2>
                <div class="flex space-x-4">
                    <input type="text" id="searchEmployee" class="input" placeholder="Rechercher un employé...">
                    <button class="btn btn-primary" onclick="openEmployeeModal()">
                        Nouvel employé
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-4 text-left">Matricule</th>
                            <th class="p-4 text-left">Nom</th>
                            <th class="p-4 text-left">Service</th>
                            <th class="p-4 text-left">Manager</th>
                            <th class="p-4 text-left">Statut</th>
                            <th class="p-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employeesList">
                        <!-- Liste des employés sera chargée ici -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rapports -->
        <div id="reports" class="page-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Rapports d'entretiens</h3>
                    <div class="space-y-4">
                        <div class="flex space-x-4">
                            <select id="reportType" class="input flex-1">
                                <option value="monthly">Mensuel</option>
                                <option value="quarterly">Trimestriel</option>
                                <option value="yearly">Annuel</option>
                            </select>
                            <button class="btn btn-primary" onclick="generateReport()">
                                Générer
                            </button>
                        </div>
                        <div id="reportPreview"></div>
                    </div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Indicateurs RH</h3>
                    <div id="hrMetrics" class="space-y-4">
                        <!-- Indicateurs seront chargés ici -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    <div id="serviceModal" class="modal hidden">
        <!-- Modal content -->
    </div>

    <div id="employeeModal" class="modal hidden">
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
                case 'overview':
                    await Promise.all([
                        loadGlobalStats(),
                        loadPendingInterviews(),
                        loadAlerts()
                    ]);
                    break;
                case 'services':
                    await loadServices();
                    break;
                case 'employees':
                    await loadEmployees();
                    break;
                case 'reports':
                    await loadHRMetrics();
                    break;
            }
        }

        // Chargement des statistiques globales
        async function loadGlobalStats() {
            try {
                const response = await fetch('/JS/SPIB/api/statistics/global');
                const data = await response.json();
                
                document.getElementById('globalStats').innerHTML = `
                    <div class="text-center p-4 bg-gray-50 rounded">
                        <div class="text-2xl font-bold text-primary">${data.total_employees}</div>
                        <div class="text-sm text-gray-600">Employés</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded">
                        <div class="text-2xl font-bold text-secondary">${data.total_services}</div>
                        <div class="text-sm text-gray-600">Services</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded">
                        <div class="text-2xl font-bold text-success">${data.interviews_this_month}</div>
                        <div class="text-sm text-gray-600">Entretiens ce mois</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded">
                        <div class="text-2xl font-bold text-warning">${data.pending_actions}</div>
                        <div class="text-sm text-gray-600">Actions en attente</div>
                    </div>
                `;
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Gestion des services
        async function loadServices() {
            try {
                const response = await fetch('/JS/SPIB/api/services');
                const services = await response.json();
                
                document.getElementById('servicesList').innerHTML = services.map(service => `
                    <div class="card">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold">${service.nom}</h4>
                                <p class="text-sm text-gray-600">${service.manager_name || 'Pas de manager'}</p>
                                <p class="text-sm text-gray-600">${service.employee_count} employés</p>
                            </div>
                            <div class="space-x-2">
                                <button class="btn btn-secondary text-sm" 
                                        onclick="editService(${service.id})">
                                    Éditer
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Gestion des employés
        async function loadEmployees() {
            try {
                const response = await fetch('/JS/SPIB/api/users');
                const employees = await response.json();
                
                document.getElementById('employeesList').innerHTML = employees.map(employee => `
                    <tr class="border-t">
                        <td class="p-4">${employee.matricule}</td>
                        <td class="p-4">${employee.prenom} ${employee.nom}</td>
                        <td class="p-4">${employee.service}</td>
                        <td class="p-4">${employee.manager}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-sm ${employee.status === 'Actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${employee.status}
                            </span>
                        </td>
                        <td class="p-4">
                            <button class="btn btn-secondary text-sm" 
                                    onclick="editEmployee(${employee.id})">
                                Éditer
                            </button>
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
            showPage('overview');
        });
    </script>
</body>
</html>
