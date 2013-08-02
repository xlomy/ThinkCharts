<?php

/* * 
 * 字段编辑相关SQL执行语句
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
//默认值
$defaultvalue = isset($_POST['setting']['defaultvalue']) ? $_POST['setting']['defaultvalue'] : '';
//是否正整数 和整数 1为正整数，-1是为整数
$minnumber = isset($_POST['setting']['minnumber']) ? $_POST['setting']['minnumber'] : 1;
//小数位数
$decimaldigits = isset($_POST['setting']['decimaldigits']) ? $_POST['setting']['decimaldigits'] : '';

switch ($field_type) {
    case 'varchar':
        if (!$maxlength)
            $maxlength = 255;
        $maxlength = min($maxlength, 255);
        $fieldtype = $issystem ? 'CHAR' : 'VARCHAR';
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` $fieldtype( $maxlength ) NOT NULL DEFAULT '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'tinyint':
        $minnumber = intval($minnumber);
        $defaultvalue = intval($defaultvalue);
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` TINYINT " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'number':
        $minnumber = intval($minnumber);
        $defaultvalue = $decimaldigits == 0 ? intval($defaultvalue) : floatval($defaultvalue);
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` " . ($decimaldigits == 0 ? 'INT' : 'FLOAT') . " " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'smallint':
        $minnumber = intval($minnumber);
        $defaultvalue = intval($defaultvalue);
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` SMALLINT " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'mediumint':
        $minnumber = intval($minnumber);
        $defaultvalue = intval($defaultvalue);
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` MEDIUMINT " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;


    case 'int':
        $minnumber = intval($minnumber);
        $defaultvalue = intval($defaultvalue);
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` INT " . ($minnumber >= 0 ? 'UNSIGNED' : '') . " NOT NULL DEFAULT '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'mediumtext':
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` MEDIUMTEXT NOT NULL";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'text':
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` TEXT NOT NULL";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'date':
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` DATE NULL";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'datetime':
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` DATETIME NULL";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'timestamp':
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `$field` TIMESTAMP NOT NULL";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case 'readpoint':
        $defaultvalue = intval($defaultvalue);
        $sql = "ALTER TABLE `$tablename` CHANGE `$oldfield` `readpoint` smallint(5) unsigned NOT NULL default '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case "double":
        $defaultvalue = intval($defaultvalue);
        $sql = "alter table `$tablename` change `$oldfield` `$field` DOUBLE NOT NULL default '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case "float":
        $defaultvalue = intval($defaultvalue);
        $sql = "alter table `$tablename` change `$oldfield` `$field` FLOAT NOT NULL default '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case "bigint":
        $defaultvalue = intval($defaultvalue);
        $sql = "alter table `$tablename` change `$oldfield` `$field`  BIGINT NOT NULL default '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case "longtext":
        $sql = "alter table `$tablename` change `$oldfield` `$field`  LONGTEXT NOT NULL";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;

    case "char":
        $sql = "alter table `$tablename` change `$oldfield` `$field`  CHAR(255) NOT NULL default '$defaultvalue'";
        $status = M('')->query($sql);
        if ($status == false && is_bool($status)) {
            $this->error("字段更新失败！");
        }
        break;
    //特殊自定义字段
    case 'pages':

        break;
    default:
        $this->error("字段类型" . $field_type . "不存在相应信息！");
        break;
}
?>