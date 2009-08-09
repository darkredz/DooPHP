<?php
/**
 * MainController
 * Feel free to delete the methods and replace them with your own code.
 *
 * @author darkredz
 */
class MainController extends DooController{

    public function index(){
		//Just replace these
		Doo::loadCore('app/DooSiteMagic');
		DooSiteMagic::displayHome();	
    }
	
	public function allurl(){
		Doo::loadCore('app/DooSiteMagic');
		DooSiteMagic::showAllUrl();	
	}
	
    public function debug(){
		Doo::loadCore('app/DooSiteMagic');
		DooSiteMagic::showDebug();
    }
	
	public function gen_sitemap_controller(){
		$this->gen_sitemap();
		$this->gen_site();
	}
	
	public function gen_sitemap(){
		Doo::loadCore('app/DooSiteMagic');
		DooSiteMagic::buildSitemap();		
	}
	
	public function gen_site(){
		Doo::loadCore('app/DooSiteMagic');
		DooSiteMagic::buildSite();
	}
	
    public function gen_model(){
        Doo::loadCore('db/DooModelGen');
        DooModelGen::gen_mysql();
    }

}
?>