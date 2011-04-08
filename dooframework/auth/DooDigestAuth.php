<?php
/**
 * DooDigestAuth class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * Handles HTTP digest authentication
 *
 * <p>HTTP digest authentication can be used with the URI router.
 * HTTP digest is much more recommended over the use of HTTP Basic auth which doesn't provide any encryption.
 * If you are running PHP on Apache in CGI/FastCGI mode, you would need to
 * add the following line to your .htaccess for digest auth to work correctly.</p>
 * <code>RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]</code>
 *
 * <p>This class is tested under Apache 2.2 and Cherokee web server. It should work in both mod_php and cgi mode.</p>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooDigestAuth.php 1000 2009-07-7 18:27:22
 * @package doo.auth
 * @since 1.0
 */
class DooDigestAuth{

    /**
     * Authenticate against a list of username and passwords.
     *
     * <p>HTTP Digest Authentication doesn't work with PHP in CGI mode,
     * you have to add this into your .htaccess <code>RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]</code></p>
     *
     * @param string $realm Name of the authentication session
     * @param array $users An assoc array of username and password: array('uname1'=>'pwd1', 'uname2'=>'pwd2')
     * @param string $failMsg Message to be displayed if the User cancel the login
     * @param string $failURL URL to be redirect if the User cancel the login
     * @param boolean $passwordHashed Use hashed password to authenticate.
     * @return string The username if login success.
     */
    public static function auth($realm, $users, $failMsg=NULL, $failURL=NULL, $passwordHashed=false){
        //user => password, eg.
        //$users = array('admin' => '1234', 'guest' => 'guest');
        if(!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && strpos($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 'Digest')===0){
            $_SERVER['PHP_AUTH_DIGEST'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
            header('WWW-Authenticate: Digest realm="'.$realm.
                   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            header('HTTP/1.1 401 Unauthorized');
            if($failMsg!=NULL)
                die($failMsg);
            if($failURL!=NULL)
                die("<script>window.location.href = '$failURL'</script>");
            exit;
        }

        // analyze the PHP_AUTH_DIGEST variable
        if (!($data = self::httpDigestParse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])){
            header('WWW-Authenticate: Digest realm="'.$realm.
                   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            header('HTTP/1.1 401 Unauthorized');
            if($failMsg!=NULL)
                die($failMsg);
            if($failURL!=NULL)
                die("<script>window.location.href = '$failURL'</script>");
            exit;
        }

        // generate the valid response
        if ($passwordHashed) { 
            $A1 = $users[$data['username']];             
        } else { 
            $A1 = md5($data['username'] .':'. self::getRealm($realm) .':'. $users[$data['username']]);             
        } 
        $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
        $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

        if ($data['response'] != $valid_response){
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="'.$realm.
                   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            if($failMsg!=NULL)
                die($failMsg);
            if($failURL!=NULL)
                die("<script>window.location.href = '$failURL'</script>");
            exit;
        }

        // ok, valid username & password
        return $data['username'];
    }
    
    /**
     * @deprecated Deprecated since 1.5
     */
    public static function http_auth($realm, $users, $fail_msg=NULL, $fail_url=NULL, $passwordHashed=false){
        return self::authenticate($realm, $users, $fail_msg, $fail_url, $passwordHashed);
    }

    /**
     * Authenticate using a function/method to process the auth logic. You can use this method to authenticate against a database data.
     * For HTTP digest auth, password key in by the user in the login dialog cannot be retrieved in plain text. 
     * Thus, storing of hased password is required, eg.:
     * <code>
     * //Hash format
     * md5($username .':'. $realm .':'. $password)
     * 
     * //Or use this method to generate the hash
     * DooDigestAuth::hashPassphrase($username, $password, $realm);
     * </code>
     * 
     * Example usage to authenticate with a database:
     * First, store the hash in a table.
     * <code>
     *   // after creating a user, store this
     *   // Table: user_realm_hash, Model: UserRealmHash
     *   // columns: username, realm, hash, user_id 
     *   $realmHash = new UserRealmHash();
     *   $realmHash->username = 'admin';
     *   $realmHash->realm = 'Admin Section';
     *   $realmHash->hash = DooDigestAuth::hashPassphrase('admin', 'password', 'Admin Section'); 
     *   $realmHash->insert();        
     * </code>
     * 
     * To begin the authentication process, put this line in your controller/method where you need to authenticate the user
     * <code>
     * // use $this->authUser
     * class AdminController extends DooController {
     *      public function manageAll(){
     *          DooDigestAuth::authWithFunc('Admin Login', array($this, 'authUser'), 'Login Failed!', null, true);
     *          //... CRUD code follows ...
     *      }
     * 
     *      public function authUser( $username, $realm ){
     *          $u = new UserRealmHash();
     *          $u->realm = $realm;
     *          $u->username = $username;
     *          $u = $u->getOne();
     *          if($u){
     *              return $u->hash;
     *          }
     *          return null;
     *      }
     * </code>
     *
     * <p>HTTP Digest Authentication doesn't work with PHP in CGI mode,
     * you have to add this into your .htaccess <code>RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]</code></p>
     *
     * @param string $realm Name of the authentication session
     * @param array $func Function/method to process the authentication. Pass the function name(string) or an array of the object and its method name. The function should accept 2 parameteres (username & realm), and should return the matching password string or null to deny the user.
     * @param string $failMsg Message to be displayed if the User cancel the login
     * @param string $failURL URL to be redirect if the User cancel the login
     * @param boolean $passwordHashed Use hashed password to authenticate.
     * @return string The username if login success.
     */
    public static function authWithFunc($realm, $func, $failMsg=NULL, $failURL=NULL, $passwordHashed=false){
        if(!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && strpos($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 'Digest')===0){
            $_SERVER['PHP_AUTH_DIGEST'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
            header('WWW-Authenticate: Digest realm="'.$realm.
                   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            header('HTTP/1.1 401 Unauthorized');
            if($failMsg!=NULL)
                die($failMsg);
            if($failURL!=NULL)
                die("<script>window.location.href = '$failURL'</script>");
            exit;
        }
        
        $data = self::httpDigestParse($_SERVER['PHP_AUTH_DIGEST']);
        
        if(is_string($func)){
            $hashedPassword = $func($data['username'], $realm);
        }
        else{
            $hashedPassword = $func[0]->{$func[1]}( $data['username'], $realm );
        }
        
        // analyze the PHP_AUTH_DIGEST variable
        if (!$data || !isset($hashedPassword)){
            header('WWW-Authenticate: Digest realm="'.$realm.
                   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            header('HTTP/1.1 401 Unauthorized');
            if($failMsg!=NULL)
                die($failMsg);
            if($failURL!=NULL)
                die("<script>window.location.href = '$failURL'</script>");
            exit;
        }

        // generate the valid response
        if ($passwordHashed) { 
            $A1 = $hashedPassword;             
        } else { 
            $A1 = md5($data['username'] .':'. self::getRealm($realm) .':'. $hashedPassword);             
        } 
        $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
        $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

        if ($data['response'] != $valid_response){
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="'.$realm.
                   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            if($failMsg!=NULL)
                die($failMsg);
            if($failURL!=NULL)
                die("<script>window.location.href = '$failURL'</script>");
            exit;
        }

        // ok, valid username & password
        return $data['username'];
    }    
    
    
    /** 
     * Get realm for HTTP Digest (works for safe mode = on/off) 
     * @return string
     */ 
    public static function getRealm($realm) { 
        if (ini_get('safe_mode')) { 
            return $realm . '-' . getmyuid();             
        } else { 
            return $realm;             
        } 
    }
    
    /**
     * Generates a hashed passphrase for a certain realm used in HTTP digest authentication.
     * Use this method to generate a hash value and store it in DB if you planned to use HTTP digest auth with hased password
     * @param string $username
     * @param string $password
     * @param string $realm
     * @return string
     */
    public static function hashPassphrase($username, $password, $realm){
        return md5($username .':'. self::getRealm($realm) .':'. $password);
    }
    
    /**
     * Method to parse the http auth header, works with IE.
     *
     * Internet Explorer returns a qop="xxxxxxxxxxx" in the header instead of qop=xxxxxxxxxxx as most browsers do.
     *
     * @param string $txt header string to parse
     * @return array An assoc array of the digest auth session
     */
    protected static function httpDigestParse($txt)
    {
        $res = preg_match("/username=\"([^\"]+)\"/i", $txt, $match);
        $data['username'] = (isset($match[1]))?$match[1]:null;
        $res = preg_match('/nonce=\"([^\"]+)\"/i', $txt, $match);
        $data['nonce'] = $match[1];
        $res = preg_match('/nc=([0-9]+)/i', $txt, $match);
        $data['nc'] = $match[1];
        $res = preg_match('/cnonce=\"([^\"]+)\"/i', $txt, $match);
        $data['cnonce'] = $match[1];
        $res = preg_match('/qop=([^,]+)/i', $txt, $match);
        $data['qop'] = str_replace('"','',$match[1]);
        $res = preg_match('/uri=\"([^\"]+)\"/i', $txt, $match);
        $data['uri'] = $match[1];
        $res = preg_match('/response=\"([^\"]+)\"/i', $txt, $match);
        $data['response'] = $match[1];
        return $data;
    }


}

