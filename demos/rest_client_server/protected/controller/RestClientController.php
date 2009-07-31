<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RestClientController
 *
 * @author leng
 */
class RestClientController extends DooController{

    public function twitter_daily(){
        //$client = $this->load()->helper('DooRestClient', true);
        Doo::loadHelper('DooRestClient');
        $client = new DooRestClient;

        // Check out this Twitter API http://apiwiki.twitter.com/Twitter-Search-API-Method%3A-trends-daily
        $client->connect_to('http://search.twitter.com/trends/daily.json')->get();

        if($client->isSuccess()){
            $data['title'] = 'Twitter Daily Trends <em>http://search.twitter.com/trends/daily.json</em>';
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>JSON string: <pre>' . $client->result() .'</pre>';
            $data['baseurl'] = Doo::conf()->APP_URL;
            $data['printr'] = $client->json_result();
        }else{
            print_r($client->resultCode());
            print_r($client->result());
            echo 'Failed to get Twitter API!';
            exit;
        }

        $this->view()->render('template', $data);
    }

    public function twitter_follower(){
        Doo::loadHelper('DooRestClient');
        $client = new DooRestClient;

        // Check out this Twitter API http://apiwiki.twitter.com/Twitter-Search-API-Method%3A-trends-daily
        $client->connect_to('http://twitter.com/followers/ids/doophp.xml')->get();

        if($client->isSuccess()){
            $data['title'] = 'Twitter Followers\' Id <em>http://twitter.com/followers/ids/doophp.xml</em>';
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['baseurl'] = Doo::conf()->APP_URL;
            $data['printr'] = $client->xml_result();
        }else{
            print_r($client->resultCode());
            print_r($client->result());
            echo 'Failed to get Twitter API!';
            exit;
        }

        $this->view()->render('template', $data);
    }

    public function twitter_post(){
        //http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0update
        //Twitter is using HTTP Basic Auth, thus you have to pass in a True in auth() because the default is Digest Auth
        //It can be done in one line!
        $client = $this->load()->helper('DooRestClient', true)
                        ->connect_to("http://twitter.com/statuses/update.xml")
                        ->auth($this->params['username'], $this->params['password'],true)
                        //->options(array('SSL_VERIFYPEER'=>false))
                        //->accept(DooRestClient::JSON)
                        ->data( array('status'=>$this->params['message']) )
                        //->data( 'abc=123123&gf=pretty girl' )    //you can use a string instead of array for the data
                        ->post();

        if($client->isSuccess()){
            $data['title'] = 'Twitter Update Status <em>http://twitter.com/statuses/update.xml</em>';
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['baseurl'] = Doo::conf()->APP_URL;
            $data['printr'] = $client->xml_result();
        }else{
            print_r($client->resultCode());
            print_r($client->result());
            echo 'Failed to get Twitter API!';
            exit;
        }

        $this->view()->render('template', $data);
    }

    public function foodById(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/list/'. $this->params['id'].$this->extension;

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )->get();

         $data['title'] = 'Food by ID <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;
         
        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            if($this->extension=='.xml'){
                $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
                $data['printr'] = $client->xml_result();
            }
            else if($this->extension=='.json'){
                $data['content'] .= '<br/><br/>JSON string: <pre>' . $client->result() .'</pre>';
                $data['printr'] = $client->json_result();
            }
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['printr'] = null;
        }

        $this->view()->render('template', $data);
    }

    public function foodAllXml(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/list/all.xml';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )->get();

         $data['title'] = 'Food by ID <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['printr'] = null;
        }

        $this->view()->render('template', $data);
    }

    public function foodAllJson(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/list/all.json';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )->get();

         $data['title'] = 'Food by ID <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>JSON string: <pre>' . $client->result() .'</pre>';
            $data['printr'] = $client->json_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['printr'] = null;
        }

        $this->view()->render('template', $data);
    }

    public function foodCreateNew(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/create';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )
               ->accept(DooRestClient::XML)
               ->data($this->params)
               ->post();

         $data['title'] = 'Food Create <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['content'] .= '<br/><br/>Error content: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }

        $this->view()->render('template', $data);
    }

    public function foodUpdate(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/update';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )
               ->accept(DooRestClient::XML)
               ->data($this->params)
               ->put();

         $data['title'] = 'Food Update <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['content'] .= '<br/><br/>Error content: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }

        $this->view()->render('template', $data);
    }

    public function foodDelete(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/delete/' . $this->params['id'];

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )->accept( DooRestClient::XML )->delete();

         $data['title'] = 'Food Delete <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>Nothing return. Delete success.';
            $data['printr'] = $client->xml_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['content'] .= '<br/><br/>Error content: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }

        $this->view()->render('template', $data);
    }

    public function foodAdmin(){
        if(empty($this->params['username']) || empty($this->params['password']))
            return 404;
            
        $server_url = Doo::conf()->APP_URL . 'index.php/api/admin/dostuff';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )
               ->auth( $this->params['username'], $this->params['password'] )
               ->post();
               
         $data['title'] = 'Food Admin <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;
         
        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['printr'] = $client->result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['content'] .= '<br/><br/>Error content: <pre>' . $client->result() .'</pre>';
            $data['printr'] = null;
        }

        $this->view()->render('template', $data);
    }

}

?>