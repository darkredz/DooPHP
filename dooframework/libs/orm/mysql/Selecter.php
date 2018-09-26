<?php

namespace orm\mysql;

/**
 * Description of Selecter
 *
 * @author eduardo
 */
class Selecter implements \orm\ISelecter {

    public static function getPkName($table,$conn=null) {
        $arr = \Orm::fetchAll("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'",$conn);
        if (count($arr) == 1) {
            return $arr[0]['Collumn_name'];
        } else {
            $arrPk = array();
            foreach ($arr as $arrItem) {
                $arrPk[] = $arrItem['Collumn_name'];
            }
            return $arrPk;
        }
    }

    /**
     * Retorna os dados da PK
     * @param type $table
     * @param type $pk
     * @param type $conn
     * @return type
     */
    public static function getByPk($table, $pk,$conn=null) {
        $sql = "SELECT * FROM {$table} WHERE ";
        $pkName = self::getPkName($table);
        if (is_array($pk)) {
            if (count($pkName) == count($pk)) {
                $arrWhere = array();
                foreach ($pk as $k=>$pkItem) {
                    $arrWhere = "$pkName[$k]={$pkItem}";
                }
                $sql .= implode(' AND ', $arrWhere);
            }
        }else{
            $sql .= "$pkName={$pk}";
        }
        return \Orm::fetch($sql, $conn);
    }

}
