<?php

//register functions to be used with your template files
$template_tags = array('upper', 'tofloat', 'sample_with_args', 'debug', 'url');

// the 1st argument must be the variable passed in from template, the other args should NOT be variables
function upper($str){
    return strtoupper($str);
}

function tofloat($str){
    return sprintf("%.2f", $str);
}

function sample_with_args($str, $prefix){
    return $str .' with args: '. $prefix;
}

function debug($var){
    if(!empty($var)){
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}

function url($addRootUrl, $id, $param=null){
    Doo::loadHelper('DooUrlBuilder');
    // param pass in as string with format
    // 'param1=>this_is_my_value, param2=>something_here'

    if($param!=null){
        $param = explode(', ', $param);
        $param2 = null;
        foreach($param as $p){
            $splited = explode('=>', $p);
            $param2[$splited[0]] = $splited[1];
        }
        return DooUrlBuilder::url($id, $param2, $addRootUrl);
    }

    return DooUrlBuilder::url($id, null, $addRootUrl);
}

?>
