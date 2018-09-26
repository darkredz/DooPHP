<?php

namespace orm;

/**
 * Iterface de selecter
 * @author eduardo
 */
interface ISelecter {

    /**
     * Retorna a Primary Key, caso exista mais de uma, retorna um array
     * @param string $table Nome da tabela
     * @return array Retorna a PK
     */
    public function getPkName($table);
    
    /*
     * Retorna a linha da PK atual
     * @param string $table Nome da tabela
     * @param string $pk Vaçpr da PK
     * @return array Retorna a PK
     */
    public function getByPK($table,$pk);
}
