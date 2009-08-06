<?php
/**
 * DooSiteMagic class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */


/**
 * DooSiteMagic serves as a Model class file generator for rapid development
 *
 * <p>If you have your routes defined, call DooSiteMagic::buildSite() and
 * it will generate the Controller files for all the controllers defined along with the methods</p>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooSiteMagic.php 1000 2009-08-06 17:10:12
 * @package doo.app
 * @since 1.0
 */
class DooSiteMagic{

    /**
     * Generates Controller class files from routes definition
     */
	public static function buildSite(){
		$route = Doo::app()->route;
		$controllers = array();
		foreach($route as $req=>$r){
            foreach($r as $rname=>$value){
                $controllers[$value[0]][] = $value[1];
            }
        }
		
		echo "<html><head><title>DooPHP Site Generator </title></head><body bgcolor=\"#2e3436\">";
		$total = 0;
		
		foreach($controllers as $cname=>$methods){
			$filestr = '';
			$filestr .= "<?php\n\nclass $cname extends DooController {" ;
			$methods = array_unique($methods);
			foreach($methods as $mname){
				$filestr .= "\n\n\tfunction $mname() {\n\t\techo 'You are visiting '.\$_SERVER['REQUEST_URI'];\n\t}";
			}
			$filestr .= "\n\n}\n?>";
			if(file_exists(Doo::conf()->SITE_PATH.'protected/controller/'.$cname.'.php')){
				echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;\"><strong><span style=\"color:#729fbe;\">$cname.php</span></strong><span style=\"color:#fff;\"> <span style=\"color:#fff;\">file exists! Skipped ...</span></span></span><br/><br/>";
			}else{
				echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;\"><span style=\"color:#fff;\">Controller file </span><strong><span style=\"color:#e7c118;\">$cname</span></strong><span style=\"color:#fff;\"> generated.</span></span><br/><br/>";
				$total++;
				$handle = fopen(Doo::conf()->SITE_PATH.'protected/controller/'.$cname.'.php', 'w+');
				fwrite($handle, $filestr);
				fclose($handle);
			}
		}
        echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;color:#fff;\">Total $total file(s) generated.</span></body></html>";
	}
}

?>