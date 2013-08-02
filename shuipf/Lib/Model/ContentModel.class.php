<?php

/**
 * 内容模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ContentModel extends RelationModel {

    //关联定义
    public $_link = array();

    function __construct($name = '', $tablePrefix = '', $connection = '') {
        $this->_link = array(
            //主表 附表关联
            $name . "_data" => array(
                "mapping_type" => HAS_ONE,
                "class_name" => $name . "_data",
                "foreign_key" => "id"
            ),
        );
        parent::__construct($name, $tablePrefix, $connection);
    }

    /**
     * 检查表是否存在 
     * $table 不带表前缀
     */
    public function table_exists($table) {
        $tables = $this->list_tables();
        return in_array(C("DB_PREFIX") . $table, $tables) ? true : false;
    }

    /**
     * 读取全部表名
     */
    public function list_tables() {
        $tables = array();
        $data = $this->query("SHOW TABLES");
        foreach ($data as $k => $v) {
            $tables[] = $v['Tables_in_' . C("DB_NAME")];
        }
        return $tables;
    }

}

?>
