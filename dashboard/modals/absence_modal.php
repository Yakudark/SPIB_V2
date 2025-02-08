<!-- Modal pour ajouter une absence -->
<div id="absenceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Ajouter une absence</h2>
            <form id="absenceForm">
                <div class="mb-4">
                    <label for="absenceAgent" class="block text-sm font-medium text-gray-700">Agent</label>
                    <select id="absenceAgent" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <!-- Les agents seront ajoutés ici dynamiquement -->
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="dateDebut" class="block text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" id="dateDebut" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                
                <div class="mb-4">
                    <label for="dateFin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="date" id="dateFin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                
                <div class="mb-4">
                    <label for="commentaire" class="block text-sm font-medium text-gray-700">Commentaire</label>
                    <textarea id="commentaire" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAbsenceModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg text-sm">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>