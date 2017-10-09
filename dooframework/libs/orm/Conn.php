<?php

namespace orm;

/**
 * Conexão ORM
 *
 * @author eduardo
 */
class Conn {
    
    /**
     * Tipo de conexão
     * @var string
     */
    private $type;
    
    /**
     * Usuario
     * @var string 
     */
    private $user;
    
    /**
     * Senha
     * @var string 
     */
    private $pass;
    
    /**
     * Nome do banco
     * @var string 
     */
    private $db;
    
    /**
     * Porta
     * @var string 
     */
    private $port;
    
    /**
     * Servidor
     * @var string 
     */
    private $server;
    
    public function getType() {
        return $this->type;
    }

    public function getUser() {
        return $this->user;
    }

    public function getPass() {
        return $this->pass;
    }

    public function getDb() {
        return $this->db;
    }

    public function getPort() {
        return $this->port;
    }

    public function getServer() {
        return $this->server;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function setPass($pass) {
        $this->pass = $pass;
        return $this;
    }

    public function setDb($db) {
        $this->db = $db;
        return $this;
    }

    public function setServer($server) {
        $this->server = $server;
        return $this;
    }


    
    
}
