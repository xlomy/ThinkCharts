<?php
//更新时间 
function datetime($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    extract(unserialize($fieldinfo['setting']));
    $isdatetime = 0;
    $timesystem = 0;
    //时间格式
    if ($fieldtype == 'int') {//整数 显示格式
        if (!$value && $defaulttype)
            $value = time();
        //整数 显示格式
        $format_txt = $format == 'm-d' ? 'm-d' : $format;
        if ($format == 'Y-m-d Ah:i:s')
            $format_txt = 'Y-m-d h:i:s';
        $value = date($format_txt, $value);

        $isdatetime = strlen($format) > 6 ? 1 : 0;
        if ($format == 'Y-m-d Ah:i:s') {

            $timesystem = 0;
        } else {
            $timesystem = 1;
        }
    } elseif ($fieldtype == 'datetime') {
        $isdatetime = 1;
        $timesystem = 1;
    } elseif ($fieldtype == 'datetime_a') {
        $isdatetime = 1;
        $timesystem = 0;
    }
    return Form::date("info[$field]",$value,$isdatetime,1,'true',$timesystem);
}

?>