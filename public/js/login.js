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
                
                if (response.ok && data.success && data.redirect) {
                    // Utiliser l'URL de redirection fournie par le serveur
                    window.location.href = data.redirect;
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
                    if (result.token) {
                        localStorage.setItem('token', result.token);
                    }
                    
                    // Utiliser l'URL de redirection fournie par le serveur
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        console.error('Pas d\'URL de redirection fournie');
                        alert('Erreur de redirection: URL manquante');
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
