<?php

namespace Drupal\user_profile_account\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CreditCardForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'account_credit_card_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Cardholder name
    $form['cardholder_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cardholder Name'),
      '#required' => TRUE,
    ];

    // Card number
    $form['card_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Card Number'),
      '#required' => TRUE,
      '#attributes' => [
        'maxlength' => 16,
        'pattern' => '[0-9]*',
      ],
    ];

    // Expiration date (month)
    $form['expiration_month'] = [
      '#type' => 'select',
      '#title' => $this->t('Expiration Month'),
      '#options' => array_combine(range(1, 12), range(1, 12)),
      '#required' => TRUE,
    ];

    // Expiration date (year)
    $form['expiration_year'] = [
      '#type' => 'select',
      '#title' => $this->t('Expiration Year'),
      '#options' => array_combine(range(date('Y'), date('Y') + 10), range(date('Y'), date('Y') + 10)),
      '#required' => TRUE,
    ];

    // CVV
    $form['cvv'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CVV'),
      '#required' => TRUE,
      '#attributes' => [
        'maxlength' => 4,
        'pattern' => '[0-9]*',
      ],
    ];

    // Submit button
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Payment'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate card number (basic length and numeric check).
    if (!is_numeric($form_state->getValue('card_number')) || strlen($form_state->getValue('card_number')) < 13) {
      $form_state->setErrorByName('card_number', $this->t('Please enter a valid card number.'));
    }

    // Validate CVV
    if (!is_numeric($form_state->getValue('cvv')) || (strlen($form_state->getValue('cvv')) < 3 || strlen($form_state->getValue('cvv')) > 4)) {
      $form_state->setErrorByName('cvv', $this->t('Please enter a valid CVV.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Redirect to a payment gateway, or process payment via an API here.
    drupal_set_message($this->t('Your payment has been processed securely.'));
  }

}
