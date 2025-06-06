<?php

/**
 * @file
 * Contains \Drupal\add_ons\Form\UserTicketForm.
*/

namespace Drupal\add_ons\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use DateTime;
use DateInterval;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;

class NoUserTicketForm extends FormBase {
    /**
    * {@inheritdoc}
    */
    public function getFormId() {
        return 'no_user_add_ons_ticketing_form';
    }

    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state) {
	
        $form['form_info'] = array(
            '#markup' => t(''),
        );
        $form['first_name'] = array(
            '#type' => 'textfield',
            '#title' => t('First Name'),
            '#required' => TRUE,
            '#maxlength' => 75,
			'#placeholder' => t('What\'s your first name'),
            '#default_value' => "Crystal",
        );
        $form['last_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Last Name'),
            '#required' => TRUE,
            '#maxlength' => 100,
			'#placeholder' => t('What\'s your last name') ,
            '#default_value' => "Hansen" ,
        );
        $form['email'] = array(
            '#type' => 'textfield',
            '#title' => t('Email'),
            '#required' => TRUE,
            '#maxlength' => 50,
			'#placeholder' => t('hello@youremail.com'),
            '#default_value' => "crystal.hansen@emtp.com",
        );
		$form['license_id'] = array(
            '#type' => 'textfield',
            '#title' => t('License ID'),
            '#required' => TRUE,
            '#maxlength' => 50,
			'#placeholder' => t('Eg. 123232232321'),
            '#default_value' => "123232232321", //implement in development local is not set up with user fields yet.
        );
		// Add a date field.
		$form['maintenance_expiry'] = [
		  '#type' => 'date',
		  '#title' => t('Select a Date'),
		  '#description' => t('Please select your License\'s maintenance expiry date.'),
		  '#required' => TRUE,
		];
        $form['subject'] = [
            '#type' => 'textfield',
            '#title' => t('Subject of support contact'),
            '#required' => TRUE,
            '#maxlength' => 255,
			'#placeholder' => t('I need help with my license but I dont have my user information, or I lost my information.'),
            '#default_value' => "I need help with my license but I dont have my user information, or I lost my information.",
        ];
        $form['description'] = array(
            '#type' => 'textarea',
            '#title' => t('Description of reason for support'),
            '#required' => TRUE,
            '#maxlength' => 1000,
			'#rows' => 5, // Number of rows for the textarea.
			'#placeholder' => t('Im trying to submit a ticket but i dont have my username credentials to communicate.'),
            '#default_value' => "Im trying to submit a ticket but i dont have my username credentials to communicate.",
        );
		
		
		$form['category'] = [
		  '#type' => 'checkboxes',
		  '#options' => array('GENERAL_INQUIRY' => $this->t('General Inquiry'), 
			'FEATURE_REQUEST' => $this->t('Feature Request'), 
			'License' => $this->t('License'),//'hs_ticket_category'=> 'GENERAL_INQUIRY;FEATURE_REQUEST;BILLING_ISSUE;PRODUCT_ISSUE'
			'Installation' => $this->t('Installation')),
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
		 
		
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Send Us a Ticket'),
            '#button_type' => 'emtp-red-btn confetti-button',
        );
		
		
		 $form['#theme'] = 'no_user_ticket_form_template';
		
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
		if ($form_state->getValue('license_id') == '') {
            $form_state->setErrorByName('license_id', $this->t('Please Enter Your License ID'));
        }
		if ($form_state->getValue('maintenance_expiry') == '') {
            $form_state->setErrorByName('maintenance_expiry', $this->t('Please Enter Your Maintenance Expiry Date'));
        }
        if ($form_state->getValue('subject') == '') {
            $form_state->setErrorByName('subject', $this->t('Please Enter a subject of support contact so we can best help you.'));
        }
        if ($form_state->getValue('description') == '') {
            $form_state->setErrorByName('description', $this->t('Give us a little more detail.'));
        }
    }

    /**
    * {@inheritdoc}
    */
    public function submitForm(array &$form, FormStateInterface $form_state) {
		// Load the current user.
		$user = \Drupal\user\Entity\User::load(33617); //using crystals id for outer contacts to access the ticketing system
		$hs_contact_id = "151"; //assigning the contact to be me on my testing of unique access of the ticketing system. tbd TODO:
        try{
            $conn = Database::getConnection();
            $field = $form_state->getValues();
// need to associate the user with this table user:authenticated user method source?

			$fields['user_id']= \Drupal::currentUser()->id();
			$fields['hs_contact_id'] = //$hs_contact_id; //
            $fields["first_name"] = $field['first_name'];
            $fields["last_name"] = $field['last_name'];
            $fields["email"] = $field['email'];
			$fields["hs_license_id"] = $field['license_id'];
			$fields["maintenance_expiry"] = $field['maintenance_expiry'];
            $fields["subject"] = "Help me please!";//$field['subject'];
            $fields["description"] = "I am adding more functionality! ";//$field['description'];
			$fields["hs_ticket_id"] = "";
			$fields["creation_date"] = date_create()->format('Y-m-d H:i:s');
			$maintenance_expiry = strtotime($field['maintenance_expiry']); // Convert to timestamp
			// Check if maintenance expiry is greater than today's date
			$fields["hs_active_maintenance_contract"] = $maintenance_expiry > strtotime($fields["creation_date"]) ? '1' : '0'; //assigning boolean 1 true 0 false condition is always true first in this case
			$fields["type"] = "Bug(s)";//$field['type'];
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
            //insert into database as fall back to api failure.
			$conn->insert('user_ticketing')->fields($fields)->execute();
			//call api to submit the ticket. 
			$response_msg = $this->submitToAPI( $form_state  );
			
			$this->updateTicket( $fields, $response_msg['id']);

            \Drupal::messenger()->addMessage(t("Support ticket has been succesfully saved. ".$response_msg['id']));
        }
        catch(Exception $ex){
            \Drupal::logger('user_ticketing')->error($ex->getMessage());
        }
    }
	/**
    * Custom send to API on form submit
    */
    public function submitToAPI( FormStateInterface $form_state) {
		// Does not apply since user is not logged in. TODO can search for user with email - license id .
		// for association to be determined
		// Search for user based on email and license id if find send to ticketing with this info $current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		$hubspot_id = "151" ;//$current_user->get('field_hs_contact_id')->value;
		$basic_auth_access_token = 'xxx-xxx-xxx-xxx';
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
					\Drupal::logger('user_ticketing')->info("Checkbox is::".$box. "the id:: is::".$id);
			  }
			  
		}
		
		
		$data3 = array (
				'properties' =>
					array (
							'hs_pipeline'=>'0', 
							'hs_pipeline_stage' => '1', 								
							'hs_ticket_priority' => "LOW",//$field['priority'],
							'subject' => $field['subject'],// aka ticket name append values to the field
							'content' => $field['description'], //append all values to content body of ticket
							'hs_ticket_category' => "GENERAL_INQUIRY",//$category, //'GENERAL_INQUIRY;FEATURE_REQUEST;BILLING_ISSUE;', hubspot created fields are tagged with hs_ so trying to follow that standard in emtp.com
							'first_name' => $field['first_name'],
							'last_name' => $field['last_name'],
							'email' => $field['email'],
							'license_id' => $field['license ID'],
							'active_maintenance_contract' => $field['hs_active_maintenance_contract'], //want this to be 'true' 'false' from hidden form field -  words passed
							'type' =>  "Bug(s)",//$field['type'],
							//'hs_file_upload' => "test.txt", //file to send name? not sure how this will store yet or if we pass it through the filemanager channel
														
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
		//print_r($data3); json_encodjson_encode($arr);e($arr);
		//print_r($data3);
		//print_r(json_encode($data3));
        //\Drupal::logger('user_ticketing')->info("data3 array".print_r(json_encode($data3)),['operations' => 'pre remote post ticket to hubspot Private API','response' => print_r(json_decode($data3), TRUE)] );

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
			
			
			\Drupal::logger('user_ticketing')->info('submission remote post success!'.print_r($response_msg), ['operation' => 'remote post ticket to hubspot Private API','response' => $response_msg['id']]);

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
		//echo $current_user->get('field_hs_contact_id')->value;
		//print_r($current_user->get('field_hs_contact_id'));
		$hubspot_id = $current_user->get('field_hs_contact_id')->value;
		$interval = 'P2D';
		$datetime = new DateTime(); // Current datetime
		$datetime->sub(new DateInterval($interval)); // Subtract the interval

		// Subtracting 1 hour (3600 seconds) from $datetime
		$datetime->sub(new DateInterval('PT1H'));
		
		try{
            $conn = Database::getConnection();
            //$field = $form_state->getValues();
			$conn->update('user_ticketing')
				 ->fields(array('hs_ticket_id' => $hs_ticket_id))
				 ->condition('creation_date', array( $fields['creation_date'],$datetime->format('Y-m-d H:i:s')), 'BETWEEN')
				    ->condition('creation_date', [$fields['creation_date'], $datetimeString], 'BETWEEN')
				 ->execute();
			\Drupal::logger('user_ticketing')->info('Support ticket has been succesfully updated with the ticket_id. '.$hs_ticket_id, ['operations'=>"Database save"]);	 
			\Drupal::messenger()->addMessage(t("Support ticket has been succesfully updated with the ticket_id. ".$hs_ticket_id ));
        }
        catch(Exception $ex){
            \Drupal::logger('user_ticketing')->error($ex->getMessage());
        }
		
	}
	public function fileManagerInsert(array $fields, string $auth){
		
		//hs_file_upload hubspot field name 
			
		// This example uploads a local file named 'example_file.txt'
		// to the /docs folder, without checking for duplicate files
			// $hubspot_url = "https://api.hubapi.com/crm/v3/objects/tickets";
			// $post_url = "https://api.hubapi.com/filemanager/api/v3/files/upload";
			
			//TODO: Store upload drupal way.  If a file was uploaded. drupal way to try to update local system but we eare uploading to hubspot first as test.
			/*if (!empty($file)) {
			  // Load the file object.
			  $file_entity = \Drupal\file\Entity\File::load($file[0]);

			  // Get the file path.
			  $file_path = $file_entity->getFileUri();

			  // Do something with the file path, such as attaching it to an API request.
			  // For example:
			  // $api->attachFile($file_path);

			  // Example: Display the file path.
			  drupal_set_message(t('File uploaded successfully. File path: @path', ['@path' => $file_path]));
			}
			else {
			  drupal_set_message(t('No file uploaded.'), 'error');
			}
			*/
			
			
			
			//Jan 2024 NEW CODE UNTESTED
			//$fields['hs_file_upload']
			$upload_file = new CURLFile($fields['hs_file_upload'],'application/octet-stream');
			// original $upload_file = new CURLFile('example_file.txt','application/octet-stream');
			
			$file_options = array(
				"access" => "PUBLIC_INDEXABLE",
				"overwrite" => false,
				"duplicateValidationStrategy" => "NONE",
				"duplicateValidationScope" => "ENTIRE_PORTAL"
			);

			$post_data = array(
				"file" => $upload_file,
				"options" => json_encode($file_options),
				"folderPath" => "/docs"
			);

			$ch = curl_init(); 
			@curl_setopt($ch, CURLOPT_POST, true);
			@curl_setopt($ch, CURLOPT_URL, $post_url);
			@curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

			$response    = @curl_exec($ch); //Log the response from HubSpot as needed.
			$status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); //Log the response status code
			@curl_close($ch);
			echo $status_code . " " . $response;
			
			//TODO: JAN 2024 Convert to guzzle style send?
			
			// how to pass this data to a json array $fields['hs_file_upload']
			
			/* $response = \Drupal::httpClient()->post($hubspot_url, [
							'verify' => true,
							'json' => $data3,
							'headers' => [
								'Authorization' => $auth,
								'Content-type' => 'application/json',
							],
						])->getBody()->getContents();
			*/			
			// this returns to debug message json: response_value: '{"inlineMessage":"Thanks for submitting the form."}'
			//Yaml::encode($response_data)
			//$response_msg = json_decode($response,true);  //true indicates array from json string  "inlineMessage" is key 
			
			
			
			
			
			
			

	}
	
	public function twoDayInterval(){
		// Initialising a interval of 2 days
		$interval = 'P2D';
		$datetime = date_create()->format('Y-m-d H:i:s'); //mysql datetime format 
		// Calling the sub() function
		$datetime->sub(new DateInterval($interval));
		$string2time = strtotime($datetime.'- 3600');
		//echo $string2time;
		return $string2time;
	}
	
}