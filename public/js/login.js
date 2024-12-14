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

                if (data.success) {
                    // Stocker le token JWT
                    sessionStorage.setItem('token', data.token);
                    
                    // Redirection en fonction du rôle
                    switch(data.user.role) {
                        case 'PM':
                            window.location.href = '/JS/SPIB/dashboard/manager.php';
                            break;
                        case 'EM':
                            window.location.href = '/JS/SPIB/dashboard/manager.php';
                            break;
                        case 'DM':
                            window.location.href = '/JS/SPIB/dashboard/manager.php';
                            break;
                        case 'RH':
                            window.location.href = '/JS/SPIB/dashboard/rh.php';
                            break;
                        case 'salarié':
                            window.location.href = '/JS/SPIB/dashboard/employee.php';
                            break;
                        default:
                            alert('Rôle non reconnu');
                    }
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de connexion');
            }
        });
    });
}
