<?php
/**
 * Description of I18nController
 *
 * @author darkredz
 */
class I18nController extends DooController{
    var $langs = array('en', 'zh', 'de', 'pt');

    function  __construct() {
        if(isset($_GET['lang'])){
            if(in_array($_GET['lang'], $this->langs)){
                setcookie('lang', $_GET['lang'], time()+3600*240, '/');
                Doo::conf()->lang = $_GET['lang'];
            }else{
                setcookie('lang', 'en');
                Doo::conf()->lang = 'en';
            }
        }
        else if(!isset($_COOKIE['lang'])){
            // if user doesn't specify any language, check his/her browser language
            // check if the visitor language is supported
            // $this->language(true) to return the country code such as en-US zh-CN zh-TW
            if(in_array($this->language(), $this->langs)){
                setcookie('lang', $this->language(), time()+3600*240, '/');
                Doo::conf()->lang = $this->language();
            }else{
                setcookie('lang', 'en', time()+3600*240, '/');
                Doo::conf()->lang = 'en';
            }
        }
        else{
            Doo::conf()->lang = $_COOKIE['lang'];
        }
    }

}
?>