<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Si l'utilisateur est déjà connecté, redirection directe
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $role = strtoupper(trim($_SESSION['role']));
    
    switch($role) {
        case 'EM':
            header('Location: /JS/STIB/dashboard/em.php');
            break;
        case 'PM':
            header('Location: /JS/STIB/dashboard/pm.php');
            break;
        case 'RH':
            header('Location: /JS/STIB/dashboard/rh.php');
            break;
        case 'DM':
            header('Location: /JS/STIB/dashboard/manager.php');
            break;
        case 'SUPERADMIN':
            header('Location: /JS/STIB/dashboard/admin.php');
            break;
        case 'SALARIE':
        case 'SALARIÉ':
            header('Location: /JS/STIB/dashboard/employee.php');
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
    <title data-i18n="login">STIB - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
            <!-- Logo et Titre -->
            <div class="text-center">
                <img class="mx-auto h-16 w-auto" src="/JS/STIB/public/assets/STIB_logo.png" alt="STIB Logo">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900" id="welcomeTitle" data-i18n="welcome">Bienvenue sur STIB</h2>
                <p class="mt-2 text-sm text-gray-600" id="connectText" data-i18n="connect">Connectez-vous à votre espace</p>
                <button id="langSwitchBtnLogin" class="btn btn-secondary text-sm mt-3"><img id="langFlagLogin" src="/JS/STIB/public/assets/nl.svg" alt="Changer la langue" style="width:24px;height:16px;vertical-align:middle;"></button>
            </div>

            <!-- Formulaire -->
            <form id="loginForm" class="mt-8 space-y-6">
                <div class="rounded-md shadow-sm -space-y-px">
                    <!-- Matricule -->
                    <div class="mb-4">
                        <label for="matricule" id="matriculeLabel" data-i18n="username" class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="matricule" name="matricule" type="text" data-i18n="entermatric" required 
                                class="appearance-none rounded relative block w-full px-3 py-2 pl-10
                                border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none
                                focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Entrez votre matricule" data-i18n-placeholder="entermatric">
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-4">
                        <label for="password" data-i18n="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required
                                class="appearance-none rounded relative block w-full px-3 py-2 pl-10
                                border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none
                                focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Entrez votre mot de passe" data-i18n-placeholder="enterpassword">
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
                    <button id="loginBtn" type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent
                        text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700
                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                        transition-colors duration-200" data-i18n="submit">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        Se connecter
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-6">
                <p data-i18n="tousDroits" class="text-center text-sm text-gray-600">
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
                
                const result = await response.json();
                
                if (result.success) {
                    // Afficher le message de succès
                    await Swal.fire({
                        icon: 'success',
                        title: 'Connexion réussie !',
                        text: 'Redirection en cours...',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Utiliser l'URL de redirection fournie par le serveur
                    window.location.href = result.redirect;
                } else {
                    // Afficher le message d'erreur
                    await Swal.fire({
                        icon: 'error',
                        title: 'Erreur de connexion',
                        text: result.message,
                        confirmButtonColor: '#1a365d'
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
                await Swal.fire({
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
    <script src="/JS/STIB/public/js/lang.js"></script>
    <script>
        function updateLangFlagLogin() {
            const flag = document.getElementById('langFlagLogin');
            if (!flag) return;
            flag.src = currentLang === 'fr' ? '/JS/STIB/public/assets/nl.svg' : '/JS/STIB/public/assets/fr.svg';
            flag.alt = currentLang === 'fr' ? 'Néerlandais' : 'Français';
        }
        setupLangSwitcher('langSwitchBtnLogin', updateLangFlagLogin);
        document.addEventListener('DOMContentLoaded', function() {
            updateAllTexts();
            updateLangFlagLogin();
        });
    </script>
</body>
</html>
