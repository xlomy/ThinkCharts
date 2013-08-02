<?php

/**
 * 数据读取，主要用于前台数据显示
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class content_output {
    
    public $modelid, $fields, $data, $catid, $categorys, $id;

    function __construct($modelid, $catid = 0, $categorys = array()) {
        $this->modelid = $modelid;
        $this->catid = $catid;
        $this->categorys = $categorys;
        $this->fields = F("Model_field_" . $modelid);
    }

    function get($data) {
        $this->data = $data;
        $this->id = $data['id'];
        $info = array();
        foreach ($this->fields as $field => $v) {
            if (!isset($data[$field])){
                continue;
            }
            $func = $v['formtype'];
            $value = $data[$field];
            $result = method_exists($this, $func) ? $this->$func($field, $data[$field]) : $data[$field];
            if ($result !== false)
                $info[$field] = $result;
        }
        return $info;
    }

//结尾 需要变成 }? > 
}?>