<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SuperAdmin') {
    header('Location: /JS/SPIB/public/views/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SPIB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Barre latérale -->
    <div class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg">
        <div class="p-4">
            <div class="flex items-center justify-center mb-8">
                <img src="/JS/SPIB/public/assets/img/logo.png" alt="SPIB Logo" class="h-12">
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
            <a href="/JS/SPIB/api/auth/logout.php" class="flex items-center justify-center p-3 text-red-600 hover:bg-red-50 rounded-lg">
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
            <div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium" id="modalTitle">Ajouter un utilisateur</h3>
                        <button onclick="closeUserModal()" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="userForm" onsubmit="handleUserSubmit(event)">
                        <input type="hidden" id="userId" name="id">
                        
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
                                    <option value="salarié">Salarié</option>
                                    <option value="RH">RH</option>
                                    <option value="EM">EM</option>
                                    <option value="PM">PM</option>
                                    <option value="DM">DM</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pool</label>
                                <select id="pool" name="pool" required onchange="handleServiceChange(this.value)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Sélectionner un service</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PM</label>
                                <select id="pm_id" name="pm_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Sélectionner un PM</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">EM</label>
                                <select id="em_id" name="em_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Sélectionner un EM</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">DM</label>
                                <select id="dm_id" name="dm_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Sélectionner un DM</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeUserModal()"
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                                Annuler
                            </button>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des utilisateurs -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pool</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="users-table">
                            <!-- Les données seront insérées ici dynamiquement -->
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
    </div>

    <script>
        let currentPage = 1;
        let totalPages = 1;
        const itemsPerPage = 10;
        let editMode = false;
        let managers = [];
        let services = [];

        // Fonction pour charger les services
        async function loadServices() {
            try {
                const response = await fetch('/JS/SPIB/api/admin/services.php');
                const data = await response.json();
                if (data.success) {
                    services = data.services;
                    updateServiceSelect();
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Mettre à jour le select des services
        function updateServiceSelect() {
            const select = document.getElementById('pool');
            select.innerHTML = '<option value="">Sélectionner un service</option>';
            services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.nom_service;
                option.textContent = service.nom_service;
                select.appendChild(option);
            });
        }

        // Fonction pour charger les managers (PM, EM, DM)
        async function loadManagers() {
            try {
                const response = await fetch('/JS/SPIB/api/admin/users.php');
                const data = await response.json();
                if (data.success) {
                    managers = data.users;
                    updateManagerSelects();
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Mettre à jour les selects des managers
        function updateManagerSelects() {
            const selects = ['pm_id', 'em_id', 'dm_id'];
            selects.forEach(selectId => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Sélectionner un manager</option>';
                managers.forEach(manager => {
                    const option = document.createElement('option');
                    option.value = manager.id;
                    option.textContent = `${manager.prenom} ${manager.nom}`;
                    select.appendChild(option);
                });
            });
        }

        // Fonction pour ouvrir le modal
        async function openUserModal(userId = null) {
            editMode = userId !== null;
            const modal = document.getElementById('userModal');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('userForm');
            
            modalTitle.textContent = editMode ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur';
            form.reset();
            
            if (editMode) {
                try {
                    const response = await fetch(`/JS/SPIB/api/admin/user_operations.php?id=${userId}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        const user = data.user;
                        document.getElementById('userId').value = user.id;
                        document.getElementById('nom').value = user.nom;
                        document.getElementById('prenom').value = user.prenom;
                        document.getElementById('matricule').value = user.matricule;
                        document.getElementById('role').value = user.role;
                        document.getElementById('pool').value = user.pool || '';
                        document.getElementById('pm_id').value = user.pm_id || '';
                        document.getElementById('em_id').value = user.em_id || '';
                        document.getElementById('dm_id').value = user.dm_id || '';
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
            const data = Object.fromEntries(formData.entries());
            
            try {
                const method = editMode ? 'PUT' : 'POST';
                const response = await fetch('/JS/SPIB/api/admin/user_operations.php', {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    closeUserModal();
                    loadUsers(currentPage);
                    loadManagers();
                    loadServices();
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.error, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'error');
            }
        }

        // Fonction pour supprimer un utilisateur
        async function deleteUser(userId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                return;
            }
            
            try {
                const response = await fetch(`/JS/SPIB/api/admin/user_operations.php?id=${userId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                if (result.success) {
                    loadUsers(currentPage);
                    loadManagers();
                    loadServices();
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

        // Fonction pour charger les utilisateurs
        async function loadUsers(page = 1) {
            try {
                const response = await fetch(`/JS/SPIB/api/admin/users.php?page=${page}&limit=${itemsPerPage}`);
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.getElementById('users-table');
                    tbody.innerHTML = '';
                    
                    data.users.forEach(user => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.nom}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.prenom}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.matricule}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.pm_name || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.em_name || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.dm_name || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    ${user.role}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.pool || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="openUserModal(${user.id})" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });

                    // Mettre à jour la pagination
                    currentPage = data.pagination.current_page;
                    totalPages = data.pagination.total_pages;
                    updatePagination(data.pagination);
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
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

        // Fonction pour gérer le changement de service
        async function handleServiceChange(service) {
            const roleSelect = document.getElementById('role');
            const selectedRole = roleSelect.value;

            // Si c'est un salarié ou un EM
            if (selectedRole === 'salarié' || selectedRole === 'EM') {
                try {
                    const response = await fetch(`/JS/SPIB/api/admin/service_managers.php?service=${encodeURIComponent(service)}`);
                    const data = await response.json();
                    
                    if (data.success && data.managers) {
                        // Mettre à jour les selects des managers
                        const pmSelect = document.getElementById('pm_id');
                        const emSelect = document.getElementById('em_id');
                        const dmSelect = document.getElementById('dm_id');

                        // Réinitialiser les selects
                        pmSelect.innerHTML = '<option value="">Sélectionner un PM</option>';
                        emSelect.innerHTML = '<option value="">Sélectionner un EM</option>';
                        dmSelect.innerHTML = '<option value="">Sélectionner un DM</option>';

                        if (selectedRole === 'salarié') {
                            // Pour un salarié, on remplit tous les managers
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
                        } else if (selectedRole === 'EM') {
                            // Pour un EM, on remplit uniquement le DM
                            if (data.managers.dm_id) {
                                const dmOption = document.createElement('option');
                                dmOption.value = data.managers.dm_id;
                                dmOption.textContent = data.managers.dm_name;
                                dmSelect.appendChild(dmOption);
                                dmSelect.value = data.managers.dm_id;
                            }
                        }
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            } else {
                // Pour les autres rôles, réinitialiser les managers
                updateManagerSelects();
            }
        }

        // Fonction pour gérer le changement de rôle
        function handleRoleChange(role) {
            const service = document.getElementById('pool').value;
            if (service) {
                handleServiceChange(service);
            }
        }

        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', () => {
            loadUsers(1);
            loadManagers();
            loadServices();
        });
    </script>
</body>
</html>
