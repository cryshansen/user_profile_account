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
        return 'user_ticketing_form';
    }

    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state) {
		$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		$hs_contact_id = $user->get('field_hubspot_contact_id')->value;
		
        $form['form_info'] = array(
            '#markup' => '<h4>Request Help with your product or service.</h4><br>',
        );
        $form['first_name'] = array(
            '#type' => 'textfield',
            '#title' => t('First Name'),
            '#required' => TRUE,
            '#maxlength' => 75,
            '#default_value' => $user->get('name')->value,
        );
        $form['last_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Last Name'),
            '#required' => TRUE,
            '#maxlength' => 100,
            '#default_value' => $user->get('name')->value,
        );
        $form['email'] = array(
            '#type' => 'textfield',
            '#title' => t('Email'),
            '#required' => TRUE,
            '#maxlength' => 50,
            '#default_value' => $user->get('mail')->value,
        );
		$form['phone'] = array(
            '#type' => 'textfield',
            '#title' => t('phone'),
            '#required' => TRUE,
            '#maxlength' => 50,
            '#default_value' => '',
        );
        $form['subject'] = [
            '#type' => 'textfield',
            '#title' => t('Subject of support contact'),
            '#required' => TRUE,
            '#maxlength' => 255,
            '#default_value' => '',
        ];
        $form['description'] = array(
            '#type' => 'textfield',
            '#title' => t('Description of reason for support'),
            '#required' => TRUE,
            '#maxlength' => 1000,
            '#default_value' => '',
        );
		$form['category'] = [
		  '#type' => 'checkboxes',
		  '#options' => array('GENERAL_INQUIRY' => $this->t('General Inquiry'), 'FEATURE_REQUEST' => $this->t('Feature Request'), 'BILLING_ISSUE' => $this->t('Billing Issue'), 'PRODUCT_ISSUE' => $this->t('Product Issue')),//'hs_ticket_category'=> 'GENERAL_INQUIRY;FEATURE_REQUEST;BILLING_ISSUE;PRODUCT_ISSUE'
		  '#title' => $this->t('Category of the Problem'),
		];
		
		$form['priority'] = [
		  '#title' => t('Priority'),
		  '#type' => 'select',
		  '#options' => array('HIGH' => t('High'),
							'MEDIUM' => t('Medium'),
							'LOW' => t('Low')),
		];
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Send Ticket'),
            '#button_type' => 'primary',
        );
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
            $form_state->setErrorByName('user_age', $this->t('Please Enter a subject of support contact'));
        }
        if ($form_state->getValue('description') == '') {
            $form_state->setErrorByName('description', $this->t('Please Enter Description of contact'));
        }
    }

    /**
    * {@inheritdoc}
    */
    public function submitForm(array &$form, FormStateInterface $form_state) {
		// Load the current user.
		$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		$hs_contact_id = $user->get('field_hubspot_contact_id')->value;
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
            $fields["subject"] = $field['subject'];
            $fields["description"] = $field['description'];
			$fields["hs_ticket_id"] = "";
			$fields["creation_date"] = date_create()->format('Y-m-d H:i:s');
			//$fields['category'] = $field['category'];
			$selection = $form_state->getUserInput()['category'] ?? [];
			foreach ($selection as $id => $box) {
			  //$checked = isset($box);
			  // Do something here...
			  if(isset($box)){ 
				$fields['category'] .= $box.";";
				//\Drupal::logger('user_profile_account_')->info("user_ticketing Checkbox is::".$box. "the id:: is::".$id);
			  }
			  
			}
			//$fields['priority'] = $field['priority'];
			
			\Drupal::logger('user_profile_account')->info("user tickenting complete checkbox selection is::".$fields['category']);
			
			if($fields['category']==""){ $fields['category'] ="GENERAL_INQUIRY;"; }
            
			$conn->insert('user_ticketing')
            ->fields($fields)->execute();
			$response_msg = $this->submitToAPI( $form_state  );
			//$response_mess=
			$this->updateTicket( $fields, $response_msg['id']);

            \Drupal::messenger()->addMessage(t("Support ticket has been succesfully saved. ".$response_msg['id']));
        }
        catch(Exception $ex){
            \Drupal::logger('user_profile_account')->error($ex->getMessage());
        }
    }
	/**
    * Custom send to API on form submit
    */
    public function submitToAPI( FormStateInterface $form_state) {
		// Load the current user.
		// for association to be determined
		$current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		$hubspot_id = $current_user->get('field_hubspot_contact_id')->value;
		$basic_auth_access_token = 'sxx-xx1-a0123456-1aaa-78xxx-bxxa-xc12345d2abc'; //fake auth
		$auth = 'Bearer ' . $basic_auth_access_token;
		$hubspot_url = "https://api.hubapi.com/crm/v3/objects/tickets";
		$field = $form_state->getValues();
		$category="";
		$selection = $form_state->getUserInput()['category'] ?? [];
		foreach ($selection as $box) {
			  //$checked = isset($box);
			  // Do something here...
			  if(isset($box)){ 
				$category .= $box.";";
					//\Drupal::logger('user_profile_account')->info("Checkbox is::".$box. "the id:: is::".$id);
			  }
			  
		}
		
		$data3 = array (
				'properties' =>
					array (
							'hs_pipeline'=>'0', 
							'hs_pipeline_stage' => '1', 								
							'hs_ticket_priority' => $field['priority'],
							'subject' => $field['subject'],// aka ticket name append values to the field
							'content' => $field['description'], //append all values to content body of ticket
							'hs_ticket_category' => $category, //'GENERAL_INQUIRY;FEATURE_REQUEST;BILLING_ISSUE;PRODUCT_ISSUE',
							'first_name' => $field['first_name'],
							'last_name' => $field['last_name'],
							'email' => $field['email'],
							'phone' => $field['phone'],
							
					),
				'associations' => 
					array (
						0 => 
						array (
						  'to' => 
							  array (
								'id' => $hubspot_id,
							  ),
						  'types' => 
							  array (
								0 => 
								array (
								  'associationCategory' => 'HUBSPOT_DEFINED',
								  'associationTypeId' => 16,
								),
						  ),
						),
					  ),
				
			);
				print_r($data3); 
        \Drupal::logger('user_profile_account')->info("user ticketing data3 array".print_r(json_decode($data3), TRUE),['operations' => 'pre remote post ticket to hubspot Private API','response' => print_r(json_decode($data3), TRUE)] );

        try{
			//httpClient is a helper class against guzzle classes of post/get therefore the "POST" is sent
			// post type is json from the front end configuration -- this then should match the response.
			// data does not need json encoding, it must happen in guzzle layer 
			//response is a stdClass object
			$response = \Drupal::httpClient()->post($hubspot_url, [
							'verify' => true,
							'json' => $data3,
							'headers' => [
								'Authorization' => $auth,
								'Content-type' => 'application/json',
							],
						])->getBody()->getContents();
						
			// this returns to debug message json: response_value: '{"inlineMessage":"Thanks for submitting the form."}'
			//Yaml::encode($response_data)
			$response_msg = json_decode($response,true);  //true indicates array from json string  "inlineMessage" is key 
			
			
			\Drupal::logger('user_profile_account')->info('User Ticketing submission remote post success!'.print_r($response_msg), ['operation' => 'remote post ticket to hubspot Private API','response' => $response_msg['id']]);

		}catch (\GuzzleHttp\Exception\GuzzleException $error) {
		  // Get the original response
		  $response = $error->getResponse();
		  // Get the info returned from the remote server.
		  $response_info = $response->getBody()->getContents();
		  // Using FormattableMarkup allows for the use of <pre/> tags, giving a more readable log item.
		  $message = new \Drupal\Component\Render\FormattableMarkup('API connection error. Error details are as follows:<pre>@response</pre>', ['@response' => print_r(json_decode($response_info), TRUE)]);
		  // Log the error
		  watchdog_exception('Remote API Connection', $error, $message);
		  \Drupal::logger('user_ticketing')->info('submission remote post failed at guzzle level', ['@error'=>$error->getMessage()]);
		}
		catch (\Exception $error) {
		  // Log the error.
		  watchdog_exception('Remote API Connection', $error, t('An unknown error occurred while trying to connect to the remote API. This is not a Guzzle error, nor an error in the remote API, rather a generic local error ocurred. The reported error was @error', ['@error' => $error->getMessage()]));
			\Drupal::logger('user_ticketing')->info('Something errored!', ['@error'=>$error->getMessage()]);
		}
		/* extra exception handling
		catch(RequestException $e){
			
			//$response = $e->getResponse();
			$responseBody = $response->getBody()->getContents();
			echo $responseBody;
		}catch(ClientErrorResponseException $exception){
			$responseBody = $exception->getResponse()->getBody(true);
			echo $responseBody;
		}*/
		return $response_msg;
	
    }
	
	public function updateTicket(array $fields, string $hs_ticket_id){
		//update the ticket in user_ticketing hs_ticket_id = $hs_ticket_id where user = and subject= and creation date between....submit time and now. 
		//update hubspot with contact association to the ticket. 
		$current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		//echo $current_user->get('field_hubspot_contact_id')->value;
		//print_r($current_user->get('field_hubspot_contact_id'));
		$hubspot_id = $current_user->get('field_hubspot_contact_id')->value;
		$datetime = date_create()->format('Y-m-d H:i:s'); //mysql datetime format 
		// Initialising a interval of 2 days
		//$interval = 'P2D';
		// Calling the sub() function
		//$datetime->sub(new DateInterval($interval));
		//$string-to-time = strtotime($datetime.'- 3600');
		//echo $hubspot_id;
		try{
            $conn = Database::getConnection();
            //$field = $form_state->getValues();
			$conn->update('user_ticketing')
				 ->fields(array('hs_ticket_id' => $hs_ticket_id))
				 ->condition('creation_date', array( $fields['creation_date'],$datetime), 'BETWEEN')
				 ->execute();
			\Drupal::logger('user_profile_account')->info('User ticketing Support ticket has been succesfully updated with the ticket_id. '.$hs_ticket_id, ['operations'=>"Database save"]);	 
			\Drupal::messenger()->addMessage(t("Support ticket has been succesfully updated with the ticket_id. ".$hs_ticket_id ));
        }
        catch(Exception $ex){
            \Drupal::logger('user_profile_account')->error($ex->getMessage());
        }
		
	}
	
}