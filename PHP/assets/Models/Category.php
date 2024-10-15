<?php




class Category {
    private static $table = 'categories';

    public static function getAllCategories() {
        $db = Database::connect();
        $query = "SELECT * FROM " . self::$table;
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   
}
?>

