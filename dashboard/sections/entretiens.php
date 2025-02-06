<!-- Section des entretiens -->
<div class="bg-white rounded-lg shadow p-6">
    <div class="border-b border-gray-200 mb-4">
        <ul class="flex -mb-px">
            <li class="mr-2">
                <button onclick="switchTab('upcoming')" class="inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active" id="upcoming-tab">
                    Prochains Entretiens
                </button>
            </li>
            <li class="mr-2">
                <button onclick="switchTab('history')" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="history-tab">
                    Historique des Entretiens
                </button>
            </li>
        </ul>
    </div>

    <!-- Filtres -->
    <div class="mb-4 w-64">
        <label for="filterAgent" class="block text-sm font-medium text-gray-700">Filtrer par agent</label>
        <select id="filterAgent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Tous les agents</option>
        </select>
    </div>

    <!-- Contenu des onglets -->
    <div id="upcoming-content" class="tab-content">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avec</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="upcoming-interviews">
                    <!-- Les entretiens à venir seront injectés ici -->
                </tbody>
            </table>
        </div>
    </div>

    <div id="history-content" class="tab-content hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avec</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="history-interviews">
                    <!-- L'historique des entretiens sera injecté ici -->
                </tbody>
            </table>
        </div>
    </div>
</div>
