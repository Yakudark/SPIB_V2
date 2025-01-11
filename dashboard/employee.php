<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /JS/SPIB/public/views/login.php');
    exit;
}
if ($_SESSION['role'] !== 'salarié') {
    header('Location: /JS/SPIB/public/views/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPIB - Espace Salarié</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-white">
    <!-- Barre latérale -->
    <div class="fixed left-0 top-0 h-full w-60 bg-white p-4 shadow-lg">
        <!-- En-tête avec photo -->
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-600 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold">Mon Profil</h2>
            </div>
        </div>

        <!-- Informations personnelles -->
        <div class="space-y-4 mb-8">
            <div>
                <div class="text-lg font-bold text-center"><?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></div>
                <div class="text-gray-600 text-center"><?php echo htmlspecialchars($_SESSION['matricule']); ?></div>
                <div class="text-gray-600 text-center">Pool Delta 1-2</div>
            </div>
            
            <div>
                <div class="text-gray-600 text-center">Manager</div>
                <div class="font-medium text-center">Appana Devadas</div>
            </div>

            <!-- Statistiques -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center">Jours de congé</div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-3xl font-bold text-green-600" id="conges-count">20</span>
                        <span class="text-sm text-gray-500" id="conges-en-attente">(1 en attente)</span>
                    </div>
                    <button onclick="openVacationModal()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                        +
                    </button>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center">Demandes</div>
                <div class="flex items-center justify-between">
                    <div class="text-3xl font-bold text-blue-600">3</div>
                    <div class="text-sm text-gray-500">En attente</div>
                    <div class="text-sm">
                        <div class="text-green-600">✓ <span id="demandes-approuvees">1</span></div>
                        <div class="text-red-600">✗ <span id="demandes-rejetees">1</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center">Absences & Entretiens</div>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-red-600 text-center" id="absences-count">0</div>
                        <div class="text-sm text-gray-500">absences</div>
                    </div>
                    <div class="text-sm">
                        <div class="mb-1">
                            <i class="fas fa-user text-purple-500"></i>
                            <span class="text-gray-600">PM:</span>
                            <span id="entretiens-pm" class="font-semibold">0</span>
                        </div>
                        <div class="mb-1">
                            <i class="fas fa-user text-blue-500"></i>
                            <span class="text-gray-600">EM:</span>
                            <span id="entretiens-em" class="font-semibold">0</span>
                        </div>
                        <div>
                            <i class="fas fa-user text-green-500"></i>
                            <span class="text-gray-600">DM:</span>
                            <span id="entretiens-dm" class="font-semibold">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons du bas -->
        <div class="space-y-2">
            
            <a href="/JS/SPIB/api/auth/logout.php" class="block w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 border border-red-600 rounded shadow text-center">
                Déconnexion
            </a>
           
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="ml-60">

        <!-- Contenu -->
        <div class="p-8 space-y-6">
            <!-- Mes Prochains Entretiens -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Mes Prochains Entretiens</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avec</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commentaire</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="entretiens-table">
                            <!-- Les données seront insérées ici dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mes Absences -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Mes Absences</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jours passés</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Signalé par</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="absences-table">
                            <!-- Les données seront insérées ici dynamiquement -->
                        </tbody>
                    </table>
                    <div class="flex justify-between items-center mt-4">
                        <button id="prev-page" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Précédent
                        </button>
                        <div id="pagination-info" class="text-sm text-gray-600"></div>
                        <div id="pagination-numbers" class="flex space-x-2"></div>
                        <button id="next-page" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Suivant
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mes Demandes -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Mes Demandes</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date demande</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nb jours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="demandes-table">
                            <!-- Les données seront insérées ici dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de demande de congés -->
    <div id="vacationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold mb-4">Nouvelle demande de congés</h3>
                <form id="vacationForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de début</label>
                        <input type="date" name="start_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                        <input type="date" name="end_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Commentaire</label>
                        <textarea name="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeVacationModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                            Soumettre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour charger les statistiques des absences
        async function loadAbsencesStats() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/absences_stats.php');
                const data = await response.json();
                
                if (data.success) {
                    // Mettre à jour les compteurs d'absences
                    document.getElementById('absences-count').textContent = data.stats.absences.nombre;
                    
                    // Mettre à jour les compteurs d'entretiens
                    document.getElementById('entretiens-pm').textContent = data.stats.entretiens.PM;
                    document.getElementById('entretiens-em').textContent = data.stats.entretiens.EM;
                    document.getElementById('entretiens-dm').textContent = data.stats.entretiens.DM;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour charger les entretiens
        async function loadEntretiens() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/entretiens.php');
                const data = await response.json();
                
                if (data.success) {
                    const entretiensTable = document.getElementById('entretiens-table');
                    entretiensTable.innerHTML = data.entretiens.map(entretien => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${entretien.date}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${entretien.type}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${entretien.avec}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${entretien.commentaire}</td>
                        </tr>
                    `).join('') || `
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">
                                Aucun entretien planifié
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour charger les demandes de congés
        async function loadVacationRequests() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/conges.php');
                const data = await response.json();
                
                if (data.success) {
                    // Mettre à jour le compteur de congés
                    document.getElementById('conges-count').textContent = data.conges.jours_disponibles;
                    document.getElementById('conges-en-attente').textContent = `(${data.conges.demandes_en_attente} en attente)`;
                    
                    // Mettre à jour les compteurs de demandes
                    document.getElementById('demandes-approuvees').textContent = data.demandes.demandes_approuvees;
                    document.getElementById('demandes-rejetees').textContent = data.demandes.demandes_rejetees;
                    
                    // Mettre à jour le tableau des demandes
                    const demandesTable = document.getElementById('demandes-table');
                    demandesTable.innerHTML = data.demandes.liste.map(demande => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${demande.date_demande}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${demande.date_debut}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${demande.date_fin}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${demande.nb_jours}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">Congés</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    ${demande.statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : 
                                    demande.statut === 'approuve' ? 'bg-green-100 text-green-800' : 
                                    'bg-red-100 text-red-800'}">
                                    ${demande.statut === 'en_attente' ? 'En attente' : 
                                      demande.statut === 'approuve' ? 'Approuvé' : 'Refusé'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="showDetails(${demande.id})" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                ${demande.statut === 'en_attente' ? `
                                    <button onclick="deleteRequest(${demande.id})" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('');
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour charger les absences
        async function loadAbsences(page = 1) {
            try {
                const response = await fetch(`/JS/SPIB/api/employee/absences.php?page=${page}`);
                const data = await response.json();
                
                if (data.success) {
                    // Mettre à jour le tableau
                    const tbody = document.getElementById('absences-table');
                    tbody.innerHTML = '';
                    
                    if (data.absences.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucune absence enregistrée
                                </td>
                            </tr>
                        `;
                    } else {
                        data.absences.forEach(absence => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${absence.date_debut}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${absence.date_fin}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${absence.jours_passes} jours</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${absence.signale_par}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${absence.motif}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }

                    // Mettre à jour la pagination
                    const paginationNumbers = document.getElementById('pagination-numbers');
                    const prevButton = document.getElementById('prev-page');
                    const nextButton = document.getElementById('next-page');
                    const paginationInfo = document.getElementById('pagination-info');

                    // Mise à jour des boutons précédent/suivant
                    prevButton.disabled = data.pagination.current_page === 1;
                    nextButton.disabled = data.pagination.current_page === data.pagination.total_pages;
                    
                    prevButton.onclick = () => loadAbsences(data.pagination.current_page - 1);
                    nextButton.onclick = () => loadAbsences(data.pagination.current_page + 1);

                    // Mise à jour des numéros de page
                    paginationNumbers.innerHTML = '';
                    for (let i = 1; i <= data.pagination.total_pages; i++) {
                        const button = document.createElement('button');
                        button.className = `px-3 py-1 rounded ${i === data.pagination.current_page 
                            ? 'bg-blue-500 text-white' 
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}`;
                        button.textContent = i;
                        button.onclick = () => loadAbsences(i);
                        paginationNumbers.appendChild(button);
                    }

                    // Mise à jour de l'information de pagination
                    paginationInfo.textContent = `Page ${data.pagination.current_page} sur ${data.pagination.total_pages} (${data.pagination.total_items} absences)`;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Charger la première page au chargement
        document.addEventListener('DOMContentLoaded', () => {
            loadAbsences(1);
        });

        // Fonction pour charger les entretiens
        async function loadEntretiens() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/entretiens.php');
                const data = await response.json();
                
                if (data.success) {
                    const entretiensTable = document.getElementById('entretiens-table');
                    entretiensTable.innerHTML = data.entretiens.map(entretien => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${entretien.date}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${entretien.type}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${entretien.avec}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${entretien.commentaire}</td>
                        </tr>
                    `).join('') || `
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">
                                Aucun entretien planifié
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour charger les demandes de congés
        async function loadVacationRequests() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/conges.php');
                const data = await response.json();
                
                if (data.success) {
                    // Mettre à jour le compteur de congés
                    document.getElementById('conges-count').textContent = data.conges.jours_disponibles;
                    document.getElementById('conges-en-attente').textContent = `(${data.conges.demandes_en_attente} en attente)`;
                    
                    // Mettre à jour les compteurs de demandes
                    document.getElementById('demandes-approuvees').textContent = data.demandes.demandes_approuvees;
                    document.getElementById('demandes-rejetees').textContent = data.demandes.demandes_rejetees;
                    
                    // Mettre à jour le tableau des demandes
                    const demandesTable = document.getElementById('demandes-table');
                    demandesTable.innerHTML = data.demandes.liste.map(demande => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${demande.date_demande}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${demande.date_debut}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${demande.date_fin}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${demande.nb_jours}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">Congés</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    ${demande.statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : 
                                    demande.statut === 'approuve' ? 'bg-green-100 text-green-800' : 
                                    'bg-red-100 text-red-800'}">
                                    ${demande.statut === 'en_attente' ? 'En attente' : 
                                      demande.statut === 'approuve' ? 'Approuvé' : 'Refusé'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="showDetails(${demande.id})" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                ${demande.statut === 'en_attente' ? `
                                    <button onclick="deleteRequest(${demande.id})" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('');
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonctions pour le modal
        function openVacationModal() {
            document.getElementById('vacationModal').classList.remove('hidden');
        }

        function closeVacationModal() {
            document.getElementById('vacationModal').classList.add('hidden');
        }

        // Gestionnaire de soumission du formulaire
        document.getElementById('vacationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/JS/SPIB/api/employee/conges.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    closeVacationModal();
                    loadVacationRequests();
                } else {
                    alert(data.error || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            }
        });

        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadAbsencesStats();
            loadVacationRequests();
            loadAbsences();
            loadEntretiens();
        });

        // Fonction pour supprimer une demande
        async function deleteRequest(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')) {
                return;
            }
            
            try {
                const response = await fetch('/JS/SPIB/api/employee/conges.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                if (data.success) {
                    loadVacationRequests();
                } else {
                    alert(data.error || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            }
        }

        // Fonction pour afficher les détails d'une demande
        function showDetails(id) {
            alert('Détails de la demande ' + id);
        }
    </script>
</body>
</html>