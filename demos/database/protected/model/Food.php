<?php
Doo::loadCore('db/DooSmartModel');

class Food extends DooSmartModel{

    public $id;
    public $name;
    public $description;
    public $location;
    public $food_type_id;
    public $_table = 'food';
    public $_primarykey = 'id';
    public $_fields = array('id','name','description','location','food_type_id');

    function __construct(){
        //parent::__construct( array('id'=>1, 'name'=>'Ban Mian', 'location'=>'Malaysia') );    //This is for you to set the properties with constructor
        //parent::$caseSensitive = true;
        parent::$className = __CLASS__;     //a must if you are using static querying methods Food::_count(), Food::getById()
        //parent::setupModel(__CLASS__);
        //parent::setupModel(__CLASS__, true);
    }
		
    public function get_recipe(){
        return Doo::db()->relate('Recipe', __CLASS__, array('limit'=>'first'));
    }

    public function get_by_id(){
        if(intval($this->id)<=0)
            return null;
        return Doo::db()->find($this, array('limit'=>1));
    }

}
?>