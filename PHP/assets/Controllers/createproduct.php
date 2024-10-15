<?php

namespace App\Controllers;

use App\Models\ProductModel;
require_once __DIR__ . "/../Models/createproduct.php";

class ProductController
{
    public static function insertProduct($categoryName, $productData)
    {
        return ProductModel::insertIntoCategoryTable($categoryName, $productData);
    }
}
