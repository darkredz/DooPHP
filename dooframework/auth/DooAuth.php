<?php
/**
 * DooAuth class, manage user authentication
 * @author Gustavo Seip <glseip@gmail.com>
 * @license http://www.doophp.com/license
 * @version $Id: DooAuth.php 2009-10-06 15:10:12
 * @package doo.auth
 * @since 1.3
 *
 */

class DooAuth {
    /**
     * HIGH security level
     * @var <Integer> 
     */
    public static $HIGH_LEVEL = 1;
    /**
     * MEDIUM security level
     * @var <Integer>
     */
    public static $MEDIUM_LEVEL = 2;
    /**
     * LOW security level
     * @var <Integer>
     */
    public static $LOW_LEVEL = 3;
    /**
     * Discarded form indicator
     * @var <Integer>
     */
    public static $FORM_DISCARDED = 1;
    /**
     * Timeout form indicator
     * @var <Integer>
     */
    public static $FORM_TIMEOUT = 2;
    /**
     * DooSession instance
     * @var <DooSession>
     */
    public $app_session;
    /**
     * Application name
     * @var <String>
     */
    public $app_name;
    /**
     * A random string for hashing
     * @var <String>
     */
    public $salt = 'DYhG93b0qyJfHxfs2tuVoUubWwvjiR4G0FgaC9mi'; //For default. Please, change!!
    /**
     * maximum time for downtime
     * $max_downtime = 0 , off downtime check
     * @var <Integer>
     */
    public $max_downtime = 60; //time in seconds
    /**
     * Security level
     * @var <Integer>
     */
    public $security_level;
    /**
     * Maximun time for form timeout
     * @var <Integer>
     */
    public $max_allowed_time = 60; //time frame - in seconds
    /**
     * Minimun time for form timeout
     * @var <Integer>
     */
    public $min_allowed_time = 20; //time frame - in seconds

    /**
     * Constructor - returns an instance object of DooAuth
     */
    public function __construct($app_name=null) {
        $this->app_session = Doo::session(isset ($app_name) ? $app_name : 'generic_app_name');
        $this->security_level = self::$LOW_LEVEL; //For default
    }

    /**
     * Get auth data
     * @return <Mixed>
     */
    public function getData() {
        return ($this->isValid() ? $this->app_session->AuthData : false);
    }

    /**
     * Finalize autentication
     */
    public function finalize() {
        if (!$this->app_session->isDestroyed())
            $this->app_session->destroy();
    }

    /**
     * Set auth data for user session
     * @param <String> User name
     * @param <Mixed> User group
     */
    public function setData($username, $group=FALSE) {
        $this->app_session->AuthData = array();
        $this->app_session->AuthData['username'] = $username;
        $this->app_session->AuthData['group'] = $group;
        $this->app_session->AuthData['security_level'] = $this->security_level;
        $this->app_session->AuthData['time'] = time();
        switch ($this->security_level) {
            case self::$HIGH_LEVEL:
                $this->app_session->AuthData['initialized'] = true;
                $this->app_session->AuthData['fingerprint'] = md5($_SERVER['HTTP_USER_AGENT'].$this->salt);
                session_regenerate_id();
                $this->app_session->AuthData['id'] = md5($this->app_session->getId());
                $this->app_session->AuthData['max_downtime'] = $this->max_downtime * 15;
                $this->app_session->AuthData['max_allowed_time'] = $this->max_allowed_time * 11; //~25% of max_downtime
                $this->app_session->AuthData['min_allowed_time'] = $this->min_allowed_time;
                break;
            case self::$MEDIUM_LEVEL:
                $this->app_session->AuthData['initialized'] = true;
                $this->app_session->AuthData['fingerprint'] = md5($_SERVER['HTTP_USER_AGENT'].$this->salt);
                $this->app_session->AuthData['max_downtime'] = $this->max_downtime * 120;
                $this->app_session->AuthData['max_allowed_time'] = $this->max_allowed_time * 60; //~50% of max_downtime
                $this->app_session->AuthData['min_allowed_time'] = $this->min_allowed_time;
                break;
            case self::$LOW_LEVEL:
                $this->app_session->AuthData['initialized'] = true;
                $this->app_session->AuthData['max_downtime'] = $this->max_downtime * 360;
                $this->app_session->AuthData['max_allowed_time'] = $this->max_allowed_time * 90; //~75% of max_downtime
                $this->app_session->AuthData['min_allowed_time'] = $this->min_allowed_time;
                break;
            default:
                break;
        }
    }

    /**
     * Verify user session
     * @see http://phpsec.org/projects/guide/4.html
     * @see http://www.serversidemagazine.com/php/session-hijacking
     * @return <Boolean>
     */
    public function isValid() {
        $ad = $this->app_session->AuthData;
        if (isset ($ad)) {
            $downtime = ((time()-$ad['time']) > $ad['max_downtime']) ;
            if (!$ad['initialized'] || !isset ($ad['username']) || $downtime)
                return false;
            if (($ad['security_level']==self::$MEDIUM_LEVEL || $ad['security_level']==self::$HIGH_LEVEL)
                && $ad['fingerprint'] != md5($_SERVER['HTTP_USER_AGENT'].$this->salt))
                return false;
            if ($ad['security_level']==self::$HIGH_LEVEL && $ad['id']!=md5($this->app_session->getId()))
                return false;
            $this->app_session->AuthData['time'] = time();
            return true;
        }
        return false;
    }

    /**
     * Get token for security purpose (secure forms, etc)
     * @see http://www.serversidemagazine.com/php/php-security-measures-against-csrf-attacks
     * @see http://www.serversidemagazine.com/php/session-hijacking
     * @return <Mixed>
     */
    public function securityToken() {
        return uniqid(rand(), true);
    }

    /**
     * Validate security token
     * @see http://www.serversidemagazine.com/php/php-security-measures-against-csrf-attacks
     * @return <Mixed>
     */
    public function validateSecurityToken() {
        $ad = $this->app_session->AuthData;
        if (isset ($ad)) {
            $time = time() - $ad['time'];
            if ($time < $ad['min_allowed_time'])
                return self::$FORM_DISCARDED;
            elseif ($time > $ad['max_allowed_time'])
                return self::$FORM_TIMEOUT;
            else
                return true;
        }
        return false;
    }
}
?>
