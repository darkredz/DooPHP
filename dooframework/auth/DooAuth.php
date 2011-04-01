<?php
/**
 * DooAuth class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2011 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * Prepare functionality for authentication in an application with settings to protect against CSRF attacks.
 * The authentication session by default has an expiry duration according to the security level defined.
 * By default, LEVEL_HIGH is 15*60 seconds, LEVEL_MEDIUM is 120*60 seconds, LEVEL_LOW is 360*60 seconds.
 *
 * Form post can be protected from CSRF attack by using DooAuth::validateForm() which accepts a token is generated
 * using DooAuth::securityToken() and render along with the form.
 * 
 * By default, form post has a minimum time frame of 20 seconds. The minimum can be changed using DooAuth::setFormPostMinTime()
 * 
 * Form session will expire after a certain amount of time. This duration is set by default according to the security level defined.
 * By default, LEVEL_HIGH is 60*11 seconds, LEVEL_MEDIUM is 60*60 seconds, LEVEL_LOW is 90*60 seconds.
 * This form session expiry time can be changed using DooAuth::setFormSessionExpire();
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @package doo.auth
 * @since 1.3
 */

class DooAuth {
    /**
     * HIGH security level
     * @var integer
     */
    const LEVEL_HIGH = 1;
    
    /**
     * MEDIUM security level
     * @var integer
     */
    const LEVEL_MEDIUM = 2;
    
    /**
     * LOW security level
     * @var integer
     */
    const LEVEL_LOW = 3;
    
    /**
     * Discarded form indicator
     * @var string
     */
    const FORM_DISCARDED = 'form_discarded';
    
    /**
     * Timeout form indicator
     * @var string
     */
    const FORM_TIMEOUT = 'form_timeout';
    
    /**
     * DooSession instance
     * @var DooSession
     */
    protected $appSession;
    
    /**
     * Application name
     * @var string
     */
    protected $appName;
    
    /**
     * A random string for hashing
     * @var string
     */
    protected $salt;
    
    /**
     * Duration(in seconds) for auth session to expire
     * @var integer
     */
    protected $authSessionExpire; 
    
    /**
     * Security level
     * @var integer
     */
    protected $securityLevel;
    
    /**
     * Duration(in seconds) for form session timeout
     * @var integer
     */
    protected $formSessionExpire;
    
    /**
     * Minimum time-frame(in seconds) for form post to discard spam bots or an automated CSRF attack
     * @var integer
     */
    protected $formPostMinTime=20;
    
    /**
     * Indicator for valid authetication
     * @var boolean
     */
    protected $isValid = false;

    /**
     * Username from the session
     * @var string
     */
    public $username;

    /**
     * Group name from the session
     * @var string
     */
    public $group;

    /**
     * User ID from the session
     * @var string
     */
    public $userID;	
	
    /**
     * Constructor - returns an instance object of DooAuth
     */
    public function __construct($appName) {
        $this->setApplicationName($appName);
    }

    /**
     * Start auth component
     */
    public function start() {
        $this->appSession = Doo::session($this->getApplicationName());
        $this->validate();
    }

    /**
     * Finalize autentication
     */
    public function finalize() {
        if (!$this->appSession->isDestroyed())
            $this->appSession->destroy();
    }

    /**
     * Set auth data for user session
     * @param string User name
     * @param mixed User group
     * @param mixed User ID
     */
    public function setData($username, $group=false, $userID=null) {
        $this->appSession->AuthData = array();
        $this->username = $this->appSession->AuthData['_username'] = $username;
        $this->group = $this->appSession->AuthData['_group'] = $group;
		if($userID!==null)
			$this->userID = $this->appSession->AuthData['_userID'] = $userID;
			
        $this->appSession->AuthData['_time'] = time();
        $this->appSession->AuthData['_securityLevel'] = $this->getSecurityLevel();
        
        switch ($this->securityLevel) {
            case self::LEVEL_HIGH:
                $this->appSession->AuthData['_fingerprint'] = md5($_SERVER['HTTP_USER_AGENT'].$this->getSalt());
                session_regenerate_id();
                $this->appSession->AuthData['_id'] = md5($this->appSession->getId());
                break;
            case self::LEVEL_MEDIUM:
                $this->appSession->AuthData['_fingerprint'] = md5($_SERVER['HTTP_USER_AGENT'].$this->getSalt());
                break;
        }                    
    }

    /**
     * Validate authentication data
     * @see http://phpsec.org/projects/guide/4.html
     * @see http://www.serversidemagazine.com/php/session-hijacking
     * @return boolean
     */
    public function validate() {
        $authData = $this->appSession->AuthData;
        $securityLevel = $authData['_securityLevel'];
        
        if ( isset($this->appSession) && $authData!==null ) {
            if ( ($securityLevel==self::LEVEL_LOW && (isset($authData['_username']) 
                    || ((time() - $authData['_time']) <= $this->getSessionExpire()))) || //LEVEL_LOW
                    
                    (($securityLevel==self::LEVEL_MEDIUM || $securityLevel==self::LEVEL_HIGH) //LEVEL_MEDIUM
                         && $authData['_fingerprint'] == md5($_SERVER['HTTP_USER_AGENT'].$this->getSalt())) ||
                                 
                    ($securityLevel==self::LEVEL_HIGH && $this->_id==md5($this->appSession->getId())) ) { //LEVEL_HIGH
                
                $this->isValid = true;
                $this->appSession->AuthData['_time'] = time();
                $this->username = $authData['_username'];
                $this->group = $authData['_group'];
            }
        } else
            $this->isValid = false;
    }

    /**
     * Get token for security purpose (secure forms, etc)
     * @see http://www.serversidemagazine.com/php/php-security-measures-against-csrf-attacks
     * @see http://www.serversidemagazine.com/php/session-hijacking
     * @return mixed
     */
    public function securityToken() {
        if ($this->isValid()) {
            return $this->appSession->AuthData['_formToken'] = uniqid(rand(), true);
        }
        return false;
    }

    /**
     * Validate form with security token
     * @see http://www.serversidemagazine.com/php/php-security-measures-against-csrf-attacks
     * @return mixed
     */
    public function validateForm($receivedToken) {
        if ($this->isValid && isset($receivedToken)) {
            if ($this->appSession->AuthData['_formToken']!=$receivedToken)
                return false;
            $time = time() - $this->appSession->AuthData['_time'];
            
            if ($time < $this->getFormPostMinTime())
                return self::FORM_DISCARDED;
            elseif ($time > $this->getFormSessionExpire())
                return self::FORM_TIMEOUT;
            return true;
        }
        return false;
    }

    /////////// SETTERs & GETTERs ////////////
    public function setApplicationName($appName) {
        if (!isset ($appName))
            throw new DooAuthException("Application name cannot be empty");
        $this->appName = $appName;
    }
    
    public function getApplicationName() {
        if (!isset ($this->appName))
            throw new DooAuthException("Application name not defined");
        return $this->appName;
    }
    
    public function setSalt($salt) {
        if (!isset ($salt))
            throw new DooAuthException("Salt cannot be empty");
        $this->salt = $salt;
    }
    
    public function getSalt() {
        if (!isset ($this->salt))
            throw new DooAuthException("Salt not defined");
        return $this->salt;
    }
    
    public function setSecurityLevel($securityLevel) {
        if (!isset ($securityLevel))
            throw new DooAuthException("Security level cannot be empty");
        $this->securityLevel = $securityLevel;        
    }
    
    public function getSecurityLevel() {
        if (!isset($this->securityLevel))
            throw new DooAuthException("Security level not defined");        
        return $this->securityLevel;
    }
    
    /**
     * Set the duration(in seconds) for auth session to expire
     * @param integer $duration 
     */
    public function setSessionExpire($duration) {
        if (!isset ($duration))
            throw new DooAuthException("Session expire duration cannot be empty");
        $this->authSessionExpire = $duration;
    }
    
    /**
     * Get the duration(in seconds) for auth session to expire
     * @return integer
     */
    public function getSessionExpire() {
        if(!isset($this->authSessionExpire)){
            switch ($this->securityLevel) {
                case self::LEVEL_HIGH:
                    return 15 * 60;
                    break;
                case self::LEVEL_MEDIUM:
                    return 120 * 60;
                    break;
                case self::LEVEL_LOW:
                    return 360 * 60;
                    break;
                default:
                    throw new DooAuthException("Session expire duration not defined");
                    break;
            }
        }
                        
        return $this->authSessionExpire;
    }
    
    /**
     * Set the minimum time-frame(in seconds) for form post to discard spam bots or an automated CSRF attack
     * @param integer $duration 
     */
    public function setFormPostMinTime($duration) {
        if (!isset ($duration))
            throw new DooAuthException("Form post minimum time-frame cannot be empty");
        $this->formPostMinTime = $duration;
    }
    
    /**
     * Get the minimum time-frame(in seconds) for form post to discard spam bots or an automated CSRF attack
     * @return integer
     */
    public function getFormPostMinTime() {
        if (!isset ($this->formPostMinTime))
            throw new DooAuthException("Form post minimum time-frame not defined");
        return $this->formPostMinTime;
    }
    
    /**
     * Set the duration(in seconds) for form session to expire
     * @param integer $duration 
     */
    public function setFormSessionExpire($duration) {
        if (!isset ($duration))
            throw new DooAuthException("Form session expire duration cannot be empty");
        $this->formSessionExpire = $duration;
    }
    
    /**
     * Get the duration(in seconds) for form session to expire
     * @return integer
     */
    public function getFormSessionExpire() {        
        if(empty($this->authSessionExpire)){
            switch ($this->securityLevel) {
                case self::LEVEL_HIGH:
                    return 11 * 60;
                    break;
                case self::LEVEL_MEDIUM:
                    return 60 * 60;
                    break;
                case self::LEVEL_LOW:
                    return 90 * 60;
                    break;
            }                        
        }
        
        return $this->formSessionExpire;
    }        
    
    public function isValid() {
        return $this->isValid;
    }

    ////////// Deprecated ///////
    /**
     * @deprecated Deprecated since 1.5. Use setFormPostMinTime() instead
     */
    public function setPostExpire($duration){
        $this->setFormPostMinTime($duration);
    }
    
    /**
     * @deprecated Deprecated since 1.5. Use getFormPostMinTime() instead
     */
    public function getPostExpire(){
        return $this->getFormPostMinTime();
    }
    
    /**
     * @deprecated Deprecated since 1.5. Use setFormSessionExpire() instead
     */
    public function setPostWait($duration){
        $this->setFormSessionExpire($duration);
    }
    
    /**
     * @deprecated Deprecated since 1.5. Use getFormSessionExpire() instead
     */
    public function getPostWait(){
        return $this->getFormSessionExpire();
    }
    
    ////////////////// Magic ////////////////////
    public function  __set($name,  $value) {
        if (!isset ($this->appSession->AuthData))
            throw new DooAuthException("authentication data not initialized");
        return $this->appSession->AuthData[$name] = $value;
    }
    public function  __get($name) {
        if (!isset ($this->appSession->AuthData))
            throw new DooAuthException("authentication data not initialized");
        return $this->appSession->AuthData[$name];
    }
}

class DooAuthException extends Exception {
    
}
