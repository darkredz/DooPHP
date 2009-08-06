<?php
/**
 * Description of MainController
 *
 * @author darkredz
 */
class MainController extends DooController{

    public function index(){
		echo '<h1>It Works!</h1>';
    }

    public function gen_model(){
        Doo::loadCore('db/DooModelGen');
        DooModelGen::gen_mysql();
    }

}
?>
