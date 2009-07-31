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

    public function gen_models(){
        Doo::loadCore('db/DooModelGen');
        $gen = new DooModelGen;
        $gen->gen_mysql();
    }

}
?>
