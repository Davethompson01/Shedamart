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

    public static function createCategory($name) {
        $db = Database::connect();
        $query = "INSERT INTO " . self::$table . " (name) VALUES (:name)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":name", $name);
        return $stmt->execute();
    }

    public static function deleteCategory($id) {
        $db = Database::connect();
        $query = "DELETE FROM " . self::$table . " WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>

