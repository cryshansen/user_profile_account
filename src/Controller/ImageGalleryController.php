<?php
namespace Drupal\user_profile_account\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Welcome page module.
 */
class ImageGalleryController extends ControllerBase {

  /**
   * Returns Modules and Suggested products available for purchase based on the user license.
   *
   * @return array
   *   return of product add ons available plus additional suggestions based on the user license and areas of interest.
   */
  public function galleries(string $user) {
	 
    return array (
        '#theme' => 'user_profile_gallery',
      );

  }

}