<?php
 namespace orm\mysql;

/**
 * Conexão MYSQL
 *
 * @author eduardo
 */
class ConnMysql implements \orm\IConn{
    
    /**
     * Conexão do banco de dados
     * @var \orm\Conn
     */
    private $connObj;
    
    public function __construct($connObj) {
        $this->connObj = $connObj;
    }
    
    /**
     * Gera e retorna o DSN
     * @return strign
     */
    private function generateDSN(){
        $host=$this->connObj->getServer();
        $db=$this->connObj->getDb();
        $port=$this->connObj->getPort();
        if(is_null($port)){
            $port = 3066;
        }
        return "mysql:host=$host;port=$port;dbname=$db";
    }
    
    /**
     * Cria a conexão
     * @return \PDO
     */
    public function conn(){
        new \PDO($this->generateDSN(),$this->connObj->getUser(),$this->connObj->getPass());
    }
    
    
}
