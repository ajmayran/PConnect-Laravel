
// start: Sidebar
// Sidebar toggle
const sidebarToggle = document.querySelector('.sidebar-toggle');
const sidebarMenu = document.querySelector('.sidebar-menu');
const sidebarOverlay = document.querySelector('.sidebar-overlay');
const main = document.querySelector('.main');

function toggleSidebar() {
    sidebarMenu.classList.toggle('translate-x-[-100%]');
    sidebarOverlay.classList.toggle('hidden');
    
    // Toggle main content width and margin
    if (window.innerWidth >= 768) { // md breakpoint
        main.classList.toggle('md:w-full');
        main.classList.toggle('md:ml-0');
        main.classList.toggle('md:w-[calc(100%-256px)]');
        main.classList.toggle('md:ml-64');
    }
}

sidebarToggle.addEventListener('click', toggleSidebar);
sidebarOverlay.addEventListener('click', toggleSidebar);

// Dropdown menus
const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
const dropdownMenus = document.querySelectorAll('.dropdown-menu');

dropdownToggles.forEach((toggle, index) => {
    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownMenus[index].classList.toggle('hidden');
        
        // Hide other dropdown menus
        dropdownMenus.forEach((menu, menuIndex) => {
            if (menuIndex !== index) {
                menu.classList.add('hidden');
            }
        });
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', () => {
    dropdownMenus.forEach(menu => {
        menu.classList.add('hidden');
    });
});

// Sidebar dropdown
const sidebarDropdownToggles = document.querySelectorAll('.sidebar-dropdown-toggle');

sidebarDropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', (e) => {
        e.preventDefault();
        const parent = toggle.parentElement;
        parent.classList.toggle('selected');
    });
});
// end: Sidebar



// start: Popper
const popperInstance = {}
document.querySelectorAll('.dropdown').forEach(function (item, index) {
    const popperId = 'popper-' + index
    const toggle = item.querySelector('.dropdown-toggle')
    const menu = item.querySelector('.dropdown-menu')
    menu.dataset.popperId = popperId
    popperInstance[popperId] = Popper.createPopper(toggle, menu, {
        modifiers: [
            {
                name: 'offset',
                options: {
                    offset: [0, 8],
                },
            },
            {
                name: 'preventOverflow',
                options: {
                    padding: 24,
                },
            },
        ],
        placement: 'bottom-end'
    });
})
document.addEventListener('click', function (e) {
    const toggle = e.target.closest('.dropdown-toggle')
    const menu = e.target.closest('.dropdown-menu')
    if (toggle) {
        const menuEl = toggle.closest('.dropdown').querySelector('.dropdown-menu')
        const popperId = menuEl.dataset.popperId
        if (menuEl.classList.contains('hidden')) {
            hideDropdown()
            menuEl.classList.remove('hidden')
            showPopper(popperId)
        } else {
            menuEl.classList.add('hidden')
            hidePopper(popperId)
        }
    } else if (!menu) {
        hideDropdown()
    }
})

function hideDropdown() {
    document.querySelectorAll('.dropdown-menu').forEach(function (item) {
        item.classList.add('hidden')
    })
}
function showPopper(popperId) {
    popperInstance[popperId].setOptions(function (options) {
        return {
            ...options,
            modifiers: [
                ...options.modifiers,
                { name: 'eventListeners', enabled: true },
            ],
        }
    });
    popperInstance[popperId].update();
}
function hidePopper(popperId) {
    popperInstance[popperId].setOptions(function (options) {
        return {
            ...options,
            modifiers: [
                ...options.modifiers,
                { name: 'eventListeners', enabled: false }
            ],
        }
    });
}
// end: Popper



// start: Tab
document.querySelectorAll('[data-tab]').forEach(function (item) {
    item.addEventListener('click', function (e) {
        e.preventDefault()
        const tab = item.dataset.tab
        const page = item.dataset.tabPage
        const target = document.querySelector('[data-tab-for="' + tab + '"][data-page="' + page + '"]')
        document.querySelectorAll('[data-tab="' + tab + '"]').forEach(function (i) {
            i.classList.remove('active')
        })
        document.querySelectorAll('[data-tab-for="' + tab + '"]').forEach(function (i) {
            i.classList.add('hidden')
        })
        item.classList.add('active')
        target.classList.remove('hidden')
    })
})
// end: Tab



// start: Chart
new Chart(document.getElementById('order-chart'), {
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
            },
        ]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function generateNDays(n) {
    const data = []
    for(let i=0; i<n; i++) {
        const date = new Date()
        date.setDate(date.getDate()-i)
        data.push(date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric'
        }))
    }
    return data
}
function generateRandomData(n) {
    const data = []
    for(let i=0; i<n; i++) {
        data.push(Math.round(Math.random() * 10))
    }
    return data
}
// end: Chart
