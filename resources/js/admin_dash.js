document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
    initializeDropdowns();
    initializePopper();
    initializeTabs();
    initializeCharts();
});

// Sidebar functionality
function initializeSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarMenu = document.querySelector('.sidebar-menu');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const main = document.querySelector('.main');

    function toggleSidebar() {
        sidebarMenu.classList.toggle('translate-x-[-100%]');
        sidebarOverlay.classList.toggle('hidden');
        
        if (window.innerWidth >= 768) {
            main.classList.toggle('md:w-full');
            main.classList.toggle('md:ml-0');
            main.classList.toggle('md:w-[calc(100%-256px)]');
            main.classList.toggle('md:ml-64');
        }
    }

    if (sidebarToggle && sidebarOverlay) {
        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);
    }

    // Sidebar dropdowns
    const sidebarDropdownToggles = document.querySelectorAll('.sidebar-dropdown-toggle');
    sidebarDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const parent = toggle.parentElement;
            parent.classList.toggle('selected');
        });
    });
}

// Dropdown functionality
function initializeDropdowns() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');

    dropdownToggles.forEach((toggle, index) => {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenus[index].classList.toggle('hidden');
            
            dropdownMenus.forEach((menu, menuIndex) => {
                if (menuIndex !== index) {
                    menu.classList.add('hidden');
                }
            });
        });
    });

    document.addEventListener('click', () => {
        dropdownMenus.forEach(menu => menu.classList.add('hidden'));
    });
}

// Popper initialization
function initializePopper() {
    const popperInstance = {};
    
    document.querySelectorAll('.dropdown').forEach((item, index) => {
        const popperId = `popper-${index}`;
        const toggle = item.querySelector('.dropdown-toggle');
        const menu = item.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            menu.dataset.popperId = popperId;
            popperInstance[popperId] = Popper.createPopper(toggle, menu, {
                modifiers: [
                    {
                        name: 'offset',
                        options: { offset: [0, 8] }
                    },
                    {
                        name: 'preventOverflow',
                        options: { padding: 24 }
                    }
                ],
                placement: 'bottom-end'
            });
        }
    });

    return popperInstance;
}

// Tab functionality
function initializeTabs() {
    document.querySelectorAll('[data-tab]').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const tab = item.dataset.tab;
            const page = item.dataset.tabPage;
            const target = document.querySelector(`[data-tab-for="${tab}"][data-page="${page}"]`);

            if (target) {
                document.querySelectorAll(`[data-tab="${tab}"]`)
                    .forEach(i => i.classList.remove('active'));
                document.querySelectorAll(`[data-tab-for="${tab}"]`)
                    .forEach(i => i.classList.add('hidden'));
                    
                item.classList.add('active');
                target.classList.remove('hidden');
            }
        });
    });
}

// Chart functionality
function initializeCharts() {
    const chartElement = document.getElementById('order-chart');
    if (chartElement && typeof Chart !== 'undefined') {
        new Chart(chartElement, {
            type: 'line',
            data: {
                labels: generateNDays(7),
                datasets: [
                    {
                        label: 'Active',
                        data: generateRandomData(7),
                        borderWidth: 1,
                        fill: true,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgb(59 130 246 / .05)',
                        tension: .2
                    },
                    {
                        label: 'Completed',
                        data: generateRandomData(7),
                        borderWidth: 1,
                        fill: true,
                        pointBackgroundColor: 'rgb(16, 185, 129)',
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgb(16 185 129 / .05)',
                        tension: .2
                    },
                    {
                        label: 'Canceled',
                        data: generateRandomData(7),
                        borderWidth: 1,
                        fill: true,
                        pointBackgroundColor: 'rgb(244, 63, 94)',
                        borderColor: 'rgb(244, 63, 94)',
                        backgroundColor: 'rgb(244 63 94 / .05)',
                        tension: .2
                    }
                ]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
}

// Utility functions
function generateNDays(n) {
    return [...Array(n)].map((_, i) => {
        const date = new Date();
        date.setDate(date.getDate() - i);
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    });
}

function generateRandomData(n) {
    return [...Array(n)].map(() => Math.round(Math.random() * 10));
}