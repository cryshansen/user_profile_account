

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

  
  Drupal.behaviors.chartBarOrders = {
    attach: function (context, settings) {
  
      console.log('chartBarOrders behavior loaded');
      console.log('ordersPerDay Settings:',drupalSettings.user_profile_sales.ordersPerDay);


      const total_orders = drupalSettings.user_profile_sales.ordersPerDay;
      

      const ctx = document.getElementById("chart-orders-bar");
      if (ctx && total_orders) {


        // Extract labels (assume both weeks use same days for simplicity)
        const labels = total_orders.map(item => item.day);
        const industrytype = total_orders.map(item => item.industrytype);
        const totalSales = total_orders.map(item => item.total);

        new Chart(ctx, {
          type: "bar",
          data: {
            labels: industrytype,
            datasets: [
              {
                label: 'Orders Per Day',
                data: totalSales,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0
              }
            ]
          },
          options: { responsive: true }
        });
      }
    }
  };

  Drupal.behaviors.chartDonutIndustries = {
    attach: function (context, settings) {
      console.log('Sales chart behavior loaded');
      console.log('Settings:', drupalSettings.user_profile_sales);
      const data = drupalSettings.user_profile_sales.industryPercent;
      // Extract labels (assume both weeks use same days for simplicity)
     

      const ctx = document.getElementById("chart-industry-donut");
      if (ctx && data) {

        const labels = data.map(item => item.day);
        const industrytype = data.map(item => item.industrytype);
        const totalSales = data.map(item => item.total);

        
        new Chart(ctx, {
          type: "doughnut",
          data: {
            labels: industrytype,
            datasets: [
              {
                label: 'Sales by Type',
                data: totalSales,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0
              }
            ]
          },
          options: { responsive: true }
        });
      }
    }
  };

  Drupal.behaviors.chartLineRevenue = {
    attach: function (context, settings) {
      console.log('Sales chart behavior loaded');
      console.log('Settings:', drupalSettings.user_profile_sales);
      const data =drupalSettings.user_profile_sales.revenueTrend;
      const ctx = document.getElementById("chart-revenue-line");
      if (ctx && data) {
        new Chart(ctx, {
          type: "line",
          data: data,
          options: { responsive: true }
        });
      }
    }
  };

  Drupal.behaviors.chartGaugePaid = {
    attach: function (context, settings) {
      console.log('chartGaugePaid chart behavior loaded');
      console.log('chartGaugePaid Settings:', drupalSettings.user_profile_sales);
      const data = drupalSettings.user_profile_sales.paidVsOwing;
      const ctx = document.getElementById("chart-paid-gauge");
      if (ctx && data) {
        new Chart(ctx, {
          type: "doughnut", // mimic gauge with rotation
          data: data,
          options: {
            rotation: -Math.PI,
            circumference: Math.PI,
            responsive: true
          }
        });
      }
    }
  };
  
  /**try to override dashboard.js for the monthly sales*/
  Drupal.behaviors.monthlySales = {
    attach: function (context, settings) {

          console.log('monthlySales chart behavior loaded');
          console.log('monthlySales  current settins:', drupalSettings.user_profile_sales.current_month);
          console.log('monthlySales previous settins:', drupalSettings.user_profile_sales.previous_month);

          // Graphs
          const ctx2 = document.getElementById('myChart');
          console.log(ctx2);

          if (!ctx2 || ctx2.dataset.rendered) return;
          ctx2.dataset.rendered = true;

          const currentMonth = drupalSettings.user_profile_sales.current_month;
          const previousMonth = drupalSettings.user_profile_sales.previous_month;
    
          // Extract labels (assume both weeks use same days for simplicity)
          const labels = currentMonth.map(item => item.day);
          const currentSales = currentMonth.map(item => item.total_sales);
          const previousSales = previousMonth.map(item => item.total_sales);

          // eslint-disable-next-line no-unused-vars
          new Chart(ctx2, {
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
