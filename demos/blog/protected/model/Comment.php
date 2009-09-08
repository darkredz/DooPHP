<?php

Doo::loadCore('db/DooModel');

class Comment extends DooModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $post_id;

    /**
     * @var varchar Max length is 64.
     */
    public $author;

    /**
     * @var varchar Max length is 128.
     */
    public $email;

    /**
     * @var varchar Max length is 45.
     */
    public $content;

    /**
     * @var varchar Max length is 128.
     */
    public $url;

    /**
     * @var datetime
     */
    public $createtime;

    /**
     * @var tinyint Max length is 1.  unsigned.
     */
    public $status;

    public $_table = 'comment';
    public $_primarykey = 'id';
    public $_fields = array('id','post_id','author','email','content','url','createtime','status');

    public function getVRules() {
        //edited for inserting Comments
        return array(
                'post_id' => array(
                        array( 'integer', 'Post ID needed to be an integer' ),
                        array( 'custom', 'BlogController::checkPostExist' ),
                ),

                'author' => array(
                        array( 'maxlength', 64, 'Author name cannot be longer than 64 characters!' ),
                        array( 'notempty', 'Author field cannot be empty' ),
                        array( 'notnull' ),
                ),

                'email' => array(
                        array( 'email' ),
                ),

                'content' => array(
                        array( 'maxlength', 145, 'Comment cannot be longer than 145 characters!' ),
                        array( 'notempty', 'Comment cannot be empty!' ),
                        array( 'notnull' ),
                ),

                'url' => array(
                        array( 'url'),
                        array( 'maxlength', 145, 'URL cannot be longer than 145 characters!' ),
                        array( 'optional' ),
                ),
            );
    }
}
?>