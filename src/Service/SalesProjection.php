<?php

namespace Drupal\user_profile_account\Service;

use Drupal\Core\Database\Database;
use Drupal\Core\Datetime\DrupalDateTime;

class SalesProjection {
  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public function generateFutureSales($days = 30) {
    // 1. Get recent daily sales average (example: from last 30 days)
    $query = $this->database->select('usca_sales_data_regional_pricing', 's')
      ->fields('s', ['purchasedate'])
      ->addExpression('SUM(priceusd)', 'total')
      ->addExpression('DATE(purchasedate)', 'day')
      ->groupBy('day')
      ->orderBy('day', 'DESC')
      ->range(0, 30);
    $results = $query->execute()->fetchAll();

    $totals = array_column($results, 'total');
    $avg = array_sum($totals) / count($totals);
    $growthRate = 1.02; // Example: 2% daily growth

    // 2. Generate and insert future daily projections
    $baseDate = new \DateTime('tomorrow');

    for ($i = 0; $i < $days; $i++) {
      $date = clone $baseDate;
      $date->modify("+$i day");
      $projectedSales = $avg * pow($growthRate, $i);

      // Insert or update
      $this->database->merge('projected_sales')
        ->key(['sales_date' => $date->format('Y-m-d H:i:s')])
        ->fields(['projected_sales' => $projectedSales])
        ->execute();
    }
  }
}