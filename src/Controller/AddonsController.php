<?php
namespace Drupal\user_profile_account\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Welcome page module.
 */
class AddonsController extends ControllerBase {

  /**
   * Returns Modules and Suggested products available for purchase based on the user license.
   *
   * @return array
   *   return of product add ons available plus additional suggestions based on the user license and areas of interest.
   */
  public function addons(string $user) {
	  //$products = array();
	  //$sim=0;
	  //get the tickets based on id value -- get user from path or get from authentication? 
	  
	  //To get another database  from settings file ( 'external' , 'default' )
		/*$con = \Drupal\Core\Database\Database::getConnection('default','default');
		$query = $con->select('add_ons', 'up');
		$query->condition('up.user_id', $user);		
		$query->fields('up', array('user_id', 'id','description','product_name','price','category','creation_date','hs_contact_id','hs_product_id') );
		*/
		/*echo $query;
		print_r((string) $query);
		print_r($query->arguments());
		*/
		//$current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		//echo $current_user->get('field_hubspot_contact_id')->value;
		//print_r($current_user->get('field_hubspot_contact_id'));
		//$hubspot_id = $current_user->get('field_hubspot_contact_id')->value;
		//echo $hubspot_id;
		//$user_products = $query->execute()->fetchAll();
		
    //return //[
      
	  // Your theme hook name.
	 // '#theme' => 'add_ons_theme_hook',
      
	  // Your variables.
	  //'#description' => 'View all products available for .',
	  //'#products' => []
	  //'#hubspot_id' => $user,
   // ];

    /*return array(
        '#markup' => '' . t('Products available:') . '',
    );*/
    return array (
        '#theme' => 'user-profile-add-ons',
        '#version' => 'Customized Lab', // could be unit number or license key 
        '#description' => 'Products available to promote your services',
        '#products' => ['Lighting Specializations ','Media Metrics','Post-Processing & Editing','Artistic Development','Portfolio Building & Curation','Marketing & Social Media for Artists'],
		'#consulting' => ['Lighting Consultation','Specialized Artistic Study', 'Archival Curations'],
		'#training' =>['Training Events by Interests', 'Customized Corporate Training','Marketing Training','Custom Branding'],
      );

  }
  /**
   * Process cart controls the payment process - save to database, post to paypal / third party resource / respond with positivity :)
   *  js cart is posted to this process
   *  response is string message of pleasant "success or fail" message
   * * logging to be disabled at go live. 
   */
  function processPayment($cart){
	//TODO: move outside the scope of this? configuration ? 
	//application name in sandbox paypal account same way as hubspot apis for exposure
	//PayPall SandboxAPI 'default' credentials
	$client_id ="xxxxxxxxx";
	$client_secret = "xxxxxxx";
	$sandbox_account = "xxxxx@business.example.com";
	
	$message =" Payment Processor";
	// Logs a notice
	
		// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
		// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
		// Set this to 0 once you go live or don't require logging.
		//define("DEBUG", 1);
		// Set to 0 once you're ready to go live
		define("USE_SANDBOX", 1);
		//define("LOG_FILE", "ipn.log");
		// Read POST data
		// reading posted data directly from $_POST causes serialization
		// issues with array data in POST. Reading raw POST data from input stream instead.
		$raw_post_data = file_get_contents('php://input');
		//$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		\Drupal::logger('add_ons')->notice($message ."post : ". JSON.stringify($cart));
		foreach ($cart as $keyval) {
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2)
				$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exists = true;
		}
		foreach ($myPost as $key => $value) {
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}
		// Post IPN data back to PayPal to validate the IPN data is genuine
		// Without this step anyone can fake IPN data
		if(USE_SANDBOX == true) {
			$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		}


		try{

			$ch = curl_init($paypal_url);
			if ($ch == FALSE) {
				return FALSE;
			}
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
			if(DEBUG == true) {
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
			}
			// CONFIG: Optional proxy configuration
			//curl_setopt($ch, CURLOPT_PROXY, $proxy);
			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
			// Set TCP timeout to 30 seconds
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
			// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
			// of the certificate as shown below. Ensure the file is readable by the webserver.
			// This is mandatory for some environments.
			//$cert = __DIR__ . "./cacert.pem";
			//curl_setopt($ch, CURLOPT_CAINFO, $cert);
			$res = curl_exec($ch);
			if (curl_errno($ch) != 0) // cURL error
				{
				if(DEBUG == true) {	
					error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
				}
				curl_close($ch);
				exit;
			} else {
					// Log the entire HTTP response if debug is switched on.
					if(DEBUG == true) {
						error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
						error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
					}
					curl_close($ch);
			}
			// Inspect IPN validation result and act accordingly
			// Split response headers and payload, a better way for strcmp
			$tokens = explode("\r\n\r\n", trim($res));
			$res = trim(end($tokens));


			if (strcmp ($res, "VERIFIED") == 0) {
				// assign posted variables to local variables
				$item_name = $_POST['item_name'];
				$item_number = $_POST['item_number'];
				$payment_status = $_POST['payment_status'];
				$payment_amount = $_POST['mc_gross'];
				$payment_currency = $_POST['mc_currency'];
				$txn_id = $_POST['txn_id'];
				$receiver_email = $_POST['receiver_email'];
				$payer_email = $_POST['payer_email'];
				
				
				
				// check whether the payment_status is Completed
				$isPaymentCompleted = false;
				if($payment_status == "Completed") {
					$isPaymentCompleted = true;
				}
				// check that txn_id has not been previously processed
				$isUniqueTxnId = false; 
				$param_type="s";
				$param_value_array = array($txn_id);
				$result = $db->runQuery("SELECT * FROM payment WHERE txn_id = ?",$param_type,$param_value_array);
				if(empty($result)) {
					$isUniqueTxnId = true;
				}	
				// check that receiver_email is your PayPal email
				// check that payment_amount/payment_currency are correct
				if($isPaymentCompleted) {
					//saves the cart and the payment process including response. 
					saveCartPayment($cart);
				} 
				// process payment and mark item as paid.
				
				
				if(DEBUG == true) {
					error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
				}
				
			} else if (strcmp ($res, "INVALID") == 0) {
				// log for manual investigation
				// Add business logic here which deals with invalid IPN messages
				if(DEBUG == true) {
					error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
				}
			}

			// get tokens for creating the order and processing the payment
			//create order
			createOrder();
		}catch(Exception $e){

			\Drupal::logger('my_module')->error($e->message);
		}
	
  }

  function createOrder(){
			//create paypal order data array  https://developer.paypal.com/docs/api/orders/v2/
			$jayParsedAry = [
				"intent" => "CAPTURE", 
				"purchase_units" => [
					[
						"reference_id" => "d9f80740-38f0-11e8-b467-0ed5f89f718b", //generate the reference id as random value
						"amount" => [
							"currency_code" => "USD", 
							"value" => "100.00" 
						] 
					] 
				], 
				"payment_source" => [
							"paypal" => [
								"experience_context" => [
									"payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED", 
									"payment_method_selected" => "PAYPAL", 
									"brand_name" => "EXAMPLE INC", 
									"locale" => "en-US", 
									"landing_page" => "LOGIN", 
									"shipping_preference" => "SET_PROVIDED_ADDRESS", 
									"user_action" => "PAY_NOW", 
									"return_url" => "https://example.com/returnUrl", 
									"cancel_url" => "https://example.com/cancelUrl" 
								] 
							] 
							] 
			]; 

			$sampleResponse = [
			  "id" => "5O190127TN364715T", 
			  "status" => "PAYER_ACTION_REQUIRED", 
			  "payment_source" => [
					"paypal" => [
					] 
				 ], 
			  "links" => [
						  [
							 "href" => "https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T", 
							 "rel" => "self", 
							 "method" => "GET" 
						  ], 
						  [
								"href" => "https://www.paypal.com/checkoutnow?token=5O190127TN364715T", 
								"rel" => "payer-action", 
								"method" => "GET" 
							 ] 
					   ] 
		   ]; 
			

  }

	function saveCartPayment(array $cart){

		$param_type = "sssdss";
		$param_value_array = array($item_number, $item_name, $payment_status, $payment_amount, $payment_currency, $txn_id);
		$payment_id = $db->insert("INSERT INTO payment(item_number, item_name, payment_status, payment_amount, payment_currency, txn_id) VALUES(?, ?, ?, ?, ?, ?)", $param_type, $param_value_array);
			//To get another database  from settings file ( 'external' , 'default' )
		/*$con = \Drupal\Core\Database\Database::getConnection('default','default');
		$query = $con->select('add_ons', 'up');
		$query->condition('up.user_id', $user);		
		$query->fields('up', array('user_id', 'id','description','product_name','price','category','creation_date','hs_contact_id','hs_product_id') );
		*/
		/*echo $query;
		print_r((string) $query);
		print_r($query->arguments());
		*/
		//$current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		//echo $current_user->get('field_hubspot_contact_id')->value;
		//print_r($current_user->get('field_hubspot_contact_id'));
		//$hubspot_id = $current_user->get('field_hubspot_contact_id')->value;
		//echo $hubspot_id;
		//$user_products = $query->execute()->fetchAll();
		return;		
	}



}