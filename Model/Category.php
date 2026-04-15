<?php

class Category {
    private $id;
    private $name;
    private $description;

    public function __construct($id = 0, $name = '', $description = '') {
        $this->id= $id;
        $this->name= $name;
        $this->description = $description;
    }

    // Getters
    public function getId()          { return $this->id; }
    public function getName()        { return $this->name; }
    public function getDescription() { return $this->description; }

    // Setters
    public function setId($id)                   { $this->id = $id; }
    public function setName($name)               { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; }

    public function show() {
        echo '<table border="1">';
        echo '<tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Description</th>
        </tr>';
        echo '<tr>';
        echo '<td>' . $this->getId()          . '</td>';
        echo '<td>' . $this->getName()        . '</td>';
        echo '<td>' . $this->getDescription() . '</td>';
        echo '</tr>';
        echo '</table>';
    }
}
?>
