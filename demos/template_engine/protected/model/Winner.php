<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Winner
 *
 * @author leng
 */
class Winner {
    var $fullname;
    var $gender;
    var $Physical;

    function  __construct() {
        Doo::loadModel('Physical');
        $this->Physical = new Physical;
    }
}
?>