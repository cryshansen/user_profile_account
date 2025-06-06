<?php

namespace Drupal\user_profile_account\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;
use Drupal\Core\Render\Markup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Drupal\Core\Url;

/**
 * Controller for user profile.
 */
class UserProfileController extends ControllerBase {

  /**
   * Render the user profile.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user entity.
   *
   * @return array
   *   Render array for the profile page.
   */
  public function view(UserInterface $user) {
    // Load the user information display view mode.
    $user_view = $this->entityTypeManager()->getViewBuilder('user')->view($user, 'full');
     
    \Drupal::logger('user_profile_account')->debug('Rendering profile for user ID: ' . $user->id());

    $user_profile_url = Url::fromRoute('user_profile_account.profile', ['user' => $user->id()])->toString();
     
    \Drupal::logger('user_profile_account')->debug('Rendering user profile url: ' . $user_profile_url);

    // Build the payment form. You might have a custom form class for this.
    //$payment_form = \Drupal::formBuilder()->getForm('Drupal\user_profile_account\Form\CreditCardForm');

    return [
      '#theme' => 'user_profile',
      '#user' => $user,
      '#user_view' => $user_view,
      '#payment_form' => null, //$payment_form,
    ];
  }
}