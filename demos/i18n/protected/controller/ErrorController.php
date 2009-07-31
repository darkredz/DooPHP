<?php
/**
 * Description of ErrorController
 *
 * @author darkredz
 */
Doo::loadController('I18nController');

class ErrorController extends I18nController {
    
    function index(){
        $data['header'] ='header';
        $data['nav'] = 'nav';
        $data['title'] = 'ERROR 404';
        $data['content'] = 'This is a cool message.';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render($_COOKIE['lang'] .'/template', $data);
    }
}
?>