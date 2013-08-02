<?php

//多行文本框
function textarea($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);
    extract($setting);
    if (!$value)
        $value = $defaultvalue;
    $allow_empty = 'empty:true,';
    if ($minlength || $pattern)
        $allow_empty = '';
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    //如果内容为空，着使用默认值
    $value = empty($value) ? $setting['defaultvalue'] : $value;
    $str = "<textarea name='info[{$field}]' id='$field' style='width:{$width}%;height:{$height}px;' $formattribute $css";
    if ($maxlength)
        $str .= " onkeyup=\"strlen_verify(this, '{$field}_len', {$maxlength})\"";
    $str .= ">{$value}</textarea>";
    if ($maxlength)
        $str .= '还可以输入<B><span id="' . $field . '_len">' . $maxlength . '</span></B>个字符！ ';

    return $str;
}

?>