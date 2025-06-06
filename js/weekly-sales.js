(function (Drupal, drupalSettings) {
  Drupal.behaviors.salesComparisonChart = {
    attach: function (context, settings) {

      console.log('Sales chart behavior loaded');
      console.log('sales:', drupalSettings.user_profile_sales);
      const weekly_data = drupalSettings.user_profile_sales.currentWeek;
      const ctx_weekly = document.getElementById('combinedSalesChart');
      if (!ctx_weekly || ctx_weekly.dataset.rendered) return;
      ctx_weekly.dataset.rendered = true;

      const currentWeek = drupalSettings.user_profile_sales.currentWeek;
      const previousWeek = drupalSettings.user_profile_sales.previousWeek;
      if (ctx_weekly && currentWeek) {
          // Extract labels (assume both weeks use same days for simplicity)
          const labels = currentWeek.map(item => item.day);
          const currentSales = currentWeek.map(item => item.total_sales);
          const previousSales = previousWeek.map(item => item.total_sales);

          new Chart(ctx_weekly, {
            type: 'line', // or 'bar'
            data: {
              labels: labels,
              datasets: [
                {
                  label: 'Current Week',
                  data: currentSales,
                  backgroundColor: 'rgba(54, 162, 235, 0.5)',
                  borderColor: 'rgba(54, 162, 235, 1)',
                  borderWidth: 2,
                  fill: false,
                  tension: 0
                },
                {
                  label: 'Previous Week',
                  data: previousSales,
                  backgroundColor: 'rgba(255, 206, 86, 0.5)',
                  borderColor: 'rgba(255, 206, 86, 1)',
                  borderWidth: 2,
                  fill: false,
                  tension: 0
                }
              ]
            },
            options: {
              responsive: true,
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          });
        }



    }
  };

  

})(Drupal, drupalSettings);
