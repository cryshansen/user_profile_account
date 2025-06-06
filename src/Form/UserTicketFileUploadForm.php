<?php

/**
 * @file
 * Contains \Drupal\user_profile_account\Form\UserTicketForm.
*/

namespace Drupal\user_profile_account\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;

class UserTicketForm extends FormBase {
    /**
    * {@inheritdoc}
    */
    public function getFormId() {
        return 'add_ons_upload_form';
    }
    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
        $hs_contact_id = $user->get('field_hs_contact_id')->value;
        $field_maintenance_expiration = $user->get('field_maintenance_expiration')->value;
        $hs_active_maintenance_contract = false;
        // Convert the date string to a DateTime object
    //    $date = new DateTime($field_maintenance_expiration);dat
        $date =  date($field_maintenance_expiration);
        // Get today's date
        $today = \Drupal::time()->getCurrentTime();
        // Compare field_maintenance_expiration in emtp.com to todays date for active maintenance to send to hubspot.
        if ($date > $today) {
          //echo "The date is greater than today. true"; hubspot is verbal field true / false
          $hs_active_maintenance_contract =  true;
        } else {
          //echo "The date is not greater than today. false";
          $hs_active_maintenance_contract =  false;
        }

            $form['form_info'] = array(
                '#markup' => '<h4>Request Help with your product or service.</h4><br>',
            );
        $form['contact_information'] = [
          '#type' => 'fieldset',
          '#title' => $this->t('Contact Information'),
        ];

        $form['contact_information']['email'] = [
          '#type' => 'email',
          '#title' => $this->t('Email'),
          '#required' => TRUE,
        ];

        $form['contact_information']['first_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('First Name'),
          '#required' => TRUE,
          '#default_value' => $user->get('field_first_name')->value,
        ];

        $form['contact_information']['last_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Last Name'),
          '#required' => TRUE,
          '#default_value' => $user->get('field_last_name')->value
        ];

        $form['phone'] = array(
                '#type' => 'textfield',
                '#title' => t('phone'),
                '#required' => TRUE,
                '#maxlength' => 50,
                '#default_value' => '819-213-5681', //implement in development local is not set up with user fields yet.
            );
            $form['subject'] = [
                '#type' => 'textfield',
                '#title' => t('Subject of support contact'),
                '#required' => TRUE,
                '#maxlength' => 255,
                '#default_value' => 'Help me please!',
            ];
            $form['description'] = array(
                '#type' => 'textfield',
                '#title' => t('Description of reason for support'),
                '#required' => TRUE,
                '#maxlength' => 1000,
                '#default_value' => 'Im testing new fields.',
            );
        $form['category'] = [
          '#type' => 'checkboxes',
          '#options' => array('GENERAL_INQUIRY' => $this->t('General Inquiry'), 
          'FEATURE_REQUEST' => $this->t('Feature Request'), 
          'License' => $this->t('License'),//'hs_ticket_category'=> 'GENERAL_INQUIRY;FEATURE_REQUEST;BILLING_ISSUE;PRODUCT_ISSUE'
          'Installation' => $this->t('Installation'),
          'EMTPWorks' => $this->t('EMTPWorks'),
          'EMTP solver' => $this->t('EMTP solver'),
          'Models' => $this->t('Models'),
          'PSS/E Import tool' => $this->t('PSS/E Import tool'),
          'ScopeView' => $this->t('ScopeView')),
          '#title' => $this->t('Category of the Problem'),
        ];
        
        $form['priority'] = [
          '#title' => t('Priority'),
          '#type' => 'select',
          '#options' => array('HIGH' => t('High'),
                  'MEDIUM' => t('Medium'),
                  'LOW' => t('Low')),
        ];
        
        //chansen Jan30 2024 add on fields: as per Henry
        
        //hs_file_upload
        
        //type dropdown   values hubspot Bug(s);Knowledge;SPAM;Feature request(s);
        $form['type'] = [
          '#title' => t('Type of Issue'),
          '#type' => 'select',
          '#options' => array('Bug(s)' => t('Bug(s)'),
                  'Knowledge' => t('Knowledge'),
                  'SPAM' => t('SPAM'),
                  'Feature request(s)' => t('Feature request(s)')),
        ];
        //hs_active_maintenance_contract / true,false look in user for maintance record / unit number record and send true or false
        $form['hs_active_maintenance_contract'] = [
          '#type' => 'hidden',
          '#value' => $hs_active_maintenance_contract, // Assigning 'true'  'false' words.
        ];

        //TODO: File upload Feb 5 2024
        $form['file_upload'] = [
          '#type' => 'file',
          '#title' => t('Upload a File'),
          '#description' => t('You can add a file to upload to help us, help you!'),
          // You can add additional attributes like '#required' => TRUE here if needed.
          ];

            $form['actions']['#type'] = 'actions';
            $form['actions']['submit'] = array(
                '#type' => 'submit',
                '#value' => $this->t('Send Ticket'),
                '#button_type' => 'emtp-red-btn confetti-button',
            );
        $form['#theme'] = 'user_ticketing_form_theme';
        $form['#theme_variables'] = [
          'form' => $form,
        ];
            return $form;
    }
    /**
    * {@inheritdoc}
    */
    public function validateForm(array &$form, FormStateInterface $form_state) {
    
        if ($form_state->getValue('first_name') == '') {
            $form_state->setErrorByName('first_name', $this->t('Please Enter Your First Name'));
        }
        if ($form_state->getValue('last_name') == '') {
            $form_state->setErrorByName('last_name', $this->t('Please Enter Your Last Name'));
        }
        if ($form_state->getValue('email') == '') {
            $form_state->setErrorByName('email', $this->t('Please Enter Your Email'));
        }
    if ($form_state->getValue('phone') == '') {
            $form_state->setErrorByName('phone', $this->t('Please Enter Your contact Phone Number'));
        }
        if ($form_state->getValue('subject') == '') {
            $form_state->setErrorByName('subject', $this->t('Please Enter a subject of support contact'));
        }
        if ($form_state->getValue('description') == '') {
            $form_state->setErrorByName('description', $this->t('Please Enter Description of contact'));
        }
    }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get form values from the form state.
    $values = $form_state->getValues(); //$field = $form_state->getValues(); allows to reference fields list as array value

    // Handle file upload.
    $file = $this->handleFileUpload($values['file_upload']);

    if ($file instanceof FileInterface && $file->getFileUri()) {
      // File uploaded successfully.
      // Save to database.
      $this->saveToDatabase($file, $form_state);

      // Save to file location (public directory) in drupal  find the forum way and duplicate.
      $this->saveToFileLocation($file);

      // Send data to API for file upload.
      //$this->sendToApiForFileUpload($file);

      // Send data to API for form submission.
      //$this->sendToApiForFormSubmission(FormStateInterface $form_state);
    }
    else {
      // File upload failed.
      // Handle error as needed.
      drupal_set_message(t('File upload failed.'), 'error');
    }
  }

  /**
   * Method to handle file upload.
   */
  public function handleFileUpload(FileInterface $file, FormStateInterface $form_state) {
    try {
      // Your file upload handling logic here.
      // Example:
      // $file_entity = File::load($file->id());
      // Validate and process the file as needed.
      // Return the file entity if upload is successful.
      return $file_entity;
    } catch (\Exception $e) {
      // Catch and handle exceptions.
      // Log errors, display messages etc.
      return NULL; // Return NULL or throw an exception depending on your requirement.
    }
  }
   /**
   * Method to save data to database.
   */
  public function saveToDatabase(FileInterface $file, FormStateInterface $form_state) {
    // Database insertion logic here.
        // Load the current user.
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $hs_contact_id = $user->get('field_hs_contact_id')->value;
        try{
            $conn = Database::getConnection();
            $field = $form_state->getValues();
// need to associate the user with this table user:authenticated user method source?

      $fields['user_id']= \Drupal::currentUser()->id();
      $fields['hs_contact_id'] = $hs_contact_id; //
            $fields["first_name"] = $field['first_name'];
            $fields["last_name"] = $field['last_name'];
            $fields["email"] = $field['email'];
      $fields["phone"] = $field['phone'];
            $fields["subject"] = "Help me please!";//$field['subject'];
            $fields["description"] = "I am adding more functionality! ";//$field['description'];
      $fields["hs_ticket_id"] = "";
      $fields["creation_date"] = date_create()->format('Y-m-d H:i:s');
      $fields["hs_active_maintenance_contract"] = $field['hs_active_maintenance_contract'] ? '1' : '0'; //assigning boolean 1 true 0 false condition is always true first in this case
      $fields["type"] = $field['type'];
      $selection = $form_state->getUserInput()['category'] ?? [];
      foreach ($selection as $id => $box) {
        //$checked = isset($box);
        if(isset($box)){ 
        $fields['category'] .= $box.";";
        \Drupal::logger('user_ticketing')->info("Checkbox is::".$box. "the id:: is::".$id);
        }
        
      }
      $fields['priority'] = $field['priority'];
      
      \Drupal::logger('user_ticketing')->info("complete checkbox selection is::".$fields['category']);
      
      if($fields['category']==""){ $fields['category'] ="GENERAL_INQUIRY;"; }
            
            $fields['file_upload'] =  $file->getFilename();
            
            //insert into database as fall back to api failure. 
      $conn->insert('user_ticketing')->fields($fields)->execute();
      //if file uploads successfully we send it to the api.
      
      
        
      //call api to submit the ticket. 
      $response_msg = $this->submitToAPI( $form_state  );
      
      $this->updateTicket( $fields, $response_msg['id']);

      \Drupal::messenger()->addMessage(t("Support ticket has been succesfully saved. ".$response_msg['id']));
  
    } catch (\Exception $e) {
      // Catch and handle exceptions.
      // Log errors, display messages etc.
      return NULL; // Return NULL or throw an exception depending on your requirement.
    }
    
    
    
    
    
  }

   /**
   * Method to save file to file location.
   */

  public function saveToFileLocation(FileInterface $file) {
    // File saving logic here.
    // Example:
    // $file->setPermanent();
    // $file->save();
  }

  /**
   * Method to send data to API for file upload.
   */
  public function sendToApiForFileUpload(FileInterface $file) {
    // API call for file upload logic here.
    // Example:
    // Use GuzzleHttp\Client to make API request.
  }

  /**
   * Method to send data to API for form submission.
   */
  public function sendToApiForFormSubmission(array $data) {
    // API call for form submission logic here.
    // Example:
    // Use GuzzleHttp\Client to make API request.
  }




}