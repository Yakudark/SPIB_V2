class STIB {
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

// --- Ajout pour la gestion des langues ---
const translations = {
    fr: {
        dashboard: 'Tableau de bord',
        employees: 'Employés',
        interviews: 'Entretiens',
        services: 'Services',
        logout: 'Déconnexion',
        upcomingInterviews: 'Entretiens à venir',
        requiredActions: 'Actions requises',
        statistics: 'Statistiques',
        manageEmployees: 'Gestion des Employés',
        addEmployee: 'Ajouter un employé',
        filters: 'Filtres',
        service: 'Service',
        pool: 'Pool',
        role: 'Rôle',
        availableActions: 'Actions disponibles',
        interview: 'Entretien',
        interviewDesc: 'Entretien formel avec l\'employé',
        call: 'Appel bienveillant',
        callDesc: 'Prise de nouvelles et soutien',
        welcomeBack: 'Welcome Back',
        welcomeBackDesc: 'Entretien de retour',
        toSchedule: 'À planifier',
        inProgress: 'En cours',
        completed: 'Terminé',
        manageServices: 'Gestion des Services',
        addService: 'Ajouter un service',
        details: 'Détails',
        interviewBtn: 'Entretien',
        edit: 'Modifier',
        delete: 'Supprimer',
        employeesCard: 'Employés',
        interviewsCard: 'Entretiens',
        agir: 'Agir',
        notAssigned: 'Non assigné',
    },
    nl: {
        dashboard: 'Dashboard',
        employees: 'Werknemers',
        interviews: 'Gesprekken',
        services: 'Diensten',
        logout: 'Afmelden',
        upcomingInterviews: 'Komende gesprekken',
        requiredActions: 'Vereiste acties',
        statistics: 'Statistieken',
        manageEmployees: 'Beheer van werknemers',
        addEmployee: 'Werknemer toevoegen',
        filters: 'Filters',
        service: 'Dienst',
        pool: 'Pool',
        role: 'Rol',
        availableActions: 'Beschikbare acties',
        interview: 'Gesprek',
        interviewDesc: 'Formeel gesprek met de werknemer',
        call: 'Welzijnsoproep',
        callDesc: 'Contact opnemen en ondersteuning',
        welcomeBack: 'Welcome Back',
        welcomeBackDesc: 'Terugkomgesprek',
        toSchedule: 'Te plannen',
        inProgress: 'Bezig',
        completed: 'Voltooid',
        manageServices: 'Beheer van diensten',
        addService: 'Dienst toevoegen',
        details: 'Details',
        interviewBtn: 'Gesprek',
        edit: 'Bewerken',
        delete: 'Verwijderen',
        employeesCard: 'Werknemers',
        interviewsCard: 'Gesprekken',
        agir: 'Actie',
        notAssigned: 'Niet toegewezen',
    }
};
// let currentLang = 'fr'; // Désactivé pour éviter les conflits de scope, la variable est globale via lang.js
let currentLang = 'fr';

function switchLanguage() {
    currentLang = currentLang === 'fr' ? 'nl' : 'fr';
    updateAllTexts();
    updateLangFlag();
}

function updateAllTexts() {
    const t = translations[currentLang];
    // Navbar
    document.querySelector('[data-page="dashboard"]').textContent = t.dashboard;
    document.querySelector('[data-page="employees"]').textContent = t.employees;
    document.querySelector('[data-page="interviews"]').textContent = t.interviews;
    document.querySelector('[data-page="services"]').textContent = t.services;
    document.getElementById('logoutBtn').textContent = t.logout;
    document.getElementById('langSwitchBtn').textContent = currentLang === 'fr' ? 'NL' : 'FR';
    // Dashboard cards
    document.querySelector('#dashboard .card:nth-child(1) h3').textContent = t.upcomingInterviews;
    document.querySelector('#dashboard .card:nth-child(2) h3').textContent = t.requiredActions;
    document.querySelector('#dashboard .card:nth-child(3) h3').textContent = t.statistics;
    // Employees
    document.querySelector('#employees h2').textContent = t.manageEmployees;
    document.getElementById('addEmployeeBtn').textContent = t.addEmployee;
    document.querySelector('#employees .card h3').textContent = t.filters;
    document.querySelector('label[for="serviceFilter"], label.label[for="serviceFilter"]').textContent = t.service;
    document.querySelector('label[for="poolFilter"], label.label[for="poolFilter"]').textContent = t.pool;
    document.querySelector('label[for="roleFilter"], label.label[for="roleFilter"]').textContent = t.role;
    // Interviews
    document.querySelector('#interviews .card h3').textContent = t.availableActions;
    document.querySelector('#interviews .card .draggable-card[data-action="entretien"] h4').textContent = t.interview;
    document.querySelector('#interviews .card .draggable-card[data-action="entretien"] p').textContent = t.interviewDesc;
    document.querySelector('#interviews .card .draggable-card[data-action="appel_bienveillant"] h4').textContent = t.call;
    document.querySelector('#interviews .card .draggable-card[data-action="appel_bienveillant"] p').textContent = t.callDesc;
    document.querySelector('#interviews .card .draggable-card[data-action="welcome_back"] h4').textContent = t.welcomeBack;
    document.querySelector('#interviews .card .draggable-card[data-action="welcome_back"] p').textContent = t.welcomeBackDesc;
    document.querySelector('#interviews .card:nth-child(2) h3').textContent = t.toSchedule;
    document.querySelector('#interviews .card:nth-child(3) h3').textContent = t.inProgress;
    document.querySelector('#interviews .card:nth-child(4) h3').textContent = t.completed;
    // Services
    document.querySelector('#services h2').textContent = t.manageServices;
    document.getElementById('addServiceBtn').textContent = t.addService;
}

function updateLangFlag() {
    const flag = document.getElementById('langFlag');
    if (!flag) return;
    flag.src = currentLang === 'fr' ? '/JS/STIB/public/assets/nl.svg' : '/JS/STIB/public/assets/fr.svg';
    flag.alt = currentLang === 'fr' ? 'Néerlandais' : 'Français';
}

document.addEventListener('DOMContentLoaded', () => {
    window.app = new STIB();
    document.getElementById('langSwitchBtn').addEventListener('click', switchLanguage);
    updateAllTexts();
    updateLangFlag();
});
// --- Fin ajout langues ---
