<?php
namespace orm;

/**
 * Gerenciador de conexões
 *
 * @author eduardo
 */
class Manager {
    
    /**
     * Nome da conexão principal
     * @var string
     */
    private static $default;
    
    /**
     * Cria um array de conexões
     * @var array
     */
    private static $arrConn = array();
    
    /**
     * Cria um array de conexões
     * @var array
     */
    private static $arrConnData = array();
    
    /**
     * Adiciona uma conexao
     * @param IConn $connObj
     * @param strign $name
     * @param bool $isDefault
     */
    public static function addConn($connObj,$name,$isDefault=false){
        self::$arrConn[$name] = $connObj->conn();
        self::$arrConnData[$name] = $connObj;
        if($isDefault){
            self::$default = $name;
        }
    }
    
    /**
     * Retorna o PDO
     * @param strign $name nome da conexão
     * @return \PDO
     */
    public static function getConn($name=null){
        if(is_null($name)){
            $name = self::$default;
        }
        return self::$arrConn[self::$default];
    }
    
    /**
     * Retorna os dados da conexão
     * @param type $name
     * @return Conn
     */
    public static function getDataConn($name=null){
        if(is_null($name)){
            $name = self::$default;
        }
        return self::$arrConnData[$name];
    }
}
