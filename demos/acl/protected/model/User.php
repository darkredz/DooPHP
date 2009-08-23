<?php
class User{

    /**
     * @var smallint Max length is 5.  unsigned.
     */
    public $id;

    /**
     * @var varchar Max length is 64.
     */
    public $username;

    /**
     * @var varchar Max length is 64.
     */
    public $pwd;

    /**
     * @var varchar Max length is 32.
     */
    public $group;

    /**
     * @var tinyint Max length is 4.
     */
    public $vip;
    public $_table = 'user';
    public $_primarykey = 'id';
    public $_fields = array('id','username','pwd','group','vip');
}
?>