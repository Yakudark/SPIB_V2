<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /JS/SPIB/dashboard/employee.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPIB - Connexion</title>
    <link href="/JS/SPIB/public/css/tailwind.min.css" rel="stylesheet">
    <link href="/JS/SPIB/public/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1a365d 0%, #2d5a9e 100%);
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .login-button {
            background-color: #1a365d;
            transition: all 0.3s ease;
        }
        .login-button:hover {
            background-color: #2d5a9e;
            transform: translateY(-2px);
        }
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #1a365d;
            box-shadow: 0 0 0 2px rgba(26, 54, 93, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="login-container w-full max-w-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Bienvenue sur SPIB</h1>
            <p class="text-gray-600">Connectez-vous à votre espace</p>
        </div>
        
        <form id="loginForm" class="space-y-6">
            <div>
                <label for="matricule" class="block text-sm font-medium text-gray-700 mb-1">
                    Matricule
                </label>
                <input id="matricule" name="matricule" type="text" required 
                    class="form-input block w-full px-4 py-3 rounded-md border border-gray-300 shadow-sm focus:outline-none text-gray-900"
                    placeholder="Entrez votre matricule">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Mot de passe
                </label>
                <input id="password" name="password" type="password" required
                    class="form-input block w-full px-4 py-3 rounded-md border border-gray-300 shadow-sm focus:outline-none text-gray-900"
                    placeholder="Entrez votre mot de passe">
            </div>
            
            <button type="submit"
                class="login-button w-full py-3 px-4 text-white rounded-md shadow-md hover:shadow-lg font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Se connecter
            </button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const matricule = document.getElementById('matricule').value;
            const password = document.getElementById('password').value;
            
            try {
                const response = await fetch('/JS/SPIB/api/auth/login.php', {
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
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Connexion réussie !',
                        text: 'Redirection en cours...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (data.user.role === 'salarié') {
                            window.location.href = '/JS/SPIB/dashboard/employee.php';
                        } else if (data.user.role === 'manager') {
                            window.location.href = '/JS/SPIB/dashboard/manager.php';
                        } else if (data.user.role === 'admin') {
                            window.location.href = '/JS/SPIB/dashboard/admin.php';
                        }
                    });
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
    </script>
</body>
</html>
