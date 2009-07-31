<?php

class RestController extends DooController{

    public function listFood(){
        $data['food'] = 'One Ton Mee';
        $data['type'] = 'Noodles';
        $data['rating'] = '10 out of 10';

        switch($this->extension){
            case '.json':
                $result = json_encode($data);
                $format = 'JSON format';
                break;
            case '.xml':
                $result =  htmlentities("<result><food>{$data['food']}</food><type>{$data['type']}</type><rating>{$data['rating']}</rating></result>");
                $format = 'XML format';
                break;
        }
        $data['title'] = 'RestController->listFood';
        $data['content'] = 'Food result in '.$format;
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = $result;
        $this->view()->render('template', $data);
    }

    public function createFood(){
        echo "RestController->createFood\n\n";
        echo "Food created with data:\n";
        print_r($_POST);
    }

    public function updateFood(){
        echo "RestController->updateFood\n\n";
        echo "Food updated with data:\n";
        print_r($this->puts);
    }

    public function deleteFood(){
        echo "RestController->deleteFood\n\n";
        echo "Food deleted with id:\n";
        print_r($this->params['id']);
    }

}
?>