<?php
class Recipe{
    public $id;
    public $description;
    public $food_id;
    public $_table = 'recipe';
    public $_primarykey = 'id';
    public $_fields = array('id','description','food_id');
}
?>