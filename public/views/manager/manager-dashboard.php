<?php
require_once '../../../middleware/auth.php';
$user = checkRole(['PM', 'EM', 'DM']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STIB - Espace Manager</title>
    <link href="/JS/STIB/public/css/tailwind.min.css" rel="stylesheet">
    <link href="/JS/STIB/public/css/style.css" rel="stylesheet">
    <script src="/JS/STIB/public/lib/draggable.bundle.min.js"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-primary text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="text-xl font-bold">STIB - <?php echo htmlspecialchars($user['role']); ?></div>
                <div class="hidden md:flex space-x-4">
                    <a href="#" class="hover:text-gray-200" data-page="team">Mon équipe</a>
                    <a href="#" class="hover:text-gray-200" data-page="interviews">Entretiens</a>
                    <a href="#" class="hover:text-gray-200" data-page="stats">Statistiques</a>
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
        <!-- Mon équipe -->
        <div id="team" class="page-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Mon équipe</h2>
                <div class="space-x-4">
                    <select id="filterPool" class="input max-w-xs">
                        <option value="">Tous les pools</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="teamMembers"></div>
        </div>

        <!-- Gestion des entretiens -->
        <div id="interviews" class="page-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-6">
                    <div class="card">
                        <h3 class="text-lg font-semibold mb-4">Actions disponibles</h3>
                        <div class="space-y-4">
                            <?php if ($user['role'] === 'PM'): ?>
                            <div class="draggable-card" draggable="true" data-action="entretien">
                                <h4 class="font-semibold">Entretien</h4>
                                <p class="text-sm text-gray-600">Entretien formel</p>
                            </div>
                            <div class="draggable-card" draggable="true" data-action="appel_bienveillant">
                                <h4 class="font-semibold">Appel bienveillant</h4>
                                <p class="text-sm text-gray-600">Prise de nouvelles</p>
                            </div>
                            <?php endif; ?>
                            <?php if (in_array($user['role'], ['EM', 'DM'])): ?>
                            <div class="draggable-card" draggable="true" data-action="entretien_suivi">
                                <h4 class="font-semibold">Entretien de suivi</h4>
                                <p class="text-sm text-gray-600">Point périodique</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="card">
                            <h3 class="text-lg font-semibold mb-4">À planifier</h3>
                            <div class="drop-zone" data-zone="to-schedule"></div>
                        </div>
                        <div class="card">
                            <h3 class="text-lg font-semibold mb-4">En cours</h3>
                            <div class="drop-zone" data-zone="in-progress"></div>
                        </div>
                        <div class="card">
                            <h3 class="text-lg font-semibold mb-4">Terminé</h3>
                            <div class="drop-zone" data-zone="completed"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div id="stats" class="page-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Entretiens du mois</h3>
                    <div id="monthlyStats" class="space-y-4"></div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Taux de réalisation</h3>
                    <div id="completionRate" class="space-y-4"></div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">Actions requises</h3>
                    <div id="requiredActions" class="space-y-4"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Initialisation du drag & drop
        const containers = document.querySelectorAll('.drop-zone');
        if (containers.length > 0) {
            const draggable = new Draggable.Sortable(containers, {
                draggable: '.draggable-card',
                handle: '.draggable-card'
            });

            draggable.on('sortable:stop', async (event) => {
                const action = event.data.dragEvent.source.dataset.action;
                const zone = event.data.newContainer.dataset.zone;
                
                try {
                    const response = await fetch('/JS/STIB/api/interviews', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action,
                            zone,
                            manager_id: <?php echo $user['id']; ?>
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Erreur lors de la création de l\'entretien');
                    }

                    loadInterviews();
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                }
            });
        }

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
                case 'team':
                    await loadTeamMembers();
                    break;
                case 'interviews':
                    await loadInterviews();
                    break;
                case 'stats':
                    await loadStatistics();
                    break;
            }
        }

        // Chargement de l'équipe
        async function loadTeamMembers() {
            try {
                const response = await fetch('/JS/STIB/api/users/team/<?php echo $user['id']; ?>');
                const data = await response.json();
                
                const container = document.getElementById('teamMembers');
                container.innerHTML = data.map(member => `
                    <div class="card">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold">${member.prenom} ${member.nom}</h4>
                                <p class="text-sm text-gray-600">${member.pool || 'Aucun pool'}</p>
                            </div>
                            <div class="space-y-2">
                                <button class="btn btn-primary text-sm w-full" 
                                        onclick="planInterview(${member.id})">
                                    Planifier entretien
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Chargement des statistiques
        async function loadStatistics() {
            try {
                const response = await fetch('/JS/STIB/api/statistics/manager/<?php echo $user['id']; ?>');
                const data = await response.json();
                
                // Mise à jour des statistiques
                document.getElementById('monthlyStats').innerHTML = `
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary">${data.monthly_interviews}</div>
                        <div class="text-sm text-gray-600">Entretiens ce mois</div>
                    </div>
                `;

                document.getElementById('completionRate').innerHTML = `
                    <div class="text-center">
                        <div class="text-3xl font-bold text-success">${data.completion_rate}%</div>
                        <div class="text-sm text-gray-600">Taux de réalisation</div>
                    </div>
                `;

                // Actions requises
                document.getElementById('requiredActions').innerHTML = data.required_actions.map(action => `
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <div>
                            <div class="font-semibold">${action.type}</div>
                            <div class="text-sm text-gray-600">${action.employee}</div>
                        </div>
                        <button class="btn btn-primary text-sm" 
                                onclick="handleAction(${action.id})">
                            Agir
                        </button>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Déconnexion
        document.getElementById('logoutBtn').addEventListener('click', async () => {
            try {
                await fetch('/JS/STIB/api/auth/logout', { method: 'POST' });
                window.location.href = '/JS/STIB/public/login.php';
            } catch (error) {
                console.error('Erreur de déconnexion:', error);
            }
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            showPage('team');
        });
    </script>
</body>
</html>
