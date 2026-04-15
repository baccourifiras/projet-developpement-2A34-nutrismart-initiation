<?php
require_once __DIR__ . '/../Model/Category.php';

class CategoryController {

    public function showCategory($category) {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Nom</th><th>Description</th></tr>';
        echo '<tr>';
        echo '<td>' . $category->getId()          . '</td>';
        echo '<td>' . $category->getName()        . '</td>';
        echo '<td>' . $category->getDescription() . '</td>';
        echo '</tr>';
        echo '</table>';
    }

    public function addCategory($name, $description) {
        $category = new Category(0, $name, $description);
        return $category;
    }

    public function updateCategory($id, $name, $description) {
        $category = new Category($id, $name, $description);
        return $category;
    }
}
?>
