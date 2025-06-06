<?php

namespace Drupal\user_profile_account\Service;

use Drupal\Core\Database\Database;


class ProductService {

 /**
   * Returns an listing of array of products. 
   * Useful for injecting service to a product listing block or page bootstrap cards
   * A license product that is one of three products.      
   */
  public function getLicenseProducts() {
    // Fetch or generate product data.
    //Product type = License
    $connection = Database::getConnection();
    //SELECT distinct folder,Node_Type FROM `em_products` where folder not in ('Trainings','Consulting','Fees','') and Product_Type ="License" and Node_Type not in ('Node lock Stand alone 1 PC only Maintenance Fees','');
    // Build and execute the query
    $query = $connection->select('products', 'p')
        ->distinct()
        ->fields('p', ['folder', 'Node_Type'])
        ->condition('folder', ['Trainings', 'Consulting', 'Fees', ''], 'NOT IN')
        ->condition('Product_Type', 'License', '=');
    // Log the rendered SQL query.
    \Drupal::logger('add_ons')->debug($query->__toString());
    // Fetch all results as an array of objects
    $results = $query->execute()->fetchAll();

/*

    'EMTP'  ,   'Network 1 User or Node lock Stand Alone with Hardware Key Software Fees',   License;
    'EMTP - Node Lock / Stand Alone' , 'Node lock  Stand alone  1 PC only Software Fees', License;
    'EMTP - University Partnership', ' University Network 1 User  Software Annual Fees' ,   License
    
*/
    // Optionally, you can format the data into an array if needed
    $license_products = [];
    foreach ($results as $record) {
        $license_products[] = [
            //'record_id' => $record->record_id,
            'product_name' => $record->folder,
            'product_type' => $record->Node_Type,
            // Add other fields as necessary
        ];
    }

    return $license_products;
    
  }
  // Define a sample product
/*    
    $product1 = [
          'name' => 'EMTP Basic - Stand Alone',
          'description' => 'Node Lock / Stand Alone',
          'price' => '17000.0',
          'icon' => t('<i class="fas fa-plug"></i>'), // Provide the path to the icon
    ];
    
    
*/
 public function getProductsInclucedInByPlan(){
     
      //Product type = License
    $connection = Database::getConnection();
    //record id of emtp basic 1747124737  'EMTP - Node Lock / Stand Alone' , 'Node lock  Stand alone  1 PC only Software Fees', "License";
    //Select record_id, folder, Node_Type, Product_Type, Name, Product_description, Price_CAD,Price_EUR,Price_INR,Price_USD  FROM em_products where folder ="EMTP - Node Lock / Stand Alone" and Node_Type="Node lock  Stand alone  1 PC only Software Fees" order by Name;
    // Build and execute the query
    $query = $connection->select('products', 'p')
        ->fields('p', ['record_id' ,'folder', 'Node_Type','Product_Type', 'Name', 'Product_description', 'Price_CAD','Price_EUR','Price_INR','Price_USD'])
        ->condition('folder', 'EMTP - Node Lock / Stand Alone', '=')
        ->condition('Product_Type', 'License', '=')
        ->condition('Node_Type','Node lock  Stand alone  1 PC only Software Fees', '=')
        ->orderBy('Name', 'ASC');
    // Log the rendered SQL query.
    \Drupal::logger('add_ons')->debug($query->__toString());
    // Fetch all results as an array of objects
    $results = $query->execute()->fetchAll();
    
    // Optionally, you can format the data into an array if needed
    $plan_products = [];
    foreach ($results as $record) {
        $plan_products[] = [
            'record_id' => $record->record_id,
            //'name' => $record->folder,
            //'plan_product_type' => $record->Node_Type,
            'name' =>$record->Name,
            'description' => $record->Product_description,
            // Add other fields as necessary
           /*TODO: 'price'=> [
                    [
                        'currency'=>'CAD', 
                        'currency_value' => $record->Price_CAD,
                    ],[
                        'currency'=>'EUR', 
                        'currency_value' => $record->Price_EUR,
                    ],[
                        'currency'=>'INR', 
                        'currency_value' => $record->Price_INR,
                    ],[
                        'currency'=>'USD', 
                        'currency_value' => $record->Price_USD,
                    ],
            ],*/
            'price'=> $record->Price_CAD,
            'icon' => t('<i class="fas fa-'.$record->icon.'"></i>'),
        ];
    }

    return $plan_products;
    
    
 }
  /*
  * this gets all products at the folder product type level. folder ['Trainings', 'Consulting', 'Fees', '']
  * product_Type License
  * and node
  */
  public function getPlans() {
    // Get a connection to the Drupal database
    $connection = Database::getConnection();

     $query = $connection->select('products', 'p')
        ->distinct()
        ->fields('p', ['folder', 'Node_Type'])
        ->condition('folder', ['Trainings', 'Consulting', 'Fees', ''], 'NOT IN')
        ->condition('Product_Type', 'License', '=')
        ->condition('Node_Type', ['Node lock  Stand alone  1 PC only Maintenance Fees', ''], 'NOT IN');
    // Log the rendered SQL query.
    \Drupal::logger('add_ons')->debug($query->__toString());
    
    // Execute the query
    $result = $query->execute();

    // Fetch results
    //$distinct_folder = $result->fetchCol();
    $distinct_products = $result->fetchAll();
   

    // Optionally, you can format the data into an array if needed
    $plans = [];
    foreach ($distinct_products as $plan) {
         if (empty($product->folder)) {
             $plans[] = [
                'folder' => "Global license",
                'product_type' => $plan->Product_Type,
            ];
         } else {
            drupal_set_message("Folder: {$product->folder}, Product Type: {$product->Product_Type}");
            $plans[] = [
                'folder' => $plan->folder,
                'product_type' => $plan->Product_Type,
            ];
         }
    }

    return $plan_products;
}
 
  /**
   * Returns an array of package types.
   */
  public function getPackageTypes() {
	  
	$packages = ['Trial','Academic','Professional','Advanced','Customized']; 
    
	return [
	  ['name' => 'Trial', 'features' => ['Feature 1', 'Feature 2']],
      ['name' => 'Starter', 'features' => ['Feature 1', 'Feature 2']],
	  ['name' => 'Academic', 'features' => ['Feature 1', 'Feature 2']],
	  ['name' => 'Professional', 'features' => ['Feature 1', 'Feature 2']],
      ['name' => 'Advanced', 'features' => ['Feature A', 'Feature B']],
	  ['name' => 'Customized', 'features' => ['Feature A', 'Feature B']],
      // More package types.
    ];
	
	
  }
  public function getPlanByUser(){
      
      
      
      
      
      return [
         // ['EMTP - Node Lock / Stand Alone' , 'Node lock  Stand alone  1 PC only Software Fees', 'License']
        
      ];
  }
  
    /**
     * Fetch the product name from em_product table by record_id.
     */
  /*  
  public function getProductNameByRecordId($recordId) {
         // Get a connection to the Drupal database
        $connection = Database::getConnection();

        $query = $connection->select('products', 'p')
            ->fields('p', ['Name','Product_description'])
            ->condition('record_id', $recordId);
            //->execute()
            //->fetchField();
          // Fetch all results as an array of objects
        $results = $query->execute()->fetchObject(); 
        return $results ?: (object) ['Name' => 'Unknown Product', 'Product_description' => 'No description available']; //$query ? $query : 'Unknown Product';  // Default to "Unknown Product" if no match is found
    }
*/

 
}
