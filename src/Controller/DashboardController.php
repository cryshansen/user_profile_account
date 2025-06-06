<?php
namespace Drupal\user_profile_account\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Database\Database;
use Drupal\Core\Pager\PagerManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Provides route responses for the Welcome page module.
 */
class DashboardController extends ControllerBase {

  /**
   * Returns Modules and Suggested products available for purchase based on the user license.
   *
   * @return array
   *   return of product add ons available plus additional suggestions based on the user license and areas of interest.
   */
  public function dashboard(string $user) {


    if ($user == 1) {
      /*
      $url = \Drupal::url('user_profile_account.dashboard_demo');
      return new RedirectResponse($url);
      */
      return $this->dashboardDemo();
    }

    $limit = 25; // Default number of rows per page
    //$limit = \Drupal::request()->query->get('limit') ?? 25;


    // needs a select all with paging to list 10/25/50 table records
    /*
    $con = Database::getConnection('default','default');
		$query = $con->select('usca_sales_data_regional_pricing', 'up');
    $query->fields('up', array( 'id','customer_name','email', 'industrytype','purchasedate','city','region','country','price_USD','payment_method','customer_segment','referral_source') );
    // Add pagination
    $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit);

    $query->orderBy('purchasedate', 'DESC');//->orderBy('node. created', 'ASC');
    $full_data_table = $query->execute()->fetchAll();
  */
    $data2 = $this->getWeeklySalesData(); //linear
    $monthly=$this->getMonthlySalesData();//linear
    $total_orders = $this->getDailyTotalOrdersData(); //barchart
    $industry_trend = $this->getIndustryTrendData();  //donut
    $revenue_trend =$this->getRevenueTrendData(); //revenue type linear customer-segment-guage
    $customer_segment = $this->getCustomerSegmentData();
    $yourGaugeData =$this->getGrowthProjectionData(); //guage some growth projection?
//  'theme' => 'weekly_sales_dashboard',



    return array (
        '#theme' => 'user_profile_dasboard',
//        '#salestbl'  => $full_data_table, //full table display
        '#current_week' => $data2['current_week_sales'],
        '#previous_week' => $data2['previous_week_sales'],
        '#attached' => [
            'library' => [
              'user_profile_account/chart', // This is where Chart.js is loaded
            ],
            'drupalSettings' => [
              'user_profile_sales' => [
                'current_month' => $monthly['current_month'],
                'previous_month' => $monthly['previous_month'],   
                'currentWeek' => $data2['current_week_sales'],
                'previousWeek' => $data2['previous_week_sales'],
                'ordersPerDay' => $total_orders,
                'industryPercent' => $industry_trend,
                'revenueTrend' => $revenue_trend,
                'customerSegment' => $customer_segment,
                'paidVsOwing' => $yourGaugeData,
              ],
            ],
          ],
          'pager' => [
            '#type' => 'pager',
          ],
      );

  }
 

  public function monthlyDashboard(string $user) {
    $monthly=$this->getMonthlySalesData();//linear

    return array (
      '#theme' => 'user_profile_month_dasboard',
      '#attached' => [
          'library' => [
            'user_profile_account/monthlychart', // This is where Chart.js is loaded
          ],
          'drupalSettings' => [
            'user_profile_sales' => [
              'current_month' => $monthly['current_month'],
              'previous_month' => $monthly['previous_month'],   
            ],
          ],
        ],
    );


  }
  public function weeklySalesDashboard(string $user){
    $weekly = $this->getWeeklySalesData(); //linear  $data2 = $this->getWeeklySalesData(); //linear

     return array (
        '#theme' => 'user_profile_weekly_dasboard',
        '#attached' => [
            'library' => [
              'user_profile_account/weeklychart', // This is where Chart.js is loaded
            ],
            'drupalSettings' => [
              'user_profile_sales' => [
                  'currentWeek' => $data2['current_week_sales'],
                  'previousWeek' => $data2['previous_week_sales'],
                ],
            ],
        ],
      );

  }
  
  public function ordersPerDayDashboard(string $user) {

    $total_orders = $this->getDailyTotalOrdersData(); //barchart
    return array (
      '#theme' => 'user_profile_orderday_dasboard',
      '#attached' => [
          'library' => [
            'user_profile_account/industry-typeday', // This is where Chart.js is loaded
          ],
          'drupalSettings' => [
            'user_profile_sales' => [ 
              'ordersPerDay' => $total_orders,
            ],
          ],
        ],
    );

  }
/** donut  */
  public function industryTrendDashboard(string $user) {
    $industry_trend = $this->getIndustryTrendData();  //donut
    return array (
      '#theme' => 'user_profile_industry_trend_dasboard',
      '#attached' => [
          'library' => [
            'user_profile_account/industry-trend', // This is where Chart.js is loaded 
          ],
          'drupalSettings' => [
            'user_profile_sales' => [ 
              'industryPercent' => $industry_trend,
            ],
          ],
        ],
    );

  }
  public function customerSegmentDashboard(string $user){
    $customer_segment = $this->getCustomerSegmentData();

    return array (
      '#theme' => 'user_profile_customer_trend_dasboard',
      '#attached' => [
          'library' => [
            'user_profile_account/customer-segment-guage', // This is where Chart.js is loaded 
          ],
          'drupalSettings' => [
            'user_profile_sales' => [ 
              'customerSegment' => $customer_segment,
            ],
          ],
        ],
    );


  }
/** donut  */
  public function revenueTrendDashboard(string $user){
    $revenue_trend = $this->getRevenueTrendData();
   
    return array (
      '#theme' => 'user_profile_revenu_trend_dasboard',
      '#attached' => [
          'library' => [
            'user_profile_account/revenue-trend', // This is where Chart.js is loaded 
          ],
          'drupalSettings' => [
            'user_profile_sales' => [ 
              'revenueTrend' => $revenue_trend,
            ],
          ],
        ],
      );

  }

  
  public function getWeeklySalesData() {
      $connection = \Drupal::database();
    
      // Calculate dates
      $today = date('Y-m-d');
      $last_week = date('Y-m-d', strtotime('-7 days'));
      $week_before = date('Y-m-d', strtotime('-14 days'));
    
      // Current week: last 7 days
      $query1 = $connection->select('usca_sales_data_regional_pricing', 's');
      $query1->addExpression("DATE(purchasedate)", 'day');
      $query1->addExpression('SUM(price_USD)', 'total_sales');
      $query1->addExpression('COUNT(*)', 'sale_count');
      $query1->condition('purchasedate', $last_week, '>=');
      $query1->condition('purchasedate', $today, '<');
      $query1->groupBy('day');
      $query1->orderBy('day', 'ASC');
      $current_week = $query1->execute()->fetchAll();
    
      // Previous week: 14–8 days ago
      $query2 = $connection->select('usca_sales_data_regional_pricing', 's');
      $query2->addExpression("DATE(purchasedate)", 'day');
      $query2->addExpression('SUM(price_USD)', 'total_sales');
      $query2->addExpression('COUNT(*)', 'sale_count');
      $query2->condition('purchasedate', $week_before, '>=');
      $query2->condition('purchasedate', $last_week, '<');
      $query2->groupBy('day');
      $query2->orderBy('day', 'ASC');
      $previous_week = $query2->execute()->fetchAll();
    
      return [
        'current_week_sales' => $current_week,
        'previous_week_sales' => $previous_week,
      ];  

  }

  public function getMonthlySalesData() {
    $connection = \Drupal::database();
  
    // Calculate dates
    $today = date('Y-m-d');
    $this_month = date('Y-m-d', strtotime('-30 days'));
    $last_month = date('Y-m-d', strtotime('-60 days'));
  
    // Current week: last 7 days
    $query1 = $connection->select('usca_sales_data_regional_pricing', 's');
    $query1->addExpression("DATE(purchasedate)", 'day');
    $query1->addExpression('SUM(price_USD)', 'total_sales');
    $query1->addExpression('COUNT(*)', 'sale_count');
    $query1->condition('purchasedate', $this_month, '>=');
    $query1->condition('purchasedate', $today, '<=');
    $query1->groupBy('day');
    $query1->orderBy('day', 'ASC');
    $current_month = $query1->execute()->fetchAll();
  
    // Previous week: 14–8 days ago
    $query2 = $connection->select('usca_sales_data_regional_pricing', 's');
    $query2->addExpression("DATE(purchasedate)", 'day');
    $query2->addExpression('SUM(price_USD)', 'total_sales');
    $query2->addExpression('COUNT(*)', 'sale_count');
    $query2->condition('purchasedate', $last_month, '>=');
    $query2->condition('purchasedate', $this_month, '<');
    $query2->groupBy('day');
    $query2->orderBy('day', 'ASC');
    $previous_month = $query2->execute()->fetchAll();
  
    return [
      'current_month' => $current_month,
      'previous_month' => $previous_month,
    ];  

}


  public function  getDailyTotalOrdersData(){ 

    // needs a select all with paging to list 10/25/50 table records
    $connection = Database::getConnection('default','default');
    // Calculate dates
    $today = date('Y-m-d');
    $last_week = date('Y-m-d', strtotime('-7 days'));
    $week_before = date('Y-m-d', strtotime('-14 days')); //use for second bar chart data too commpare
    //SELECT COUNT(*) as total,industrytype,DATE(purchasedate) as day FROM `drqd_usca_sales_data_regional_pricing` WHERE purchasedate >= NOW() + INTERVAL -7 DAY AND purchasedate < NOW() + INTERVAL 0 DAY group by industrytype order by purchasedate DESC;    // Current week: last 7 days
    $query1 = $connection->select('usca_sales_data_regional_pricing', 's');
    $query1->addExpression('COUNT(*)', 'total');
    $query1->addExpression("DATE(purchasedate)", 'day');
    $query1->fields('s',['industrytype']);
    $query1->condition('purchasedate', $last_week, '>=');
    $query1->condition('purchasedate', $today, '<=');
    $query1->groupBy('industrytype');
    $query1->groupBy('day');
    $query1->orderBy('industrytype', 'ASC');
    $query1->orderBy('day', 'DESC');
    $total_orders = $query1->execute()->fetchAll();

    return  $total_orders; 

  }

  public function getIndustryTrendData(){
     // needs a select all with paging to list 10/25/50 table records
    $connection = Database::getConnection('default','default');
    // Calculate dates
    $today = date('Y-m-d');
    $last_week = date('Y-m-d', strtotime('-7 days'));

  
    $query1 = $connection->select('usca_sales_data_regional_pricing', 's');
    $query1->addExpression('COUNT(*)', 'total');
    $query1->fields('s', ['industrytype']);
    $query1->condition('purchasedate', $last_week, '>=');
    $query1->condition('purchasedate', $today, '<=');
    $query1->groupBy('industrytype');
    $query1->orderBy('total', 'DESC');
    $current_week = $query1->execute()->fetchAll();

    return  $current_week; 

  }

  /** donut referral source */
  public function getRevenueTrendData(){
 
    // needs a select all with paging to list 10/25/50 table records
    $connection = Database::getConnection('default','default');
    // Calculate dates
    $today = date('Y-m-d');
    $last_week = date('Y-m-d', strtotime('-7 days'));
    $week_before = date('Y-m-d', strtotime('-14 days'));

    // Current week: last 7 days
    $query1 = $connection->select('usca_sales_data_regional_pricing', 's');
    $query1->addExpression('COUNT(*)', 'total');
    $query1->fields('s', ['referral_source']);
    $query1->condition('purchasedate', $last_week, '>=');
    $query1->condition('purchasedate', $today, '<=');
    $query1->groupBy('referral_source');
    $query1->orderBy('referral_source', 'ASC');
    $referral_source = $query1->execute()->fetchAll();

    return $referral_source;
  }

/** guage  */
  public function getCustomerSegmentData(){
    // needs a select all with paging to list 10/25/50 table records
    $connection = Database::getConnection('default','default');
    // Calculate dates
    $today = date('Y-m-d');
    $last_week = date('Y-m-d', strtotime('-7 days'));
    //$week_before = date('Y-m-d', strtotime('-14 days'));
    // Current week: last 7 days
    $query1 = $connection->select('usca_sales_data_regional_pricing', 's');
    $query1->addExpression('COUNT(*)', 'total');
    $query1->fields('s', ['customer_segment']);
    $query1->condition('purchasedate', $last_week, '>=');
    $query1->condition('purchasedate', $today, '<=');
    $query1->groupBy('customer_segment');
    $query1->orderBy('customer_segment', 'ASC');
    $customer_segment = $query1->execute()->fetchAll();


    return $customer_segment;

  }
  //some growth prrojection
  public function getGrowthProjectionData(){

    
    // needs a select all with paging to list 10/25/50 table records
    $connection = Database::getConnection('default','default');

    // Calculate dates
    $today = date('Y-m-d');
    $last_week = date('Y-m-d', strtotime('-7 days'));
    $week_before = date('Y-m-d', strtotime('-14 days'));
  
    // Current week: last 7 days
    $query1 = $connection->select('usca_sales_data_regional_pricing', 's');
    $query1->addExpression("DATE(purchasedate)", 'day');
    $query1->addExpression('SUM(price_USD)', 'total_sales');
    $query1->addExpression('COUNT(*)', 'sale_count');
    $query1->condition('purchasedate', $last_week, '>=');
    $query1->condition('purchasedate', $today, '<=');
    $query1->groupBy('day');
    $query1->orderBy('day', 'ASC');
    $current_week = $query1->execute()->fetchAll();

  } 
  public function getFullSalesData(string $user) {
    // needs a select all with paging to list 10/25/50 table records
    $con = Database::getConnection('default','default');
    $query = $con->select('usca_sales_data_regional_pricing', 'up');
    $query->fields('up', array( 'id','customer_name','email', 'industrytype','purchasedate','city','region','country','price_USD','payment_method','customer_segment','referral_source') );
    // Add pagination
    $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit);

    $query->orderBy('purchasedate', 'DESC');//->orderBy('node. created', 'ASC');
    $full_data_table = $query->execute()->fetchAll();
    return $full_data_table;

  }

  public function dashboardDemo() {
    

   
    $data2 = $this->getWeeklySalesData(); //linear
    $monthly=$this->getMonthlySalesData();//linear
    $total_orders = $this->getDailyTotalOrdersData(); //barchart
    $industry_trend = $this->getIndustryTrendData();  //donut
    $revenue_trend =$this->getRevenueTrendData(); //revenue type linear customer-segment-guage
    $customer_segment = $this->getCustomerSegmentData();
    $yourGaugeData =$this->getGrowthProjectionData(); //guage some growth projection?
//  'theme' => 'weekly_sales_dashboard',



    return array (
        '#theme' => 'user_profile_public_dasboard',
        '#salestbl'  => $full_data_table, //full table display
        '#current_week' => $data2['current_week_sales'],
        '#previous_week' => $data2['previous_week_sales'],
        '#attached' => [
            'library' => [
              'user_profile_account/chart', // This is where Chart.js is loaded
            ],
            'drupalSettings' => [
              'user_profile_sales' => [
                'current_month' => $monthly['current_month'],
                'previous_month' => $monthly['previous_month'],   
                'currentWeek' => $data2['current_week_sales'],
                'previousWeek' => $data2['previous_week_sales'],
                'ordersPerDay' => $total_orders,
                'industryPercent' => $industry_trend,
                'revenueTrend' => $revenue_trend,
                'customerSegment' => $customer_segment,
                'paidVsOwing' => $yourGaugeData,
              ],
            ],
          ],
          'pager' => [
            '#type' => 'pager',
          ],
      );

  }
  



}