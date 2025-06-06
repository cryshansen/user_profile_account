

(function (Drupal, drupalSettings) {
  Drupal.behaviors.salesComparisonChart = {
    attach: function (context, settings) {

      console.log('Sales chart behavior loaded');
      console.log('sales:', drupalSettings.user_profile_sales);

      const ctx = document.getElementById('combinedSalesChart');
      if (!ctx || ctx.dataset.rendered) return;
      ctx.dataset.rendered = true;

      const currentWeek = drupalSettings.user_profile_sales.currentWeek;
      const previousWeek = drupalSettings.user_profile_sales.previousWeek;

      // Extract labels (assume both weeks use same days for simplicity)
      const labels = currentWeek.map(item => item.day);
      const currentSales = currentWeek.map(item => item.total_sales);
      const previousSales = previousWeek.map(item => item.total_sales);

      new Chart(ctx, {
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
              fill: false
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
  };

  
  /**try to override dashboard.js for the monthly sales*/
  Drupal.behaviors.monthlySales = {
    attach: function (context, settings) {

          console.log('monthlySales chart behavior loaded');
          console.log('monthlySales  current settins:', drupalSettings.user_profile_sales.current_month);
          console.log('monthlySales previous settins:', drupalSettings.user_profile_sales.previous_month);

          // Graphs
          const ctx6 = document.getElementById('myChart');
          console.log(ctx2);

          if (!ctx6 || ctx6.dataset.rendered) return;
          ctx6.dataset.rendered = true;

          const currentMonth = drupalSettings.user_profile_sales.current_month;
          const previousMonth = drupalSettings.user_profile_sales.previous_month;
    
          // Extract labels (assume both weeks use same days for simplicity)
          const labels = currentMonth.map(item => item.day);
          const currentSales = currentMonth.map(item => item.total_sales);
          const previousSales = previousMonth.map(item => item.total_sales);

          // eslint-disable-next-line no-unused-vars
          new Chart(ctx6, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                  {
                    label: 'Current Month',
                    data: currentSales,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0
                  },
                  {
                    label: 'Previous Month',
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
              scales: {
                yAxes: [{
                  ticks: {
                    beginAtZero: false
                  }
                }]
              },
              legend: {
                display: false
              },
              responsive: true, // Instruct chart js to respond nicely.
              maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height 
            }
          });
        }
    
  };




})(Drupal, drupalSettings);
