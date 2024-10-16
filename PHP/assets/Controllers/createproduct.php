<?php

namespace App\Controllers;

use App\Models\ProductModel;
require_once __DIR__ . "/../Models/createproduct.php";

class ProductController
{
    public static function insertProduct($categoryName, $productData)
    {
        return ProductModel::insertProductIntoProductsTable($categoryName, $productData);
    }

    public static function getNewlyCreatedProducts()
    {
        $productModel = new ProductModel();
        return $productModel->getNewlyCreatedProducts();
    }

    public static function getMostCheckedProducts($limit = 10)
    {
        $productModel = new ProductModel();
        return $productModel->getMostCheckedProducts($limit);
    }
}
