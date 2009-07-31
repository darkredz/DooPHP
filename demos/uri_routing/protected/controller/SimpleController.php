<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimpleController
 *
 * @author leng
 */
class SimpleController extends DooController{
    public $autoroute = false;
    
	public function simple(){
        $data['title'] = 'SimpleController->simple';
        
        if(!empty($this->extension))
            $data['content'] = 'Simple test, extension: ' . $this->extension;
        else
            $data['content'] = 'Simple extension test';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = $this->params;
        $this->view()->render('template', $data);
	}

}
?>