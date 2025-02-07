    // Product Sales Chart
    const productSalesCtx = document.getElementById('productSalesChart').getContext('2d');
    new Chart(productSalesCtx, {
      type: 'line',
      data: {
        labels: ['7-10','8-10', '9-10', '10-10', '11-10'],
        datasets: [{
          label: 'Last Month',
          data: [829, 721, 920, 1050, 1250],
          borderColor: '#ABEBC6',
          backgroundColor: '#27AE60',
        }]
      },
    });

    // Add to Cart Chart
    const addToCartCtx = document.getElementById('addToCartChart').getContext('2d');
    new Chart(addToCartCtx, {
      type: 'line',
      data: {
        labels: ['7-10','8-10', '9-10', '10-10','11-11'],
        datasets: [{
          label: 'This Month',
          data: [120, 142, 199, 164, 255],
          borderColor: '#ABEBC6',
          backgroundColor: '#27AE60',
        }]
      },
    });

    // Checkout Chart
    const checkoutCtx = document.getElementById('checkoutChart').getContext('2d');
    new Chart(checkoutCtx, {
      type: 'bar',
      data: {
        labels: ['7-10','8-10', '9-10', '10-10', '11-10'],
        datasets: [{
          label: 'Sales',
          data: [110, 124, 155, 145, 198],
          borderColor: '#ABEBC6',
          backgroundColor: '#27AE60',
        }]
      },
    });

    // Chart Orders
    const chartOrdersCtx = document.getElementById('chartOrders').getContext('2d');
    new Chart(chartOrdersCtx, {
      type: 'line',
      data: {
        labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct','Nov'],
        datasets: [{
          label: 'Data',
          data: [250, 405, 723, 620, 956, 713, 532, 602],
          borderColor: '#ABEBC6',
          backgroundColor: '#27AE60',
          tension: 0.5
        }]
      },
    });

    // Income Chart
    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    new Chart(incomeCtx, {
      type: 'line',
      data: {
        labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
        datasets: [{
          label: 'Income',
          data: [10230, 12500, 11000, 15200, 17200, 13000, 11500, 12500],
          borderColor: '#ABEBC6',
          backgroundColor: '#27AE60',
        }]
      },
    });
