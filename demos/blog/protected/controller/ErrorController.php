<?php
/**
 * ErrorController
 * Feel free to change this and customize your own error message
 *
 * @author darkredz
 */
class ErrorController extends DooController{

    public $data;

    function defaultError(){
        $this->data['rootUrl'] = Doo::conf()->APP_URL;
        $this->data['title'] = 'ERROR 404 not found';
        $this->data['content'] = '<p>This is handler by an internal Route as defined in common.conf.php $config[\'ERROR_404_ROUTE\']</p>
                
<p>Your error document needs to be more than 512 bytes in length. If not IE will display its default error page.</p>

<p>Give some helpful comments other than 404 :(
Also check out the links page for a list of URLs available in this demo.</p>';
        $this->prepareSidebar();
        $this->render('error', $this->data);
    }
	
    function loginError(){
        $this->data['rootUrl'] = Doo::conf()->APP_URL;
        $this->data['title'] =  'Unauthorized Access!';
        $this->data['content'] =  '<p style="color:#ff0000;">You must login first to administer!</p>';
        $this->prepareSidebar();
        $this->render('error', $this->data);
    }

    function postError(){
        $this->data['rootUrl'] = Doo::conf()->APP_URL;
        $this->data['title'] =  'Post Not Found!';
        $this->data['content'] =  '<p style="color:#ff0000;">The post with ID '.$this->params['pid'].' is not found.</p>';
        $this->prepareSidebar();
        $this->render('error', $this->data);
    }

    /**
     * Prepare sidebar data, random tags and archive list
     */
    private function prepareSidebar(){

        //if tags cache exist, skip retrieving from DB, expires every 5 minutes
        $cacheTagOK = Doo::cache('front')->testPart('sidebarTag', 300);
        if(!$cacheTagOK){
            echo '<h2>Cache expired. Get Tags from DB!</h2>';
            //get random 10 tags
            Doo::loadModel('Tag');
            $tags = new Tag();
            $this->data['randomTags'] = $tags->limit(10, null, null, array('custom'=>'ORDER BY RAND()'));
        }else{
            $this->data['randomTags'] = array();
        }

        //if archive cache exist, skip retrieving from DB, archive expires when Post added, updated, deleted
        $cacheArchiveOK = Doo::cache('front')->testPart('sidebarArchive', 31536000);
        if(!$cacheArchiveOK){
            echo '<h2>Cache expired. Get Archives from DB!</h2>';
            //you can pass data to constructor to set the Model properties
            Doo::loadModel('Post');
            $p = new Post(array('status'=>1));
            $this->data['archives'] = $p->getArchiveSummary();
        }else{
            $this->data['archives'] =array();
        }
    }
}
?>