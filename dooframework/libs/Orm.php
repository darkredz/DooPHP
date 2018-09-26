<?php

/**
 * Conexão ORM
 *
 * @author eduardo
 */
class Orm {

    
    /**
     * Retorna a classe selecter
     * @param string $conn nome da conexão
     * @return \orm\ISelecter
     */
    private static function getSelecterClass($conn=null){
        $type = \orm\Manager::getDataConn($conn)->getType();
        $type = strtolower($type);
        $class = "\\orm\\$type\\Selecter";
        return new $class();
    }
    /**
     * Retorna a primeira linha do sql
     * @param string $sql
     * @param string $conn
     * @return array
     */
    public static function fetch($sql, $conn = null) {
        $connObj = \orm\Manager::getConn($conn);
        $prepare = $connObj->prepare($sql);
        $prepare->execute();
        return $prepare->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna um array do sql
     * @param string $sql
     * @param string $conn
     * @return array
     */
    public static function fetchAll($sql, $conn = null) {
        $connObj = \orm\Manager::getConn($conn);
        $prepare = $connObj->prepare($sql);
        $prepare->execute();
        return $prepare->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Executa um sql
     * @param string $sql
     * @param string $conn
     * @return bool
     */
    public static function exec($sql, $conn = null) {
        $connObj = \orm\Manager::getConn($conn);
        $prepare = $connObj->exec($sql);
    }
    
    /**
     * Retorna um array da PK
     * @param string $table
     * @param string $pk
     * @param string $conn
     * @return array
     */
    public static function getByPk($table,$pk,$conn){
        $selecterObj = self::getSelecterClass($conn);
        return $selecterObj->getByPK($table, $pk);
    }

}
