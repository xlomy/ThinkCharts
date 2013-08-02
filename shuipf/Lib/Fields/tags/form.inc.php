<?php
/**
 * TAGS
 * @param type $field
 * @param type $value
 * @param type $fieldinfo
 * @return type 
 */
function tags($field, $value, $fieldinfo) {
    extract($fieldinfo);
    if (!$value)
        $value = $defaultvalue;
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:"请输入TAGS！");
    }
    return "<input type='text' name='info[$field]' id='$field' value='$value' style='width:280px' {$formattribute} {$css} class='input' placeholder='请输入TAGS'>";
}

?>