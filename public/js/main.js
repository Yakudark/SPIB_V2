class SPIB {
    constructor() {
        this.currentUser = null;
        this.initializeNavigation();
        this.initializeDragAndDrop();
        this.loadInitialData();
    }

    initializeNavigation() {
        document.querySelectorAll('[data-page]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.showPage(e.target.dataset.page);
            });
        });
    }

    showPage(pageId) {
        document.querySelectorAll('.page-content').forEach(page => {
            page.classList.add('hidden');
        });
        document.getElementById(pageId).classList.remove('hidden');
        this.loadPageData(pageId);
    }

    initializeDragAndDrop() {
        const containers = document.querySelectorAll('.drop-zone');
        const draggable = new Draggable.Sortable(containers, {
            draggable: '.draggable-card',
            handle: '.draggable-card',
            mirror: {
                appendTo: 'body',
                constrainDimensions: true
            }
        });

        draggable.on('drag:start', () => {
            containers.forEach(container => {
                container.classList.add('drop-zone-active');
            });
        });

        draggable.on('drag:stop', () => {
            containers.forEach(container => {
                container.classList.remove('drop-zone-active');
            });
        });

        draggable.on('sortable:stop', (event) => {
            const action = event.data.dragEvent.source.dataset.action;
            const zone = event.data.newContainer.dataset.zone;
            this.handleActionDrop(action, zone);
        });
    }

    async loadInitialData() {
        try {
            const [services, pools, roles] = await Promise.all([
                this.fetchData('/api/services'),
                this.fetchData('/api/users/pools'),
                this.fetchData('/api/users/roles')
            ]);

            this.populateFilters(services, pools, roles);
            this.showPage('dashboard');
        } catch (error) {
            console.error('Error loading initial data:', error);
        }
    }

    async loadPageData(pageId) {
        switch (pageId) {
            case 'dashboard':
                await this.loadDashboard();
                break;
            case 'employees':
                await this.loadEmployees();
                break;
            case 'interviews':
                await this.loadInterviews();
                break;
            case 'services':
                await this.loadServices();
                break;
        }
    }

    async loadDashboard() {
        try {
            const [upcoming, required, stats] = await Promise.all([
                this.fetchData('/api/interviews/upcoming'),
                this.fetchData('/api/actions/required'),
                this.fetchData('/api/statistics')
            ]);

            this.renderUpcomingInterviews(upcoming);
            this.renderRequiredActions(required);
            this.renderStatistics(stats);
        } catch (error) {
            console.error('Error loading dashboard:', error);
        }
    }

    async loadEmployees() {
        try {
            const employees = await this.fetchData('/api/users');
            this.renderEmployees(employees);
        } catch (error) {
            console.error('Error loading employees:', error);
        }
    }

    async loadInterviews() {
        try {
            const interviews = await this.fetchData('/api/interviews');
            this.renderInterviews(interviews);
        } catch (error) {
            console.error('Error loading interviews:', error);
        }
    }

    async loadServices() {
        try {
            const services = await this.fetchData('/api/services');
            this.renderServices(services);
        } catch (error) {
            console.error('Error loading services:', error);
        }
    }

    async handleActionDrop(action, zone) {
        try {
            const response = await this.fetchData('/api/interviews', {
                method: 'POST',
                body: JSON.stringify({
                    action,
                    zone,
                    // Add other necessary data
                })
            });

            if (response.success) {
                this.showNotification('Action créée avec succès', 'success');
                await this.loadInterviews();
            }
        } catch (error) {
            console.error('Error handling action drop:', error);
            this.showNotification('Erreur lors de la création de l\'action', 'error');
        }
    }

    async fetchData(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    showNotification(message, type = 'info') {
        // Implementation of notification system
    }

    // Render functions for different components
    renderUpcomingInterviews(interviews) {
        const container = document.getElementById('upcomingInterviews');
        container.innerHTML = interviews.map(interview => `
            <div class="card">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold">${interview.type_action}</h4>
                        <p class="text-sm text-gray-600">${interview.employee_name}</p>
                    </div>
                    <span class="text-sm text-gray-500">${new Date(interview.date_action).toLocaleDateString()}</span>
                </div>
            </div>
        `).join('');
    }

    renderRequiredActions(actions) {
        const container = document.getElementById('requiredActions');
        container.innerHTML = actions.map(action => `
            <div class="card">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold">${action.type}</h4>
                        <p class="text-sm text-gray-600">${action.description}</p>
                    </div>
                    <button class="btn btn-primary text-sm">Agir</button>
                </div>
            </div>
        `).join('');
    }

    renderStatistics(stats) {
        const container = document.getElementById('statistics');
        container.innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary">${stats.total_interviews}</div>
                    <div class="text-sm text-gray-600">Entretiens</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-secondary">${stats.total_employees}</div>
                    <div class="text-sm text-gray-600">Employés</div>
                </div>
            </div>
        `;
    }

    renderEmployees(employees) {
        const container = document.getElementById('employeesList');
        container.innerHTML = employees.map(employee => `
            <div class="card">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold">${employee.nom} ${employee.prenom}</h4>
                        <p class="text-sm text-gray-600">${employee.service}</p>
                        <p class="text-sm text-gray-500">${employee.role}</p>
                    </div>
                    <div class="space-y-2">
                        <button class="btn btn-primary text-sm w-full">Détails</button>
                        <button class="btn btn-secondary text-sm w-full">Entretien</button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderServices(services) {
        const container = document.getElementById('servicesList');
        container.innerHTML = services.map(service => `
            <div class="card">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold">${service.nom_service}</h4>
                        <p class="text-sm text-gray-600">Responsable: ${service.responsable_nom || 'Non assigné'}</p>
                    </div>
                    <div class="space-y-2">
                        <button class="btn btn-primary text-sm w-full">Modifier</button>
                        <button class="btn btn-danger text-sm w-full">Supprimer</button>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

// Initialize the application
document.addEventListener('DOMContentLoaded', () => {
    window.app = new SPIB();
});
