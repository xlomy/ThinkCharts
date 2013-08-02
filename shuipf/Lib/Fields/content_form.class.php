<?php

/**
 * 字段显示输入表单类
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class content_form {

    public $modelid, $fields, $id, $categorys, $catid, $formValidateRules, $formValidateMessages, $formJavascript;

    /**
     * 构造函数
     * @param type $modelid 模型ID
     * @param type $catid 栏目ID
     * @param type $categorys 栏目数据
     */
    function __construct($modelid, $catid = 0, $categorys = array()) {
        $this->modelid = $modelid;
        $this->catid = $catid;
        $this->categorys = $categorys;
        $this->fields = F("Model_field_" . $modelid);
    }

    /**
     * 获取模型字段信息
     * @param type $data
     * @return type 
     */
    function get($data = array()) {
        //信息ID
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->data = $data;
        $info = array();

        foreach ($this->fields as $field => $v) {
            //判断是否后台
            if (defined('IN_ADMIN') && IN_ADMIN) {
                //判断是否内部字段，如果是，跳过
                if ($v['iscore']) {
                    continue;
                }
            } else {
                //判断是否内部字段或者，是否禁止前台投稿字段
                if ($v['iscore']) {
                    continue;
                }
                if (!$v['isadd']) {
                    continue;
                }
            }
            $func = $v['formtype'];
            $value = isset($data[$field]) ? Input::getVar($data[$field]) : '';
            if ($func == 'pages' && isset($data['maxcharperpage'])) {
                $value = $data['paginationtype'] . '|' . $data['maxcharperpage'];
            }
            //判断对应方法是否存在，不存在跳出本次循环
            if (!method_exists($this, $func)){
                continue;
            }
            //传入参数 字段名 字段值 字段信息
            $form = $this->$func($field, $value, $v);
            if ($form !== false) {
                //作为基本信息
                if ($v['isbase']) {
                    $star = $v['minlength'] || $v['pattern'] ? 1 : 0;
                    $info['base'][$field] = array('name' => $v['name'], 'tips' => $v['tips'], 'form' => $form, 'star' => $star, 'isomnipotent' => $v['isomnipotent'], 'formtype' => $v['formtype']);
                } else {
                    $star = $v['minlength'] || $v['pattern'] ? 1 : 0;
                    $info['senior'][$field] = array('name' => $v['name'], 'tips' => $v['tips'], 'form' => $form, 'star' => $star, 'isomnipotent' => $v['isomnipotent'], 'formtype' => $v['formtype']);
                }
            }
        }

        //配合 validate 插件，生成对应的js验证规则
        $this->formValidateRules = $this->ValidateRulesJson($this->formValidateRules);
        $this->formValidateMessages = $this->ValidateRulesJson($this->formValidateMessages, true);
        return $info;
    }

    /**
     * 转换为validate表单验证相关的json数据
     * @param type $ValidateRules
     */
    public function ValidateRulesJson($ValidateRules, $suang = false) {
        foreach ($ValidateRules as $formname => $value) {
            $va = array();
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    //如果作为消息，消息内容需要加引号，不然会JS报错，是否验证不需要
                    if ($suang) {
                        $va[] = "$k:'$v'";
                    } else {
                        $va[] = "$k:$v";
                    }
                }
            }
            $va = "{" . implode(",", $va) . "}";
            $formValidateRules[] = "'$formname':$va";
        }
        $formValidateRules = "{" . implode(",", $formValidateRules) . "}";
        return $formValidateRules;
    }


}?>