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
class AdminController extends DooController {
    
    public function index(){
        $data['title'] = 'AdminController->index';
        $data['content'] = 'Thanks for logging in to admin!';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = $this->params;
        $this->view()->render('template', $data);
    }

}
?>