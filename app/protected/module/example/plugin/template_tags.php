<?php

//register functions to be used with your template files
$template_tags = array('function_deny', 'isset', 'empty');

if(!function_exists('function_deny')){
	//This will be called when a function NOT Registered is used in IF or ElseIF statment
	function function_deny($var=null){
	   echo '<span style="color:#ff0000;">Function denied in IF or ElseIF statement!</span>';
	   exit;
	}
}

?>