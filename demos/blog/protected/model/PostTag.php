<?php
Doo::loadCore('db/DooModel');

class PostTag extends DooModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $tag_id;

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $post_id;

    public $_table = 'post_tag';
    public $_primarykey = 'post_id';
    public $_fields = array('tag_id','post_id');

    public function getVRules() {
        return array(
                'tag_id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'post_id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 11 ),
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