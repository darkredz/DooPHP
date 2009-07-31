<?php
/**
 * Description of MainController
 *
 * @author darkredz
 */
class MainController extends DooController{

    public function index(){
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('about', $data);
    }

    public function url(){
        $data['title'] = 'URL used in this demo';
        $data['content'] = 'Replace :var with your values.<br/><em>Request type */GET = You can test and visit these links.</em>';
        $data['baseurl'] = Doo::conf()->APP_URL;

        include Doo::conf()->SITE_PATH .'protected/config/routes.conf.php';
        $data['printr'] = array();
        $n = 1;
        foreach($route as $req=>$r){
            foreach($r as $rname=>$value){
                //$rname_strip = (strpos($rname, '/')===0)? substr($rname, 1, strlen($rname)) : $rname;
                $rname_strip = 'index.php'.$rname;
                $data['printr'][$n++ .strtoupper(" $req")] = '<a href="'.Doo::conf()->APP_URL.$rname_strip.'">'.$rname.'</a>';
            }
        }
        $this->view()->render('template', $data);
    }

    public function example(){
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = file_get_contents(Doo::conf()->SITE_PATH .'protected/config/routes.conf.php');
        $this->view()->render('example', $data);
    }


}
?>