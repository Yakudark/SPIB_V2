<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SuperAdmin') {
    header('Location: /JS/STIB/public/views/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - STIB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Barre latérale -->
    <div class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg">
        <div class="p-4">
            <div class="flex items-center justify-center mb-8">
                <img src="/JS/STIB/public/assets/img/logo.png" alt="STIB Logo" class="h-12">
            </div>
            <nav class="space-y-2">
                <a href="#" class="flex items-center p-3 text-gray-900 rounded-lg bg-blue-100">
                    <i class="fas fa-users mr-3"></i>
                    <span>Utilisateurs</span>
                </a>
                <!-- Autres liens de navigation -->
            </nav>
        </div>
        <!-- Bouton déconnexion -->
        <div class="absolute bottom-0 w-full p-4">
            <a href="/JS/STIB/api/auth/logout.php" class="flex items-center justify-center p-3 text-red-600 hover:bg-red-50 rounded-lg">
                <i class="fas fa-sign-out-alt mr-3"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="ml-64 p-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Gestion des Utilisateurs</h1>
                <button onclick="openUserModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Ajouter un utilisateur
                </button>
            </div>

            <!-- Modal pour ajouter/modifier un utilisateur -->
            <div id="userModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex min-h-screen items-center justify-center px-4">
                    <!-- Overlay -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>

                    <!-- Modal content -->
                    <div class="relative w-full max-w-4xl rounded-lg bg-white shadow-xl">
                        <div class="flex items-center justify-between rounded-t-lg bg-blue-600 px-6 py-4">
                            <h3 class="text-lg font-medium text-white" id="modalTitle">Ajouter un utilisateur</h3>
                            <button type="button" onclick="closeUserModal()"
                                class="text-gray-200 hover:text-white focus:outline-none">
                                <span class="text-2xl">&times;</span>
                            </button>
                        </div>
                        <form id="userForm" onsubmit="handleUserSubmit(event)">
                            <input type="hidden" id="userId" name="userId">
                            
                            <div class="bg-white px-6 pt-6 pb-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <!-- Première colonne -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nom</label>
                                            <input type="text" id="nom" name="nom" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Prénom</label>
                                            <input type="text" id="prenom" name="prenom" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Matricule</label>
                                            <input type="text" id="matricule" name="matricule" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Rôle</label>
                                            <select id="role" name="role" required onchange="handleRoleChange(this.value)"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">Sélectionner un rôle</option>
                                                <option value="salarié">Salarié</option>
                                                <option value="PM">PM</option>
                                                <option value="EM">EM</option>
                                                <option value="DM">DM</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Deuxième colonne -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Service</label>
                                            <select id="pool" name="pool" onchange="handleServiceChange(this.value)"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">Sélectionner un service</option>
                                                <option value="DELTA">DELTA</option>
                                                <option value="Delta 1-2">Delta 1-2</option>
                                                <option value="Delta 1-3">Delta 1-3</option>
                                                <option value="Delta 1-4">Delta 1-4</option>
                                                <option value="Delta 1-5">Delta 1-5</option>
                                                <option value="Delta 1-6">Delta 1-6</option>
                                                <option value="Delta 1-7">Delta 1-7</option>
                                                <option value="Delta 1-8">Delta 1-8</option>
                                                <option value="Delta 2-1">Delta 2-1</option>
                                                <option value="Delta 2-2">Delta 2-2</option>
                                                <option value="Delta 2-3">Delta 2-3</option>
                                                <option value="Delta 2-4">Delta 2-4</option>
                                                <option value="Delta 2-5">Delta 2-5</option>
                                                <option value="Delta 2-6">Delta 2-6</option>
                                                <option value="BREL">BREL</option>
                                                <option value="Brel 1-1">Brel 1-1</option>
                                                <option value="Brel 1-2">Brel 1-2</option>
                                                <option value="Brel 1-3">Brel 1-3</option>
                                                <option value="Brel 1-4">Brel 1-4</option>
                                                <option value="Brel 1-5">Brel 1-5</option>
                                                <option value="Brel 1-6">Brel 1-6</option>
                                                <option value="Brel 1-7">Brel 1-7</option>
                                                <option value="Brel 1-8">Brel 1-8</option>
                                                <option value="Brel 2-1">Brel 2-1</option>
                                                <option value="Brel 2-2">Brel 2-2</option>
                                                <option value="Brel 2-3">Brel 2-3</option>
                                                <option value="Brel 2-4">Brel 2-4</option>
                                                <option value="Brel 2-5">Brel 2-5</option>
                                                <option value="Brel 2-6">Brel 2-6</option>
                                                <option value="Brel 2-7">Brel 2-7</option>
                                                <option value="Brel 2-8">Brel 2-8</option>
                                                <option value="Brel 2-9">Brel 2-9</option>
                                                <option value="HAREM">HAREM</option>
                                                <option value="Ha 1-1">Ha 1-1</option>
                                                <option value="Ha 1-2">Ha 1-2</option>
                                                <option value="Ha 1-3">Ha 1-3</option>
                                                <option value="Ha 1-4">Ha 1-4</option>
                                                <option value="Ha 1-5">Ha 1-5</option>
                                                <option value="Ha 1-6">Ha 1-6</option>
                                                <option value="Ha 2-1">Ha 2-1</option>
                                                <option value="Ha 2-2">Ha 2-2</option>
                                                <option value="Ha 2-3">Ha 2-3</option>
                                                <option value="Ha 2-4">Ha 2-4</option>
                                                <option value="Ha 2-5">Ha 2-5</option>
                                                <option value="Ha 2-6">Ha 2-6</option>
                                                <option value="Ha 2-7">Ha 2-7</option>
                                                <option value="Ha 3-1">Ha 3-1</option>
                                                <option value="Ha 3-2">Ha 3-2</option>
                                                <option value="Ha 3-3">Ha 3-3</option>
                                                <option value="Ha 3-4">Ha 3-4</option>
                                                <option value="Ha 3-6">Ha 3-6</option>
                                                <option value="PIKE">PIKE</option>
                                                <option value="Pike 1-1">Pike 1-1</option>
                                                <option value="Pike 1-3">Pike 1-3</option>
                                                <option value="Pike 1-4">Pike 1-4</option>
                                                <option value="Pike 1-5">Pike 1-5</option>
                                                <option value="Pike 1-6">Pike 1-6</option>
                                                <option value="Pike 1-7">Pike 1-7</option>
                                                <option value="Pike 2-1">Pike 2-1</option>
                                                <option value="Pike 2-2">Pike 2-2</option>
                                                <option value="Pike 2-3">Pike 2-3</option>
                                                <option value="Pike 2-4">Pike 2-4</option>
                                                <option value="Pike 2-5">Pike 2-5</option>
                                                <option value="Pike 2-6">Pike 2-6</option>
                                                <option value="Pike 2-7">Pike 2-7</option>
                                                <option value="MARLY">MARLY</option>
                                                <option value="Marly 1-1">Marly 1-1</option>
                                                <option value="Marly 1-2">Marly 1-2</option>
                                                <option value="Marly 1-3">Marly 1-3</option>
                                                <option value="Marly 1-4">Marly 1-4</option>
                                                <option value="Marly 1-5">Marly 1-5</option>
                                                <option value="Marly 1-6">Marly 1-6</option>
                                                <option value="Marly 1-7">Marly 1-7</option>
                                                <option value="Marly 1-8">Marly 1-8</option>
                                                <option value="Marly 1-9">Marly 1-9</option>
                                                <option value="Marly 1-10">Marly 1-10</option>
                                                <option value="Marly 1-11">Marly 1-11</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">PM</label>
                                            <select id="pm_id" name="pm_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">--</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">EM</label>
                                            <select id="em_id" name="em_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">--</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">DM</label>
                                            <select id="dm_id" name="dm_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">--</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                <button type="button" onclick="closeUserModal()"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tableau des utilisateurs -->
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prénom
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Matricule
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rôle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        <!-- Les données seront insérées ici par JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button id="prev-page-mobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Précédent
                    </button>
                    <button id="next-page-mobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Suivant
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium" id="start-item">-</span> à <span class="font-medium" id="end-item">-</span> sur <span class="font-medium" id="total-items">-</span> utilisateurs
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination" id="pagination-container">
                            <!-- Les boutons de pagination seront insérés ici dynamiquement -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let totalPages = 1;

        // Charger les utilisateurs au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
        });

        // Fonction pour charger les utilisateurs
        async function loadUsers(page = 1) {
            try {
                const response = await fetch(`/JS/STIB/api/admin/users.php?page=${page}`);
                const data = await response.json();
                
                if (data.success) {
                    displayUsers(data.users);
                    updatePagination(data.pagination);
                    currentPage = data.pagination.current_page;
                    totalPages = data.pagination.total_pages;
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur lors du chargement des utilisateurs', 'error');
            }
        }

        // Fonction pour afficher les utilisateurs
        function displayUsers(users) {
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '';

            users.forEach(user => {
                const tr = document.createElement('tr');
                tr.classList.add('hover:bg-gray-50');
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${user.nom}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${user.prenom}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${user.matricule}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${user.pool || '-'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${user.role}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="openUserModal(${user.id})" class="text-indigo-600 hover:text-indigo-900 mr-4">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Fonction pour mettre à jour la pagination
        function updatePagination(pagination) {
            const container = document.getElementById('pagination-container');
            const startItem = document.getElementById('start-item');
            const endItem = document.getElementById('end-item');
            const totalItems = document.getElementById('total-items');
            
            // Mettre à jour les informations de pagination
            const start = (pagination.current_page - 1) * pagination.items_per_page + 1;
            const end = Math.min(start + pagination.items_per_page - 1, pagination.total_items);
            
            startItem.textContent = start;
            endItem.textContent = end;
            totalItems.textContent = pagination.total_items;

            // Générer les boutons de pagination
            let html = `
                <button onclick="loadUsers(${pagination.current_page - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" ${pagination.current_page === 1 ? 'disabled' : ''}>
                    <span class="sr-only">Précédent</span>
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;

            // Ajouter les numéros de page
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === pagination.current_page) {
                    html += `
                        <button aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            ${i}
                        </button>
                    `;
                } else {
                    html += `
                        <button onclick="loadUsers(${i})" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            ${i}
                        </button>
                    `;
                }
            }

            html += `
                <button onclick="loadUsers(${pagination.current_page + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}>
                    <span class="sr-only">Suivant</span>
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;

            container.innerHTML = html;

            // Mettre à jour les boutons mobiles
            document.getElementById('prev-page-mobile').onclick = () => loadUsers(pagination.current_page - 1);
            document.getElementById('next-page-mobile').onclick = () => loadUsers(pagination.current_page + 1);
            document.getElementById('prev-page-mobile').disabled = pagination.current_page === 1;
            document.getElementById('next-page-mobile').disabled = pagination.current_page === pagination.total_pages;
        }

        // Fonction pour ouvrir le modal
        async function openUserModal(userId = null) {
            const modal = document.getElementById('userModal');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('userForm');
            
            modalTitle.textContent = userId ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur';
            form.reset();
            
            if (userId) {
                try {
                    const response = await fetch(`/JS/STIB/api/admin/user_operations.php?id=${userId}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        const user = data.user;
                        fillUserModal(user);
                    } else {
                        showNotification('Erreur lors de la récupération des données de l\'utilisateur', 'error');
                        return;
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showNotification('Erreur lors de la récupération des données de l\'utilisateur', 'error');
                    return;
                }
            }
            
            modal.classList.remove('hidden');
        }

        // Fonction pour remplir le modal avec les données de l'utilisateur
        async function fillUserModal(userData) {
            document.getElementById('userId').value = userData.id;
            document.getElementById('nom').value = userData.nom;
            document.getElementById('prenom').value = userData.prenom;
            document.getElementById('matricule').value = userData.matricule;
            document.getElementById('role').value = userData.role;
            document.getElementById('pool').value = userData.pool || '';

            try {
                // Récupérer tous les utilisateurs pour les listes de managers
                const response = await fetch('/JS/STIB/api/admin/list_users.php');
                const result = await response.json();
                
                if (result.success) {
                    // Mettre à jour les selects des managers avec la liste complète des utilisateurs
                    updateManagerSelects(result.users);
                    
                    // Sélectionner les managers actuels
                    if (userData.pm_id) document.getElementById('pm_id').value = userData.pm_id;
                    if (userData.em_id) document.getElementById('em_id').value = userData.em_id;
                    if (userData.dm_id) document.getElementById('dm_id').value = userData.dm_id;
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des managers:', error);
            }
        }

        // Fonction pour fermer le modal
        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.getElementById('userForm').reset();
        }

        // Fonction pour gérer la soumission du formulaire
        async function handleUserSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const data = {};
            
            // Convertir FormData en objet simple
            for (let [key, value] of formData.entries()) {
                // Convertir les chaînes vides en null pour les champs optionnels
                if (key === 'userId' && value === '') {
                    continue; // Ne pas inclure userId s'il est vide (cas d'un nouvel utilisateur)
                }
                data[key] = value === '' ? null : value;
            }

            // Si c'est une modification, utiliser l'ID de l'utilisateur
            const userId = document.getElementById('userId').value;
            if (userId) {
                data.id = userId;
            }
            
            try {
                const method = userId ? 'PUT' : 'POST';
                console.log('Données envoyées au serveur:', data);
                
                const response = await fetch('/JS/STIB/api/admin/user_operations.php', {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("La réponse n'est pas au format JSON");
                }
                
                const result = await response.json();
                console.log('Réponse du serveur:', result);
                
                if (result.success) {
                    closeUserModal();
                    loadUsers(currentPage);
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.error || 'Une erreur est survenue', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue lors de la communication avec le serveur', 'error');
            }
        }

        // Fonction pour supprimer un utilisateur
        async function deleteUser(userId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                return;
            }
            
            try {
                const response = await fetch(`/JS/STIB/api/admin/user_operations.php?id=${userId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                if (result.success) {
                    loadUsers(currentPage);
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.error, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'error');
            }
        }

        // Fonction pour afficher une notification
        function showNotification(message, type = 'success') {
            const notif = document.createElement('div');
            notif.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notif.textContent = message;
            document.body.appendChild(notif);
            
            setTimeout(() => {
                notif.remove();
            }, 3000);
        }

        // Fonction pour charger les managers (PM, EM, DM)
        async function loadManagers() {
            try {
                const response = await fetch('/JS/STIB/api/admin/users.php');
                const data = await response.json();
                if (data.success) {
                    updateManagerSelects(data.users);
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Mettre à jour les selects des managers en fonction du rôle
        function updateManagerSelects(users) {
            if (!Array.isArray(users)) {
                console.error('La liste des utilisateurs n\'est pas un tableau');
                return;
            }

            const pmSelect = document.getElementById('pm_id');
            const emSelect = document.getElementById('em_id');
            const dmSelect = document.getElementById('dm_id');

            // Réinitialiser les selects
            pmSelect.innerHTML = '<option value="">--</option>';
            emSelect.innerHTML = '<option value="">--</option>';
            dmSelect.innerHTML = '<option value="">--</option>';

            // Filtrer les managers par rôle
            const pms = users.filter(m => m.role === 'PM');
            const ems = users.filter(m => m.role === 'EM');
            const dms = users.filter(m => m.role === 'DM');

            // Remplir les selects avec les managers correspondants
            pms.forEach(pm => {
                const option = document.createElement('option');
                option.value = pm.id;
                option.textContent = `${pm.prenom} ${pm.nom}`;
                pmSelect.appendChild(option);
            });

            ems.forEach(em => {
                const option = document.createElement('option');
                option.value = em.id;
                option.textContent = `${em.prenom} ${em.nom}`;
                emSelect.appendChild(option);
            });

            dms.forEach(dm => {
                const option = document.createElement('option');
                option.value = dm.id;
                option.textContent = `${dm.prenom} ${dm.nom}`;
                dmSelect.appendChild(option);
            });
        }

        // Fonction pour charger les services
        async function loadServices() {
            try {
                const response = await fetch('/JS/STIB/api/admin/services.php');
                const data = await response.json();
                if (data.success) {
                    updateServiceSelect(data.services);
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Mettre à jour le select des services
        function updateServiceSelect(services) {
            const select = document.getElementById('pool');
            select.innerHTML = '<option value="">Sélectionner un service</option>';
            services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.pool;
                option.textContent = service.pool;
                select.appendChild(option);
            });
        }

        // Fonction pour gérer le changement de service
        async function handleServiceChange(service) {
            if (!service) return;

            try {
                const response = await fetch(`/JS/STIB/api/admin/service_managers.php?service=${encodeURIComponent(service)}`);
                const data = await response.json();
                
                if (data.success && data.managers) {
                    // Mettre à jour les selects des managers
                    const pmSelect = document.getElementById('pm_id');
                    const emSelect = document.getElementById('em_id');
                    const dmSelect = document.getElementById('dm_id');

                    // Réinitialiser les selects
                    pmSelect.innerHTML = '<option value="">--</option>';
                    emSelect.innerHTML = '<option value="">--</option>';
                    dmSelect.innerHTML = '<option value="">--</option>';

                    // Remplir les managers
                    if (data.managers.pm_id) {
                        const pmOption = document.createElement('option');
                        pmOption.value = data.managers.pm_id;
                        pmOption.textContent = data.managers.pm_name;
                        pmSelect.appendChild(pmOption);
                        pmSelect.value = data.managers.pm_id;
                    }

                    if (data.managers.em_id) {
                        const emOption = document.createElement('option');
                        emOption.value = data.managers.em_id;
                        emOption.textContent = data.managers.em_name;
                        emSelect.appendChild(emOption);
                        emSelect.value = data.managers.em_id;
                    }

                    if (data.managers.dm_id) {
                        const dmOption = document.createElement('option');
                        dmOption.value = data.managers.dm_id;
                        dmOption.textContent = data.managers.dm_name;
                        dmSelect.appendChild(dmOption);
                        dmSelect.value = data.managers.dm_id;
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour gérer le changement de rôle
        function handleRoleChange(role) {
            const poolSelect = document.getElementById('pool');
            const pmSelect = document.getElementById('pm_id');
            const emSelect = document.getElementById('em_id');
            const dmSelect = document.getElementById('dm_id');

            // Réinitialiser tous les selects
            pmSelect.value = '';
            emSelect.value = '';
            dmSelect.value = '';

            // Désactiver tous les selects par défaut
            pmSelect.disabled = true;
            emSelect.disabled = true;
            dmSelect.disabled = true;
            poolSelect.disabled = true;

            // Activer/désactiver les champs en fonction du rôle
            switch(role.toLowerCase()) {
                case 'salarié':
                    poolSelect.disabled = false;
                    pmSelect.disabled = false;
                    emSelect.disabled = false;
                    dmSelect.disabled = false;
                    break;
                case 'pm':
                    poolSelect.disabled = false;
                    emSelect.disabled = false;
                    dmSelect.disabled = false;
                    break;
                case 'em':
                    poolSelect.disabled = false;
                    dmSelect.disabled = false;
                    break;
                case 'dm':
                    poolSelect.disabled = false;
                    break;
            }
        }

        // Fonction pour charger les managers (PM, EM, DM)
        async function loadManagers() {
            try {
                const response = await fetch('/JS/STIB/api/admin/users.php');
                const data = await response.json();
                if (data.success) {
                    updateManagerSelects(data.users);
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers(1);
            loadManagers();
            loadServices();
        });
    </script>
</body>
</html>
