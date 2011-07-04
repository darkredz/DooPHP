<?php

class RestServerController extends DooController{
    var $database;

    public function  __construct() {
        if(!isset($_SESSION['rest_demo']['database']))
            session_start();
            
        if(isset($_SESSION['rest_demo']['database'])){
            $this->database = $_SESSION['rest_demo']['database'];
            return;
        }

        $this->database = $_SESSION['rest_demo']['database'] = array(
            array(
                'food' => 'One Ton Mee',
                'type' => 'Noodles',
                'rating' => '10 out of 10'
            ),
            array(
                'food' => 'Bak Kut Teh',
                'type' => 'Meat',
                'rating' => '10 out of 10'
            ),
            array(
                'food' => 'Asam Laksa',
                'type' => 'Noodles',
                'rating' => '10 out of 10'
            ),
            array(
                'food' => 'Mee Rebus',
                'type' => 'Noodles',
                'rating' => '10 out of 10'
            ),
            array(
                'food' => 'Nasi Lemak',
                'type' => 'Rice',
                'rating' => '10 out of 10'
            ),
            array(
                'food' => 'Roti Canai',
                'type' => 'Bread?',
                'rating' => '10 out of 10'
            ),
            array(
                'food' => 'Bubur Ayam KFC',
                'type' => 'Porridge',
                'rating' => '5 out of 10'
            ),
            array(
                'food' => 'Dim Sum',
                'type' => 'Touch Heart??',
                'rating' => '11 out of 10'
            ),
            array(
                'food' => 'Ampang Neong Toh Fu',
                'type' => 'Tofu',
                'rating' => '10 out of 10'
            )
        );
    }

    public function listFood_xml(){
        $this->setContentType('xml');
        $result =  $this->genXml($this->database);
        echo $result;
    }

    public function listFood_json(){
        $this->setContentType('json');
        $result = json_encode($this->database);
        echo $result;
    }

    private function genXml($arr) {
        $str = '<result>';
        foreach($arr as $k=>$v){
            $str .='<record>';
            foreach($v as $k2=>$v2){
                $str .= "<$k2>$v2</$k2>";
            }
            $str .='</record>';
        }
        $str .= '</result>';
        return $str;
    }


    public function listFoodById(){
        //Check if the id is in the database array
        $id = intval($this->params['id'])-1;
        if(!isset( $this->database[ $id ] )){
            return 404;
        }
        
        switch($this->extension){
            case '.json':
                $this->setContentType('json');
                $result = json_encode( $this->database[$id]);
                break;
            case '.xml':
                $this->setContentType('xml');
                $result =  $this->genXml(array($this->database[$id]));
                break;
            default:
                return 404;
        }
        echo $result;
    }

    public function createFood(){
        // This example is using header Accept: application/json instead of extension name .json to determine the return result type
        if(!in_array($this->accept_type(), array('xml', 'json')))
            return 404;

        if(isset($_POST['type']) && isset($_POST['food']) && isset($_POST['rating'])){
            $newFood['type'] = $_POST['type'];
            $newFood['food'] = $_POST['food'];
            $newFood['rating'] = $_POST['rating'];
            $this->database = array_merge($this->database, array($newFood));
            $_SESSION['rest_demo']['database'] = $this->database;

            $newFood['insertId'] = sizeof($this->database);

            switch($this->accept_type()){
                case 'json':
                    $this->setContentType('json');
                    $result = json_encode( $newFood );
                    break;
                case 'xml':
                    $this->setContentType('xml');
                    $result =  $this->genXml(array($newFood));
                    break;
            }
            echo $result;
        }else{
            //redirect internally to the api error failure handler to display the errors
            //while throwing up a 404 error HTTP code.
            return array('/api/failed/Fields cannot be empty. (type, food, rating)', 404);
        }
    }

    public function updateFood(){
        // This example is using header Accept: application/json instead of extension name .json to determine the return result type
        if(!in_array($this->accept_type(), array('xml', 'json'))){
            return 404;
        }
        
        if(isset($this->puts['type']) && isset($this->puts['id']) && isset($this->puts['food']) && isset($this->puts['rating'])){
            $id = intval($this->puts['id'])-1;
            if(!isset( $this->database[ $id ] )){
                return array('/api/failed/Food Id not found in database', 404);
            }
            $editFood['type'] = $this->puts['type'];
            $editFood['food'] = $this->puts['food'];
            $editFood['rating'] = $this->puts['rating'];
            $this->database[$id] = $editFood;
            $_SESSION['rest_demo']['database'] = $this->database;

            $editFood['updatedId'] = $this->puts['id'];

            switch($this->accept_type()){
                case 'json':
                    $this->setContentType('json');
                    $result = json_encode( $editFood );
                    break;
                case 'xml':
                    $this->setContentType('xml');
                    $result =  $this->genXml(array($editFood));
                    break;
            }
            echo $result;
        }else{
            //redirect internally to the api error failure handler to display the errors
            //while throwing up a 404 error HTTP code.
            return array('/api/failed/Fields cannot be empty. (id, type, food, rating)', 404);
        }
    }

    public function deleteFood(){
        // This example doesnt return any thing except a HTTP 200 header, which the client can verify it is successfully deleted
        if(isset($this->params['id'])){
            $id = intval($this->params['id'])-1;
            if(!isset( $this->database[ $id ] )){
                return array('/api/failed/Food Id not found in database', 404);
            }
            unset($this->database[$id]);
            $_SESSION['rest_demo']['database'] = $this->database;
        }else{
            //redirect internally to the api error failure handler to display the errors
            //while throwing up a 404 error HTTP code.
            return array('/api/failed/Food Id cannot be empty.', 404);
        }
    }

    public function admin(){
        $users['admin'] = '1234';
        $users['doophp'] = '1234';
        $users['demo'] = 'demo';

        Doo::loadCore('auth/DooDigestAuth');
        $username = DooDigestAuth::http_auth('Food Api Admin', $users, 'Failed to login!');
        echo 'You are login now. Welcome, ' . $username;
    }

    public function api_fail(){
        switch($this->accept_type()){
            case 'json':
                $this->setContentType('json');
                $result = json_encode( array('error'=>$this->params['msg']) );
                break;
            case 'xml':
                $this->setContentType('xml');
                $result =  '<error>'.$this->params['msg'].'</error>';
                break;
        }
        echo $result;
    }

}
?>