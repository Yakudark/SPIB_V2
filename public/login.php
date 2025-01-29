<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STIB - Connexion</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary">STIB</h1>
            <p class="text-gray-600">Système de Gestion des Entretiens</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-8">
            <form id="loginForm" class="space-y-6">
                <div>
                    <label class="label" for="matricule">Matricule</label>
                    <input type="text" id="matricule" name="matricule" class="input" required>
                </div>
                <div>
                    <label class="label" for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="input" required>
                </div>
                <div id="error-message" class="text-danger text-sm hidden"></div>
                <button type="submit" class="btn btn-primary w-full">
                    Se connecter
                </button>
            </form>
        </div>
    </div>

    <script>
        // Vérifier si l'utilisateur est déjà connecté
        const user = localStorage.getItem('user');
        if (user) {
            const userData = JSON.parse(user);
            window.location.href = getRedirectUrl(userData.role);
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorMessage = document.getElementById('error-message');
            errorMessage.classList.add('hidden');

            const matricule = document.getElementById('matricule').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('../api/auth/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ matricule, password })
                });

                const data = await response.json();

                if (data.success) {
                    // Stocker le token et les informations de l'utilisateur
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    // Redirection selon le rôle
                    window.location.href = getRedirectUrl(data.user.role);
                } else {
                    errorMessage.textContent = data.message || 'Identifiants invalides';
                    errorMessage.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Erreur:', error);
                errorMessage.textContent = 'Erreur de connexion au serveur';
                errorMessage.classList.remove('hidden');
            }
        });

        function getRedirectUrl(role) {
            switch (role.toLowerCase()) {
                case 'superadmin':
                    return '../dashboard/admin.php';
                case 'rh':
                    return '../dashboard/rh.php';
                case 'dm':
                    return '../dashboard/manager.php';
                case 'em':
                    return '../dashboard/manager.php';
                case 'pm':
                    return '../dashboard/manager.php';
                case 'salarié':
                    return '../dashboard/employee.php';
                default:
                    return 'login.php';
            }
        }
    </script>
</body>
</html>
