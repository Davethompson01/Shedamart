<?php

namespace App\Models;

use PDO;
require_once __DIR__ . "/../../config/Database.php";
use App\Config\Database;

class JewelryModel
{
    private static $db;
    
    public static function initialize()
    {
        if (!self::$db) {
            self::$db = (new Database())->getConnection();
        }
    }
    
    public static function getPaginatedJewelry($limit, $offset)
    {
        self::initialize();
        
        $query = "SELECT * FROM jewellery LIMIT :limit OFFSET :offset";
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
