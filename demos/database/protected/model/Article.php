<?php
class Article{
    public $id;
    public $title;
    public $content;
    public $createtime;
    public $draft;
    public $food_id;
    public $_table = 'article';
    public $_primarykey = 'id';
    public $_fields = array('id','title','content','createtime','draft','food_id');
}
?>