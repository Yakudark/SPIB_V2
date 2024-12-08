function ouvrirPopupConges() {
    Swal.fire({
        title: 'Demande de congés',
        html: `
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Date de début</label>
                <input type="date" id="date_debut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                <input type="date" id="date_fin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Commentaire (optionnel)</label>
                <textarea id="commentaire" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3"></textarea>
            </div>
            <div id="nb_jours" class="text-sm text-gray-500"></div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Demander',
        cancelButtonText: 'Annuler',
        showLoaderOnConfirm: true,
        didOpen: () => {
            // Mettre à jour le calcul des jours quand les dates changent
            ['date_debut', 'date_fin'].forEach(id => {
                document.getElementById(id).addEventListener('change', calculerNombreJours);
            });
        },
        preConfirm: async () => {
            const date_debut = document.getElementById('date_debut').value;
            const date_fin = document.getElementById('date_fin').value;
            const commentaire = document.getElementById('commentaire').value;

            if (!date_debut || !date_fin) {
                Swal.showValidationMessage('Veuillez sélectionner les dates');
                return false;
            }

            if (new Date(date_fin) < new Date(date_debut)) {
                Swal.showValidationMessage('La date de fin doit être après la date de début');
                return false;
            }

            try {
                const response = await fetch('/api/conges/demander.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify({
                        date_debut,
                        date_fin,
                        commentaire
                    })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Erreur lors de la demande');
                return data;

            } catch (error) {
                Swal.showValidationMessage(error.message);
                return false;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Demande envoyée',
                text: `Votre demande de ${result.value.nb_jours} jours de congés a été enregistrée`
            });
            // Recharger les statistiques du dashboard
            chargerStatistiques();
        }
    });
}

async function calculerNombreJours() {
    const date_debut = document.getElementById('date_debut').value;
    const date_fin = document.getElementById('date_fin').value;
    
    if (!date_debut || !date_fin) return;
    
    const debut = new Date(date_debut);
    const fin = new Date(date_fin);
    
    if (fin < debut) {
        document.getElementById('nb_jours').textContent = 'La date de fin doit être après la date de début';
        return;
    }
    
    let nb_jours = 0;
    const current = new Date(debut);
    
    while (current <= fin) {
        if (current.getDay() !== 0 && current.getDay() !== 6) { // Pas weekend
            nb_jours++;
        }
        current.setDate(current.getDate() + 1);
    }
    
    document.getElementById('nb_jours').textContent = `Nombre de jours ouvrés : ${nb_jours}`;
}
