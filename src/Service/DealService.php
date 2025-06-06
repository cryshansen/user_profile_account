<?php

namespace Drupal\user_profile_account\Service;

use Drupal\Core\Database\Database;

/*
* Deals represent the sale or and essentially the cart until it is confirmed then also becomes an order and has order items
*/


class DealService {

    // A simple method that returns a sample array of deals.
    public function getDeals() {
        $deals = [
            [
                "id" => "8168039328",
                "properties" => [
                    "amount" => "3060.00",
                    "createdate" => "2024-01-31T21:55:16.058Z",
                    "hs_lastmodifieddate" => "2024-01-31T21:55:16.058Z",
                    "hs_object_id" => "8168039328",
                    "hs_product_id" => "1834545172",
                    "quantity" => "1",
                ],
                "createdAt" => "2024-01-31T21:55:16.058Z",
                "updatedAt" => "2024-01-31T21:55:16.058Z",
                "archived" => false,
            ],
            [
                "id" => "21642681164",
                "properties" => [
                    "amount" => "5000.00",
                    "createdate" => "2024-02-15T10:30:00.000Z",
                    "hs_lastmodifieddate" => "2024-02-15T10:30:00.000Z",
                    "hs_object_id" => "21642681164",
                    "hs_product_id" => "1834566322",
                    "quantity" => "2",
                ],
                "createdAt" => "2024-02-15T10:30:00.000Z",
                "updatedAt" => "2024-02-15T10:30:00.000Z",
                "archived" => false,
            ],
            // Add more deals as needed
        ];
    
    // Add product details for each deal based on hs_product_id
        foreach ($deals as &$deal) {
            $product = $this->getProductByHsProductId($deal['properties']['hs_product_id']);
            $deal['properties']['product_name'] = $product->Name ?? 'Unknown Product';
            $deal['properties']['product_description'] = $product->Product_description ?? 'No description available';
        }

        return $deals;
    
    
    
    }
    
    
    
    
    
     // New method to fetch product details by hs_product_id
    protected function getProductByHsProductId($hs_product_id) {
        $connection = Database::getConnection();

        $query = $connection->select('products', 'p')
            ->fields('p', ['Name', 'Product_description'])
            ->condition('record_id', $hs_product_id, '=');

        return $query->execute()->fetchObject(); // Return as an object for easier property access
    }
    
    
    
    
    
}