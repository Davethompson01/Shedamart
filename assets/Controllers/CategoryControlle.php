

<?php
class CategoryController {
    public static function listCategories() {
        $categories = Category::getAllCategories();
        echo json_encode($categories);
    }

    public static function addCategory() {
        $data = json_decode(file_get_contents("php://input"));
        $name = $data->name;

        $result = Category::createCategory($name);
        echo json_encode(["success" => $result]);
    }

    public static function removeCategory($id) {
        $result = Category::deleteCategory($id);
        echo json_encode(["success" => $result]);
    }
}
?>


