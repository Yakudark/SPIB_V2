// Fonction pour vérifier la validité du token
async function checkToken(token) {
    try {
        const response = await fetch('/JS/STIB/api/auth/check_token.php', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        return response.ok;
    } catch (error) {
        console.error('Erreur lors de la vérification du token:', error);
        return false;
    }
}

// Ne vérifier le token que si nous sommes sur la page de connexion
if (window.location.pathname.includes('/public/views/login.php')) {
    document.addEventListener('DOMContentLoaded', async function() {
        const token = localStorage.getItem('token');
        if (token) {
            try {
                const response = await fetch('/JS/STIB/api/auth/check_token.php', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Rediriger selon le rôle
                    const role = data.role.toUpperCase(); // Convertir en majuscules pour la comparaison
                    switch(role) {
                        case 'SALARIÉ':
                        case 'SALARIE':
                            window.location.href = '/JS/STIB/dashboard/employee.php';
                            break;
                        case 'MANAGER':
                            window.location.href = '/JS/STIB/dashboard/manager.php';
                            break;
                        case 'ADMIN':
                            window.location.href = '/JS/STIB/dashboard/admin.php';
                            break;
                        case 'PM':
                            window.location.href = '/JS/STIB/dashboard/pm.php';
                            break;
                        default:
                            console.error('Rôle non reconnu:', role);
                            alert('Erreur de redirection: rôle non reconnu');
                    }
                    return;
                }
            } catch (error) {
                console.error('Erreur lors de la vérification du token:', error);
            }
            // Si on arrive ici, le token n'est pas valide
            localStorage.removeItem('token');
        }

        const loginForm = document.getElementById('loginForm');
        
        loginForm.addEventListener('submit', async function(e) {
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
                    localStorage.setItem('token', result.token);
                    
                    // Rediriger selon le rôle
                    console.log('Role:', result.user.role); // Debug
                    
                    const role = result.user.role.toUpperCase(); // Convertir en majuscules pour la comparaison
                    
                    switch(role) {
                        case 'SALARIÉ':
                        case 'SALARIE':
                            window.location.href = '/JS/STIB/dashboard/employee.php';
                            break;
                        case 'MANAGER':
                            window.location.href = '/JS/STIB/dashboard/manager.php';
                            break;
                        case 'ADMIN':
                            window.location.href = '/JS/STIB/dashboard/admin.php';
                            break;
                        case 'PM':
                            window.location.href = '/JS/STIB/dashboard/pm.php';
                            break;
                        default:
                            console.error('Rôle non reconnu:', role);
                            alert('Erreur de redirection: rôle non reconnu');
                    }
                } else {
                    // Afficher le message d'erreur
                    const errorDiv = document.getElementById('error-message');
                    if (errorDiv) {
                        errorDiv.textContent = result.message || 'Erreur de connexion';
                        errorDiv.classList.remove('hidden');
                    } else {
                        alert(result.message || 'Erreur de connexion');
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la connexion');
            }
        });
    });
}
