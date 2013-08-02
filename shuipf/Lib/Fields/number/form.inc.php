<?php

//数字
function number($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);
    $size = $setting['size'];
    if (!$value)
        $value = $defaultvalue;
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    return "<input type='text' name='info[$field]' id='$field' value='$value' class='input' size='$size' {$formattribute} {$css}>";
}

?>