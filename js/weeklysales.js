

(function (Drupal, drupalSettings) {
  Drupal.behaviors.salesComparisonChart = {
    attach: function (context, settings) {

      console.log('Sales chart behavior loaded');
      console.log('sales:', drupalSettings.user_profile_sales);
    /** ordersPerDay  */
    /** industryPercent */
    /** revenueTrend */
    /** customer segment chart */
    /** weekly sales  */
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


    /** ordersPerDay  */

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

    /** industryPercent */

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
    /** revenueTrend */
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


    /** customer segment chart */

      const customer_segdata = drupalSettings.user_profile_sales.customerSegment;

      const ctx_customer_seg = document.getElementById("chart-customer-seg");

       if (!ctx_customer_seg || ctx_customer_seg.dataset.rendered) return;
       ctx_customer_seg.dataset.rendered = true;

      if (ctx_customer_seg && customer_segdata) {

        const segmenttype = customer_segdata.map(item => item.customer_segment);
        const totalSegmentSales = customer_segdata.map(item => item.total);
  

        console.log('chartGaugePaid chart behavior loaded');
        console.log('chartCustomerSeg customerSegment:', drupalSettings.user_profile_sales.customerSegment);
        //customerTotal

        new Chart(ctx_customer_seg, {
          type: "doughnut", // mimic gauge with rotation
          data: {
            labels: segmenttype,
            datasets: [
              {
                label: 'Sales by New Custommer Segment',
                data: totalSegmentSales,
                backgroundColor: [ 'transparent', 'rgba(54, 162, 235, 0.5)', 'transparent','transparent',],
                borderColor: ['rgba(0, 0, 0, .6)','rgba(54, 162, 235, 1)','rgba(39, 43, 48, 0.6)','rgba(0, 0, 0, .6)'],
                borderWidth: 1,

              },
            ]
          },
          options: {
            rotation: -Math.PI,
            circumference: Math.PI,
            responsive: true
          }
          
        });







      }








    }
  };

})(Drupal, drupalSettings);
