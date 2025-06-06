

(function (Drupal, drupalSettings) {
  /**try to override dashboard.js for the monthly sales*/
  Drupal.behaviors.monthlySales = {
    attach: function (context, settings) {

          const data6 =drupalSettings.user_profile_sales.current_month;
    
          // Graphs
          const ctx6 = document.getElementById('myChart');

          if (!ctx6 || ctx6.dataset.rendered) return;
          ctx6.dataset.rendered = true;
         
          if (ctx6 && data6) {
            console.log('monthlySales chart behavior loaded');
            console.log('monthlySales  current settins:', drupalSettings.user_profile_sales.current_month);
            console.log('monthlySales previous settins:', drupalSettings.user_profile_sales.previous_month);
  
            const currentMonth = drupalSettings.user_profile_sales.current_month;
            const previousMonth = drupalSettings.user_profile_sales.previous_month;
      
            // Extract labels (assume both weeks use same days for simplicity)
            const labels = currentMonth.map(item => item.day);
            
            const allLabels = new Set([
                currentMonth.map(item => item.day),
                previousMonth.map(item => item.day),
            ]);



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
               /* responsive: true,*/
                maintainAspectRatio: true,
                scales: {
                  y: {
                    beginAtZero: false
                  },
                  x: {
                    grid: {
                      borderColor: 'red'
                    }
                  }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                          label: function(context) {
                            const datasetLabel = context.dataset.label || '';
                            const value = context.formattedValue;
                            const label = context.label; // this is your x-axis label (day)
                            return `${datasetLabel}: ${value} on ${label}`;
                          }
                        }
                      },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
              }
            });
        }
      }
    
  };




})(Drupal, drupalSettings);
