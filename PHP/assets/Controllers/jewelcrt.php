<?php

namespace App\Controllers;

use App\Models\JewelryModel;
require_once __DIR__ . "/../Models/jewellerycart.php";
class JewelryController
{
    public static function getJewelryProducts($page = 1, $pageSize = 10)
    {
        $offset = ($page - 1) * $pageSize;
        return JewelryModel::getPaginatedJewelry($pageSize, $offset);
    }
}
