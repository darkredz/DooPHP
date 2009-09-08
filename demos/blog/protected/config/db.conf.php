<?php
/**
 * Example Database connection settings and DB relationship mapping
 * $dbmap[Table A]['has_one'][Table B] = array('foreign_key'=> Table B's column that links to Table A );
 * $dbmap[Table B]['belongs_to'][Table A] = array('foreign_key'=> Table A's column where Table B links to );
 */

$dbmap['Post']['has_many']['Tag'] = array('foreign_key'=>'post_id', 'through'=>'post_tag');
$dbmap['Tag']['has_many']['Post'] = array('foreign_key'=>'tag_id', 'through'=>'post_tag');

$dbmap['Post']['has_many']['Comment'] = array('foreign_key'=>'post_id');
$dbmap['Comment']['belongs_to']['Post'] = array('foreign_key'=>'id');


$dbconfig['dev'] = array('localhost', 'blog', 'root', '1234', 'mysql', true, 'collate'=>'utf8_unicode_ci', 'charset'=>'utf8');
$dbconfig['prod'] = array('localhost', 'blog', 'root', '1234', 'mysql', true, 'collate'=>'utf8_unicode_ci', 'charset'=>'utf8');

?>