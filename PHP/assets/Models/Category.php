<?php

require_once __DIR__ . "/../../config/Database.php";

use App\Config\Database;

class Category {
    private static $table = 'categories';

    public static function getAllCategories() {
        $db = (new Database())->getConnection();
        $query = "SELECT * FROM " . self::$table;
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   
}


