(function (Drupal, drupalSettings) {
    Drupal.behaviors.chartBarOrders = {
        attach: function (context, settings) {
    
        console.log('chartBarOrders behavior loaded');
        console.log('ordersPerDay Settings:',drupalSettings.user_profile_sales.ordersPerDay);


        const total_orders = drupalSettings.user_profile_sales.ordersPerDay;
        

        const ctx = document.getElementById("chart-orders-bar").getContext('2d');
        if (ctx && total_orders) {
            // Extract labels (assume both weeks use same days for simplicity)
            const labels = total_orders.map(item => item.day);
            const industrytype = total_orders.map(item => item.industrytype);
            const totalSales = total_orders.map(item => item.total);

            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: industrytype,
                    datasets: [ {
                        label: 'Orders Per Day',
                        data: totalSales,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                    }]
                },
                options: { 
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                        }
                    }, 
                }
            });
        }
    }
};
})(Drupal, drupalSettings);
