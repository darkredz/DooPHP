<?php
/**
 * Description of ErrorController
 *
 * @author darkredz
 */
class ErrorController extends DooController {
    
    //put your code here
    function index(){
        $data['title'] = 'ERROR 404 not found <em>means page not found!</em>';
        $data['content'] = 'This is handler by an internal Route as defined in common.conf.php $config[\'ERROR_404_ROUTE\']';
        $data['content'] .= '<p>Your error document needs to be more than 512 bytes in length. If not IE will display its default error page.</p><p>Give some helpful comments other than 404 :(<br/>Also check out the links page for a list of URLs available in this demo.</p>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = null;
        $this->view()->render('template', $data);
    }
}
?>