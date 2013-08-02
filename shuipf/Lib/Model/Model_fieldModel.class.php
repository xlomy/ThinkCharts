<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Model_fieldModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('modelid', 'require', '请选择模型！'),
        array('field', 'require', '字段名称必须填写！'),
        array('name', 'require', '字段别名必须填写！'),
        //array('field','checkField','字段只支持英文！',1,'callback',3),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
    );

    /**
     * 根据模型ID读取全部字段信息
     */
    public function Modelfield($modelid) {
        $data = $this->where(array("modelid" => $modelid))->order(array("listorder" => "ASC"))->select();
        return $data;
    }

    /**
     * 根据字段类型，增加对应的字段到相应模型表里面
     * $field_type 字段类型
     * $field 对应字段所需配置
     */
    public function addfield($field_type, $field = array()) {
        //表名
        $tablename = $field['tablename'];
        //字段名
        $fieldname = $field['fieldname'];
        //最大长度
        $maxlength = $field['maxlength'];
        //最小值
        $minlength = $field['minlength'];

        $defaultvalue = isset($_POST['setting']['defaultvalue']) ? $_POST['setting']['defaultvalue'] : '';
        $minnumber = isset($_POST['setting']['minnumber']) ? $_POST['setting']['minnumber'] : 1;
        $decimaldigits = isset($_POST['setting']['decimaldigits']) ? $_POST['setting']['decimaldigits'] : '';

        switch ($field_type) {
            case "varchar":
                if (!$maxlength)
                    $maxlength = 255;
                $maxlength = min($maxlength, 255);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` VARCHAR( $maxlength ) NOT NULL DEFAULT '$defaultvalue'";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "tinyint":
                if (!$maxlength) {
                    $maxlength = 3;
                }
                $minnumber = intval($minnumber);
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` TINYINT( $maxlength ) " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "number":
                $minnumber = intval($minnumber);
                $defaultvalue = $decimaldigits == 0 ? intval($defaultvalue) : floatval($defaultvalue);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` " . ($decimaldigits == 0 ? 'INT' : 'FLOAT') . " " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "smallint":
                $minnumber = intval($minnumber);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` SMALLINT " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "int":
                $minnumber = intval($minnumber);
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` INT " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "bigint":
                $minnumber = intval($minnumber);
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` INT BIGINT NOT NULL DEFAULT '$defaultvalue'";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "float":
                $minnumber = intval($minnumber);
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` INT FLOAT NOT NULL DEFAULT '$defaultvalue'";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "double":
                $minnumber = intval($minnumber);
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` INT DOUBLE NOT NULL DEFAULT '$defaultvalue'";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "mediumint":
                $minnumber = intval($minnumber);
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` INT " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "longtext":
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname`  LONGTEXT NOT NULL ";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "mediumtext":
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` MEDIUMTEXT NOT NULL";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "text":
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` TEXT NOT NULL";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "date":
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` DATE NULL";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "datetime":
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` DATETIME NULL";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            case "timestamp":
                $sql = "ALTER TABLE `$tablename` ADD `$fieldname` TIMESTAMP NOT NULL";
                $satatus = $this->query($sql);
                if (is_bool($satatus) && $satatus == false) {
                    return false;
                }
                return true;
                break;
            //特殊自定义字段
            case "pages":
                $this->query("ALTER TABLE `$tablename` ADD `paginationtype` TINYINT( 1 ) NOT NULL DEFAULT '0'");
                $this->query("ALTER TABLE `$tablename` ADD `maxcharperpage` MEDIUMINT( 6 ) NOT NULL DEFAULT '0'");
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * 编辑字段，更改字段名
     */
    public function editfield($field_type, $fieldname) {
        
    }

    /**
     * 删除字段 
     * @param 不带表前缀的表名
     * @param 需要删除的字段
     */
    public function drop_field($tablename, $field) {
        //判断表是否存在
        if ($this->table_exists($tablename)) {
            $tbname = C("DB_PREFIX") . $tablename;
            //特殊字段处理
            if($field == "pages"){
                if ($this->field_exists($tablename, "paginationtype")) {
                    $this->execute("ALTER TABLE `$tbname` DROP `paginationtype`;");
                }
                if ($this->field_exists($tablename, "maxcharperpage")) {
                    $this->execute("ALTER TABLE `$tbname` DROP `maxcharperpage`;");
                }
                return true;
            }
            //检查字段是否存在
            if ($this->field_exists($tablename, $field)) {
                $status = $this->execute("ALTER TABLE `$tbname` DROP `$field`;");
                if (is_bool($status) && $status == false) {
                    return false;
                }
                return true;
            }
        } else {
            return false;
        }
    }
    
    //生成模型字段缓存
    public function model_field_cache(){
        $Mode = M("Model")->select();
        foreach ($Mode as $modelid) {
            $data = $this->where(array("modelid" => $modelid['modelid'], "disabled" => 0))->order(" listorder ASC ")->select();
            foreach ($data as $key => $value) {
                $data[$value['field']] = $value;
                unset($data[$key]);
            }
            F("Model_field_" . $modelid['modelid'], $data);
        }
    }
    
     //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->model_field_cache();
    }
    
    //更新数据后更新缓存
    public function _after_update($data, $options) {
        parent::_after_update($data, $options);
        $this->model_field_cache();
    }

    //插入数据后更新缓存
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        $this->model_field_cache();
    }
}

?>
