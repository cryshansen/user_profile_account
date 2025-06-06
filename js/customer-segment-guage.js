

(function (Drupal, drupalSettings) {
  Drupal.behaviors.chartCustomerSegment = {
    attach: function (context, settings) {
      
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
