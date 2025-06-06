<?php

namespace Drupal\user_profile_account\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;
use Drupal\Core\Render\Markup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for user profile.
 */
class Payment extends ControllerBase {

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
    //$user_view = $this->entityTypeManager()->getViewBuilder('user')->view($user, 'full');
    //$user_profile_url = Url::fromRoute('user_profile_account.profile', ['user' => $user->id()])->toString();
    // Build the payment form. You might have a custom form class for this.
    $payment_form = \Drupal::formBuilder()->getForm('Drupal\user_profile_account\Form\PaymentForm');

    return [
      '#theme' => 'user_profile',
      '#user' => $user,
      '#user_view' => $user_view,
      '#payment_form' => $payment_form,
    ];
  }
}