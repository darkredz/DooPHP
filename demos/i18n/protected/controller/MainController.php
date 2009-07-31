<?php
/**
 * Description of MainController
 *
 * @author darkredz
 */

Doo::loadController('I18nController');

class MainController extends I18nController{

    public function index(){
        $data['header'] ='header';
        $data['nav'] = 'nav';
        $data['dynamic_msg'] = 'This can be translated';
        $data['dynamic_key_msg'] = array('welcome_user', 'Welcome to my site, Mr. User!');
        $data['dynamic_key_msg2'] = array('input_invalid', 'Invalid input for email address.');
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = null;
        $this->view()->render(Doo::conf()->lang .'/about', $data);
    }

    public function example(){
        $data['header'] ='header';
        $data['nav'] = 'nav';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render(Doo::conf()->lang .'/example', $data);
    }

}
?>