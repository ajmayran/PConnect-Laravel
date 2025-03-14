document.addEventListener('DOMContentLoaded', function() {
    // Apply dashboard data to DOM elements
    updateDashboardSummary();

    // Initialize charts
    initializeSalesChart();
    initializeOrdersChart();
    initializeCartChart();

    // Populate top products table
    populateTopProductsTable();

    // Add event listener for sales period selector
    document.getElementById('sales-period-selector')?.addEventListener('change', function() {
        // In a real implementation, this would fetch new data or filter existing data
        // For now, we'll just simulate a loading state and reuse the same data
        simulateChartLoading('productSalesChart');
    });
});

// Update summary metrics
function updateDashboardSummary() {
    if (!dashboardData) return;

    // Format numbers with commas and decimal places
    const formatCurrency = (value) => {
        return '₱' + parseFloat(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    const formatNumber = (value) => {
        return parseInt(value || 0).toLocaleString('en-PH');
    };

    // Update summary values
    document.getElementById('total-sales-value').textContent = formatCurrency(dashboardData.totalSales);
    document.getElementById('total-orders-value').textContent = formatNumber(dashboardData.totalOrders);
    document.getElementById('total-products-value').textContent = formatNumber(dashboardData.totalProducts);
    document.getElementById('total-customers-value').textContent = formatNumber(dashboardData.totalCustomers);

    // Update order status counts
    document.getElementById('completed-orders').textContent = formatNumber(dashboardData.orderStatuses?.completed);
    document.getElementById('pending-orders').textContent = formatNumber(dashboardData.orderStatuses?.pending);
    document.getElementById('cancelled-orders').textContent = formatNumber(dashboardData.orderStatuses?.cancelled);
}

// Initialize Sales Chart
function initializeSalesChart() {
    const ctx = document.getElementById('productSalesChart');
    if (!ctx) return;

    // Set common Chart.js defaults
    Chart.defaults.font.family = "'Figtree', sans-serif";
    Chart.defaults.color = '#6B7280';
    Chart.defaults.scale.grid.color = 'rgba(243, 244, 246, 1)';
    Chart.defaults.scale.grid.borderColor = 'rgba(243, 244, 246, 1)';

    let labels = [];
    let data = [];

    // If backend data is available, use it
    if (dashboardData && dashboardData.salesData) {
        labels = Object.keys(dashboardData.salesData);
        data = Object.values(dashboardData.salesData);
    } else {
        // Fallback to dummy data if no backend data
        labels = generateDateLabels(30);
        data = generateRandomData(30, 1000, 5000);
    }

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(39, 174, 96, 0.3)');
    gradient.addColorStop(1, 'rgba(39, 174, 96, 0)');

    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales (₱)',
                data: data,
                borderColor: '#27AE60',
                backgroundColor: gradient,
                tension: 0.4,
                borderWidth: 2,
                pointBackgroundColor: '#27AE60',
                pointBorderColor: 'rgba(255, 255, 255, 0.8)',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#111827',
                    bodyColor: '#4B5563',
                    borderColor: '#E5E7EB',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.raw.toLocaleString('en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0,
                        maxTicksLimit: 7,
                        callback: function(value, index) {
                            // Show fewer labels on mobile
                            const isMobile = window.innerWidth < 768;
                            if (isMobile) {
                                return index % 5 === 0 ? this.getLabelForValue(value) : '';
                            }
                            return index % 3 === 0 ? this.getLabelForValue(value) : '';
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-PH');
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Store chart instance for later updates
    window.salesChart = salesChart;
}

// Initialize Orders Chart
function initializeOrdersChart() {
    const ctx = document.getElementById('chartOrders');
    if (!ctx) return;

    let labels = [];
    let completedData = [];
    let pendingData = [];
    let cancelledData = [];

    // If backend data is available, use it
    if (dashboardData && dashboardData.orderData) {
        // Get all dates across all statuses
        const allDates = new Set();
        for (const status in dashboardData.orderData) {
            Object.keys(dashboardData.orderData[status]).forEach(date => allDates.add(date));
        }

        // Sort dates
        labels = Array.from(allDates).sort();

        // Fill data arrays
        labels.forEach(date => {
            completedData.push(dashboardData.orderData.completed[date] || 0);
            pendingData.push(dashboardData.orderData.pending[date] || 0);
            cancelledData.push(dashboardData.orderData.cancelled[date] || 0);
        });
    } else {
        // Fallback to dummy data
        labels = generateDateLabels(14);
        completedData = generateRandomData(14, 5, 20);
        pendingData = generateRandomData(14, 1, 10);
        cancelledData = generateRandomData(14, 0, 5);
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Completed',
                    data: completedData,
                    backgroundColor: 'rgba(39, 174, 96, 0.7)',
                    borderColor: '#27AE60',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                },
                {
                    label: 'Pending',
                    data: pendingData,
                    backgroundColor: 'rgba(241, 196, 15, 0.7)',
                    borderColor: '#F1C40F',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                },
                {
                    label: 'Cancelled',
                    data: cancelledData,
                    backgroundColor: 'rgba(231, 76, 60, 0.7)',
                    borderColor: '#E74C3C',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        usePointStyle: true,
                        pointStyle: 'rect',
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#111827',
                    bodyColor: '#4B5563',
                    borderColor: '#E5E7EB',
                    borderWidth: 1,
                    padding: 10
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 7
                    }
                },
                y: {
                    stacked: false,
                    beginAtZero: true
                }
            },
            barPercentage: 0.6
        }
    });
}

// Initialize Customer Engagement / Cart Additions Chart
function initializeCartChart() {
    const ctx = document.getElementById('addToCartChart');
    if (!ctx) return;

    let labels = [];
    let data = [];

    // If backend data is available, use it
    if (dashboardData && dashboardData.cartData) {
        labels = Object.keys(dashboardData.cartData);
        data = Object.values(dashboardData.cartData);
    } else {
        // Fallback to dummy data
        labels = generateDateLabels(14);
        data = generateRandomData(14, 5, 30);
    }

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(106, 90, 205, 0.6)');
    gradient.addColorStop(1, 'rgba(106, 90, 205, 0.1)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cart Additions',
                data: data,
                borderColor: 'rgba(106, 90, 205, 1)',
                backgroundColor: gradient,
                tension: 0.3,
                fill: true,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: 'rgba(106, 90, 205, 1)',
                pointBorderColor: 'white',
                pointBorderWidth: 1,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#111827',
                    bodyColor: '#4B5563',
                    borderColor: '#E5E7EB',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 5
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    suggestedMax: Math.max(...data) * 1.1
                }
            }
        }
    });
}

// Populate top products table
function populateTopProductsTable() {
    const tableBody = document.getElementById('top-products-table');
    if (!tableBody || !dashboardData || !dashboardData.topProducts) return;

    // Clear loading placeholders
    tableBody.innerHTML = '';

    // Format currency
    const formatCurrency = (value) => {
        return '₱' + parseFloat(value).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    // Add products to table
    dashboardData.topProducts.forEach(product => {
        const row = document.createElement('tr');
        row.className = 'transition-colors border-b hover:bg-gray-50';

        // Image
        const imgCell = document.createElement('td');
        imgCell.className = 'py-3 pl-3';

        const img = document.createElement('img');
        img.src = product.image || '/storage/products/placeholder.png'; // Fallback image
        img.className = 'object-cover w-12 h-12 rounded-md';
        img.alt = product.name;
        img.onerror = function() {
            this.src = '/storage/products/placeholder.png';
        };
        imgCell.appendChild(img);

        // Product name
        const nameCell = document.createElement('td');
        nameCell.className = 'py-3 font-medium';
        nameCell.textContent = product.name;

        // Category
        const catCell = document.createElement('td');
        catCell.className = 'py-3';
        const catSpan = document.createElement('span');
        catSpan.className = 'px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full';
        catSpan.textContent = product.category || 'Uncategorized';
        catCell.appendChild(catSpan);

        // Quantity sold
        const soldCell = document.createElement('td');
        soldCell.className = 'py-3';
        soldCell.textContent = product.total_sold;

        // Revenue
        const revenueCell = document.createElement('td');
        revenueCell.className = 'py-3 pr-3 font-medium text-right text-green-600';
        revenueCell.textContent = formatCurrency(product.total_revenue);

        row.appendChild(imgCell);
        row.appendChild(nameCell);
        row.appendChild(catCell);
        row.appendChild(soldCell);
        row.appendChild(revenueCell);

        tableBody.appendChild(row);
    });

    // If no products, show message
    if (dashboardData.topProducts.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 5;
        cell.className = 'py-8 text-center text-gray-500';
        cell.textContent = 'No products sold in the selected period';
        row.appendChild(cell);
        tableBody.appendChild(row);
    }
}

// Helper function to simulate chart reloading
function simulateChartLoading(chartId) {
    const ctx = document.getElementById(chartId);
    if (!ctx) return;

    // Add loading state
    ctx.parentNode.classList.add('opacity-50');

    // Wait a bit and restore
    setTimeout(() => {
        // For a real implementation, this would fetch new data
        // and update the chart with updateChart(chartId, newData)
        ctx.parentNode.classList.remove('opacity-50');
    }, 800);
}

// Helper function to generate date labels (for fallback data)
function generateDateLabels(days) {
    const labels = [];
    for (let i = days; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toISOString().split('T')[0]);
    }
    return labels;
}

// Helper function to generate random data (for fallback data)
function generateRandomData(count, min, max) {
    const data = [];
    for (let i = 0; i < count; i++) {
        data.push(Math.floor(Math.random() * (max - min + 1)) + min);
    }
    return data;
}

// Detect theme changes for chart color updates
function updateChartsForTheme(isDark) {
    Chart.defaults.color = isDark ? '#D1D5DB' : '#6B7280';
    Chart.defaults.scale.grid.color = isDark ? 'rgba(75, 85, 99, 0.2)' : 'rgba(243, 244, 246, 1)';

    // Update existing charts if needed
    if (window.salesChart) {
        window.salesChart.options.scales.x.grid.color = isDark ? 'rgba(75, 85, 99, 0.2)' : 'rgba(243, 244, 246, 1)';
        window.salesChart.options.scales.y.grid.color = isDark ? 'rgba(75, 85, 99, 0.2)' : 'rgba(243, 244, 246, 1)';
        window.salesChart.update();
    }
}

// Responsive behavior for charts
window.addEventListener('resize', function() {
    // Update charts if needed for better mobile display
    if (window.salesChart) {
        const isMobile = window.innerWidth < 768;
        window.salesChart.options.scales.x.ticks.maxTicksLimit = isMobile ? 5 : 7;
        window.salesChart.update();
    }
});
