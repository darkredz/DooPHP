<?php
Doo::loadCore('db/DooModel');

class Tag extends DooModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var varchar Max length is 145.
     */
    public $name;

    public $_table = 'tag';
    public $_primarykey = 'id';
    public $_fields = array('id','name');


    public function  __construct() {
        parent::setupModel(__CLASS__);
    }

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'name' => array(
                        array( 'maxlength', 145 ),
                        array( 'notnull' ),
                )
            );
    }

    public function validate($checkMode='all', $requireMode='null'){
        //You do not need this if you extend DooModel or DooSmartModel
        //MODE: all, all_one, skip
        Doo::loadHelper('DooValidator');
        $v = new DooValidator;
        $v->checkMode = $checkMode;
		$v->requiredMode = $requireMode;
        return $v->validate(get_object_vars($this), $this->getVRules());
    }

}
?>