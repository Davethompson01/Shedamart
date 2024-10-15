
<?php
class CategoryController {
    public static function listCategories() {
        $categories = Category::getAllCategories();
        echo json_encode($categories);
    }
}


