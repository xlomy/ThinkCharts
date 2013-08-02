<?php

/**
 * 数据删除时回调
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class content_delete {
    
    public $modelid, $fields, $data, $catid, $id;

    function __construct($id,$catid,$modelid) {
        $this->modelid = $modelid;
        $this->catid = $catid;
        $this->fields = F("Model_field_" . $modelid);
        if(!$this->fields){
            return false;
        }
    }

    function get($data) {
        $this->data = $data;
        $this->id = $data['id'];
        $info = array();
        foreach ($this->fields as $field => $v) {
            $func = $v['formtype'];
            if(!$func){
                continue;
            }
            $value = $data[$field];
            if(method_exists($this, $func)){
                $this->$func($field, $value);
            }
        }
        return true;
    }

//结尾 需要变成 }? > 
}?>