<?php

/**
 * 表单模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class FormguideModel extends ModelModel {
    
    protected $tableName = 'model'; 

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('name', 'require', '表单名称不能为空！'),
        array('tablename', 'require', '表名不能为空！'),
        array('name', '', '该表单名称已经存在！', 0, 'unique', 3),
        array('tablename', '', '该表名已经存在！', 0, 'unique', 3),
        array('tablename', 'checkTablesql', '创建表单所需要的SQL文件丢失，创建失败！', 1, 'callback', 3),
        array('tablename', 'checkTablename', '该表名是系统保留或者已经存在，不允许创建！', 0, 'callback', 3),
    );

}

?>
