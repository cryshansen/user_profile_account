

(function (Drupal, drupalSettings) {
    Drupal.behaviors.chartDonutReferrals = {
        attach: function (context, settings) {
          console.log('chartDonutIndustries chart behavior loaded');
          console.log('chartDonutIndustries Settings:', drupalSettings.user_profile_sales.revenueTrend);
          const referral_data = drupalSettings.user_profile_sales.revenueTrend;
          // Extract labels (assume both weeks use same days for simplicity)
         
    
          const ctx_referral = document.getElementById("chart-referral-donut");
          if (!ctx_referral || ctx_referral.dataset.rendered) return;
          ctx_referral.dataset.rendered = true;
    
    
    
          if (ctx_referral && referral_data) {

            const referral = referral_data.map(item => item.referral_source);
            const totalSales = referral_data.map(item => item.total);
    
            
            new Chart(ctx_referral, {
              type: "doughnut",
              data: {
                labels: referral,
                datasets: [
                  {
                    label: 'Sales by Referal Type',
                    data: totalSales,
                    backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)',  'rgba(255, 205, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(201, 203, 207, 0.5)',],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0
                  }
                ]
              },
              options: { 
                 responsive: true 
                
              }
            });
          }
        }
      };

})(Drupal, drupalSettings);
