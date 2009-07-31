<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CamelCaseController
 *
 * @author darkredz
 */
class CamelCaseController extends DooController {
    //will not be able to access via auto routing if autoroute is false
    public $autoroute = true;
    
    public function index(){
        $data['title'] = 'CamelCaseController->index';
        $data['content'] = 'camel case auto routing';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = $this->params;
        $this->view()->render('template', $data);
    }

    public function moo(){
        $data['title'] = 'CamelCaseController->moo';
        $data['content'] = 'camel mooing??? Hell no!';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = $this->params;
        $this->view()->render('template', $data);
    }
}
?>