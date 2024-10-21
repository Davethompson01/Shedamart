<?php

namespace App\Controllers;

use App\Models\Product;
use App\Config\Database;

class ProductController {

    private $db;

    public function __construct() {
        // Instantiate the Database and get the connection
        $database = new Database();
        $this->db = $database->getConnection(); // Correctly assign the connection to $this->db
    }

    public function uploadProducts($productsData) {
        // Initialize arrays to hold results
        $successfulUploads = [];
        $errors = [];
    
        // Assuming you already have a database connection in $this->db
        foreach ($productsData as $product) {
            // Pass both $this->db and $product to the createProduct method
            $result = Product::createProduct($this->db, $product);
    
            if (isset($result['error'])) {
                // Handle error, store product name and error details
                $errors[] = [
                    'product_name' => $product['product_name'],
                    'errors' => $result['error']
                ];
            } else {
                // Handle success, store the successful product data
                $successfulUploads[] = [
                    'product_name' => $product['product_name'],
                    'status' => 'Uploaded successfully'
                ];
            }
        }
    
        // Prepare the final response
        if (!empty($errors)) {
            return [
                'status' => 'partial',
                'uploaded' => $successfulUploads,
                'errors' => $errors
            ];
        } else {
            return [
                'status' => 'success',
                'uploaded' => $successfulUploads,
                'message' => 'All products uploaded successfully.'
            ];
        }
    }
}
