// Fonction pour vérifier la validité du token
async function checkToken(token) {
    try {
        const response = await fetch('/JS/SPIB/api/auth/check_token.php', {
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
                const response = await fetch('/JS/SPIB/api/auth/check_token.php', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Rediriger selon le rôle
                    if (data.role === 'salarié') {
                        window.location.href = '/JS/SPIB/dashboard/employee.php';
                    } else if (data.role === 'manager') {
                        window.location.href = '/JS/SPIB/dashboard/manager.php';
                    } else if (data.role === 'admin') {
                        window.location.href = '/JS/SPIB/dashboard/admin.php';
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
                
                if (data.success && data.token) {
                    // Nettoyer tout ancien token
                    localStorage.clear();
                    // Sauvegarder le nouveau token
                    localStorage.setItem('token', data.token);
                    
                    // Rediriger selon le rôle
                    if (data.user.role === 'salarié') {
                        window.location.href = '/JS/SPIB/dashboard/employee.php';
                    } else if (data.user.role === 'manager') {
                        window.location.href = '/JS/SPIB/dashboard/manager.php';
                    } else if (data.user.role === 'admin') {
                        window.location.href = '/JS/SPIB/dashboard/admin.php';
                    }
                } else {
                    throw new Error(data.message || 'Identifiants incorrects');
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de connexion',
                    text: error.message || 'Une erreur est survenue lors de la connexion'
                });
            }
        });
    });
}
