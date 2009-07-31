<?php

//register functions to be used with your template files
$template_tags = array('upper', 'tofloat', 'sample_with_args', 'debug', 't', 't2');

// the 1st argument must be the variable passed in from template, the other args should NOT be variables

// And of course you can change it to load ini/xml files for translation
function t($str){
    if(Doo::conf()->lang==Doo::conf()->default_lang)
        return $str;

    include 'lang/' . Doo::conf()->lang .'.lang.php';
    
    if(isset($lang[$str]))
        return $lang[$str];
}

// And of course you can change it to load ini/xml files for translation
function t2($arr){
    if(Doo::conf()->lang==Doo::conf()->default_lang)
        return $arr[1];

    include 'lang/' . Doo::conf()->lang .'.lang2.php';

    if(isset($lang[$arr[0]]))
        return $lang[$arr[0]];
}

?>
