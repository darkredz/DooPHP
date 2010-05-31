<?php
/**
* DooRbAcl class file.
*
* @author aligo <aligo_x@163.com>
* @link http://www.doophp.com/
* @copyright Copyright &copy; 2009 Leng Sheng Hong
* @license http://www.doophp.com/license
*
*/
Doo::loadCore('auth/DooAcl');

class DooRbAcl extends DooAcl{

    /**
     * Check if the user's role is able to access the resource/action.
     *
     * @param mixed $roles Roles of a user, usually retrieve from user's login session
     * @param string $resource Resource name (use Controller class name)
     * @param string $action Action name (use Method name)
     * @return array|string Returns the fail route if user cannot access the resource.
     */
    public function process($roles, $resource, $action=''){

		if(!is_array($roles)){
			$roles = explode(',', $roles);
		}
		
		$denied = false;
		$allowed = false;
		foreach($roles as $role) {
			$denied = $denied || $this->isDenied($role, $resource, $action);
			$allowed = $allowed || $this->isAllowed($role, $resource, $action);
		}

		if( $denied ){
            //echo 'In deny list';
            if(isset($this->rules[$role]['failRoute'])){
                $route = $this->rules[$role]['failRoute'];

                if(is_string($route)){
                    return array($route, 'internal');
                } else{
                    if(isset($route[$resource])) {
                        return (is_string($route[$resource])) ? array($route[$resource], 'internal') : $route[$resource] ;
                    } else if(isset( $route[$resource.'/'.$action] )){
                        $rs = $route[$resource.'/'.$action];
                        return (is_string($rs))? array($rs, 'internal') : $rs;
                    } else if(isset( $route['_default'] )){
                        return (is_string($route['_default'])) ? array($route['_default'], 'internal') : $route['_default'];
                    }
                }
            }
			return (is_string($this->defaultFailedRoute)) ? array($this->defaultFailedRoute, 404) : $this->defaultFailedRoute;
			
        } else if($allowed==false){
            //echo 'Not in allow list<br>';
            if(isset($this->rules[$role]['failRoute'])){
                $route = $this->rules[$role]['failRoute'];

                if(is_string($route)){
                        return array($route, 'internal');
                } else{
                    if(isset($route[$resource])){
                        return (is_string($route[$resource])) ? array($route[$resource], 'internal') : $route[$resource] ;
                    } else if(isset( $route[$resource.'/'.$action] )){
                        $rs = $route[$resource.'/'.$action];
                        return (is_string($rs))? array($rs, 'internal') : $rs;
                    } else if(isset( $route['_default'] )){
                        return (is_string($route['_default'])) ? array($route['_default'], 'internal') : $route['_default'];
                    }
                }
            }
			return (is_string($this->defaultFailedRoute)) ? array($this->defaultFailedRoute, 404) : $this->defaultFailedRoute;
        }
    }
}

?>