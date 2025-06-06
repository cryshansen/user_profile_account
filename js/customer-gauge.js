

(function (Drupal, drupalSettings) {
  
  Drupal.behaviors.chartDonutIndustries = {
    attach: function (context, settings) {
      console.log('chartDonutIndustries chart behavior loaded');
      console.log('chartDonutIndustries Settings:', drupalSettings.user_profile_sales.industryPercent);
      const donut_data = drupalSettings.user_profile_sales.industryPercent;
      // Extract labels (assume both weeks use same days for simplicity)
     

      const ctx_donut = document.getElementById("chart-industry-donut");
      if (ctx_donut && donut_data) {

        const industrytype = donut_data.map(item => item.industrytype);
        const totalSales = donut_data.map(item => item.total);

        
        new Chart(ctx_donut, {
          type: "doughnut",
          data: {
            labels: industrytype,
            datasets: [
              {
                label: 'Sales by Type',
                data: totalSales,
                backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgb(255, 99, 132)',  'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(201, 203, 207)',],
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,

              }
            ]
          },
          options: { 
             responsive: true, 
            
          }
        });
      }
    }
  };



})(Drupal, drupalSettings);
