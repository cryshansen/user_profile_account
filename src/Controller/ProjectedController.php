<?php
namespace Drupal\user_profile_account\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Database\Database;
use Drupal\Core\Pager\PagerManagerInterface;
/**
 * Provides route responses for the Welcome page module.
 */
class ProjecteddController extends ControllerBase {

  /**
   * Returns Modules and Suggested products available for purchase based on the user license.
   *
   * @return array
   *   return of product add ons available plus additional suggestions based on the user license and areas of interest.
   */
  public function saveProjectedSales(Request $request) {
    $data = json_decode($request->getContent(), true);
    foreach ($data as $item) {
      \Drupal::database()->merge('projected_sales')
        ->key(['sales_date' => $item['sales_date']])
        ->fields(['projected_sales' => $item['projected_sales']])
        ->execute();
    }
    return new JsonResponse(['status' => 'ok']);
  }
  


}