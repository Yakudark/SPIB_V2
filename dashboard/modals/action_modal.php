<!-- Modal pour nouvelle action -->
<div id="actionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Nouvelle action</h3>
            <form id="actionForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Agent</label>
                    <select name="agent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <!-- Options seront chargées dynamiquement -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type d'action</label>
                    <select name="action_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <!-- Options seront chargées dynamiquement -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date_action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Commentaire</label>
                    <textarea name="commentaire" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeActionModal()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
