<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $role = strtoupper($_SESSION['role']);
    switch ($role) {
        case 'SALARIÉ':
        case 'SALARIE':
            header('Location: /JS/STIB/dashboard/employee.php');
            break;
        case 'MANAGER':
            header('Location: /JS/STIB/dashboard/manager.php');
            break;
        case 'ADMIN':
            header('Location: /JS/STIB/dashboard/admin.php');
            break;
        case 'PM':
            header('Location: /JS/STIB/dashboard/pm.php');
            break;
        case 'EM':
            header('Location: /JS/STIB/dashboard/em.php');
            break;
        default:
            // Si le rôle n'est pas reconnu, déconnecter l'utilisateur
            session_destroy();
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STIB - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
            <!-- Logo et Titre -->
            <div class="text-center">
                <img class="mx-auto h-16 w-auto" src="/JS/STIB/public/assets/img/logo.png" alt="STIB Logo">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Bienvenue sur STIB
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Connectez-vous à votre espace
                </p>
            </div>

            <!-- Formulaire -->
            <form id="loginForm" class="mt-8 space-y-6">
                <div class="rounded-md shadow-sm -space-y-px">
                    <!-- Matricule -->
                    <div class="mb-4">
                        <label for="matricule" class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="matricule" name="matricule" type="text" required 
                                class="appearance-none rounded relative block w-full px-3 py-2 pl-10
                                border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none
                                focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Entrez votre matricule">
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required
                                class="appearance-none rounded relative block w-full px-3 py-2 pl-10
                                border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none
                                focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Entrez votre mot de passe">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bouton de connexion -->
                <div>
                    <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent
                        text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700
                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                        transition-colors duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        Se connecter
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-6">
                <p class="text-center text-sm text-gray-600">
                    2025 STIB. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const matricule = document.getElementById('matricule').value;
            const password = document.getElementById('password').value;
            
            try {
                const response = await fetch('/JS/STIB/api/auth/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        matricule: matricule,
                        password: password
                    })
                });
                
                const data = await response.json();
                console.log('Response complète:', data);
                
                if (data.success) {
                    console.log('Utilisateur:', data.user);
                    console.log('Rôle:', data.user.role);
                    
                    await Swal.fire({
                        icon: 'success',
                        title: 'Connexion réussie !',
                        text: 'Redirection en cours...',
                        timer: 3000,
                        showConfirmButton: false
                    });

                    if (data.user && data.user.role) {
                        const role = data.user.role;  
                        console.log('Rôle détecté:', role);
                        
                        if (role.toLowerCase() === 'superadmin') {
                            console.log('Redirection vers admin dashboard...');
                            location.replace('/JS/STIB/dashboard/admin.php');
                        } else if (role.toLowerCase() === 'salarié' || role.toLowerCase() === 'salarie') {
                            location.replace('/JS/STIB/dashboard/employee.php');
                        } else if (role.toLowerCase() === 'pm') {
                            location.replace('/JS/STIB/dashboard/pm.php');
                        } else {
                            console.error('Rôle non reconnu:', role);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur de redirection',
                                text: 'Rôle non reconnu: ' + role,
                                confirmButtonColor: '#1a365d'
                            });
                        }
                    } else {
                        console.error('Données utilisateur manquantes:', data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur de redirection',
                            text: 'Données utilisateur manquantes',
                            confirmButtonColor: '#1a365d'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de connexion',
                        text: data.message,
                        confirmButtonColor: '#1a365d'
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de connexion',
                    text: 'Une erreur est survenue lors de la connexion',
                    confirmButtonColor: '#1a365d'
                });
            }
        });

        // Toggle visibilité du mot de passe
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Change l'icône
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
