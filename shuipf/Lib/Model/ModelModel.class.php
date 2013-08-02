<?php

/**
 * 模型管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ModelModel extends CommonModel {

    private $sql_path;
    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('name', 'require', '模型名称不能为空！'),
        array('tablename', 'require', '表名不能为空！'),
        array('name', '', '该模型名称已经存在！', 0, 'unique', 1),
        array('tablename', '', '该表名已经存在！', 0, 'unique', 3),
        array('tablename', 'checkTablesql', '创建模型所需要的SQL文件丢失，创建失败！', 1, 'callback', 3),
        array('tablename', 'checkTablename', '该表名是系统保留或者已经存在，不允许创建！', 0, 'callback', 3),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array("disabled", 0),
        array("sort", 0),
        array('addtime', 'time', 1, 'function'),
    );

    //检查需要创建的表名是否为系统保留名称
    public function checkTablename($tablename) {
        if (!$tablename) {
            return false;
        }
        //检查是否在保留内
        if (in_array($tablename, array("member_group", "member_content"))) {
            return false;
        }
        //检查该表名是否存在
        if ($this->table_exists($tablename)) {
            return false;
        }

        return true;
    }

    //检查SQL文件是否存在！
    public function checkTablesql($tablename = "") {
        //目录
        $this->sql_path = APP_PATH . C("APP_GROUP_PATH") . "/Models/Sql/";
        //检查主表结构sql文件是否存在
        if (!is_file($this->sql_path . "shuipfcms_zhubiao.sql")) {
            return false;
        }
        if (!is_file($this->sql_path . "shuipfcms_zhubiao_data.sql")) {
            return false;
        }
        if (!is_file($this->sql_path . "shuipfcms_insert.sql")) {
            return false;
        }
        if (!is_file($this->sql_path . "shuipfcms_member.sql")) {
            return false;
        }
        return true;
    }

    /**
     * 创建会员模型
     * @param type $TableName 表名
     * @param type $modelid 所属模型id
     * @return boolean
     */
    public function AddModelMember($TableName, $modelid) {
        if (empty($TableName)) {
            return false;
        }
        //表前缀
        $TableQianzui = C("DB_PREFIX");

        $Zsql = file_get_contents($this->sql_path . "shuipfcms_member.sql");
        if (!$Zsql) {
            return false;
        }
        $status = $this->sql_execute($Zsql, $TableQianzui, $TableName, $modelid);
        if ($status == false && is_bool($status)) {
            return false;
        }
        return true;
    }

    /**
     * 创建主表和副表
     * @param type $TableName 表名
     * @param type $modelid 所属模型id
     * @return boolean
     */
    public function AddModelTable($TableName, $modelid) {
        if (empty($TableName)) {
            return false;
        }
        //表前缀
        $TableQianzui = C("DB_PREFIX");

        $Zsql = file_get_contents($this->sql_path . "shuipfcms_zhubiao.sql");
        if (!$Zsql) {
            return false;
        }
        $status = $this->sql_execute($Zsql, $TableQianzui, $TableName, $modelid);
        if ($status == false && is_bool($status)) {
            return false;
        }

        $Fsql = file_get_contents($this->sql_path . "shuipfcms_zhubiao_data.sql");
        if (!$Fsql) {
            return false;
        }
        $status = $this->sql_execute($Fsql, $TableQianzui, $TableName, $modelid);
        if ($status == false && is_bool($status)) {
            return false;
        }

        $Fidel = file_get_contents($this->sql_path . "shuipfcms_insert.sql");
        $FidelArr = explode("\n", $Fidel);
        foreach ($FidelArr as $v) {
            if ($v) {
                $this->sql_execute($v, $TableQianzui, $TableName, $modelid);
            }
        }
        return true;
    }

    /**
     * 执行SQL
     * @param type $sql
     * @param type $prefix 表前缀
     * @param type $modelid 模型id
     * @return type
     */
    private function sql_execute($sql, $prefix, $TableName, $modelid) {
        if (!$sql) {
            return false;
        }
        $sql = str_replace(array(
            "@shuipfcms@",
            "@zhubiao@",
            "@modelid@",
            "\r\n"
                ), array(
            $prefix,
            $TableName,
            $modelid,
            ""
                ), $sql);
        return $this->execute($sql);
    }

    /**
     * 删除表
     * $table 不带表前缀
     */
    public function DeleteTable($table) {
        $status = $this->table_exists($table);
        if ($status) {
            $this->drop_table($table);
        }
        return true;
    }

    /**
     * 删除模型
     * @param type $modelid 模型id
     * @return boolean
     */
    public function delete_model($modelid) {
        if ((int) $modelid <= 0) {
            return false;
        }
        //这里可以根据缓存获取表名
        $modeldata = $this->where(array("modelid" => $modelid))->find();
        if (!$modeldata) {
            return false;
        }
        //表名
        $model_table = $modeldata['tablename'];
        //删除模型数据
        $this->where(array("modelid" => $modelid))->delete();
        //删除所有和这个模型相关的字段
        D("Model_field")->where(array("modelid" => $modelid))->delete();
        //删除主表
        $this->DeleteTable($model_table);
        if ((int) $modeldata['type'] != 2) {
            //删除副表
            $this->DeleteTable($model_table . "_data");
        }
        return true;
    }

    /**
     * 根据模型类型取得数据用于缓存
     * @param type $type
     * @return type
     */
    public function Cache($type) {
        $data = $this->where(array("type" => $type, "disabled" => 0))->select();
        $Cache = array();
        foreach ($data as $v) {
            $Cache[$v['modelid']] = $v;
        }
        return $Cache;
    }

    /**
     * 生成模型缓存，以模型ID为下标的数组 
     * @return boolean
     */
    public function model_cache() {
        F("Model", $this->Cache(0));
        D("Model_field")->model_field_cache();
        return true;
    }

    /**
     * 生成会员模型缓存 
     * @return boolean
     */
    public function MemberModelCache() {
        F("Model_Member", $this->Cache(2));
        return true;
    }

    /**
     * 后台有更新则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("Model", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->model_cache();
    }

    //更新数据后更新缓存
    public function _after_update($data, $options) {
        parent::_after_update($data, $options);
        $this->model_cache();
    }

    //插入数据后更新缓存
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        $this->model_cache();
    }

}

?>
