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
	
    public function test(){
        $this->load()->model('Food');
        $food = new Food;
        echo '<pre>';

        //print_r( $food->getById(14) );
        //print_r( $food->getById(14, array('limit'=>1)) );

        //print_r( $food->limit(10) );
        //print_r( $food->limit(10,'id,name') );
        //print_r( $food->limit(10,'name','id') );
        //print_r( $food->limit(10,'name,id','location') );
        //print_r( Food::_limit(10) );

        //print_r( $food->getOne() );
        //print_r( Food::_getOne() );
        //$food->location = 'Asia';
        //$food->food_type_id = 10;
        print_r( $food->count() );
        print_r( Food::_count() );
        print_r( Food::_count($food) );
        //Doo::cache('php')->set('foodtotal', Food::_count($food));
        //echo '<h1>'. Doo::cache('php')->get('foodtotal') .'</h1>';


        print_r(  Food::getById__first(6) );


        //$food->id = 14;
        //print_r( $food->getOne() );
        // print_r( $food->find() );

        //print_r( Food::_find($food) );

        //print_r( $food->getById_location__first(6, 'Malaysia') );
        //print_r( $food->getById_location(6, 'Malaysia', array('limit'=>1)) );
        //print_r( $food->relateFoodType($food, array('limit'=>'first')) );
        //print_r( $food->relateFoodType($food) );
        //print_r( $food->relateFoodType__first($food) );

        //print_r( Food::getById__location__first(6, 'Malaysia') );
        //print_r( Food::getById__location(6, 'Malaysia') );
        //print_r( Food::getById__first(6) );
        //print_r( Food::getByLocation('Malaysia') );
        //print_r( Food::relateFoodType__first($food) );
        //print_r( Food::relateFoodType() );
        //print_r( Food::relateFoodType_first() );
        //print_r( Food::_relate('', 'FoodType') );
        //
        //$f = new Food;
        //print_r( $f->relate('FoodType') );
        //print_r( $f->relate('FoodType', array('limit'=>'first')) );

        # if update/delete/insert cache auto purged on the Model.
        //$food->id = 6;
        //$food->name = 'Wan Tan Mee';
        //$food->update();
        //print_r($food->find());
        
        //$food->purgeCache();  #to delete cache manually
        //Food::_purgeCache();  #to delete cache manually

        # If no SQL is displayed, it means that the data are read from cache.
        # And of course your Model have to extend DooSmartModel
        print_r(Doo::db()->showSQL());
    }

    public function gen_models(){
        Doo::loadCore('db/DooModelGen');
        DooModelGen::genMySQL();
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