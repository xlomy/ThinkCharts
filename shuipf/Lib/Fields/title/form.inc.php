<?php

//标题字段
function title($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $style_arr = explode(';', $this->data['style']);
    $style_color = $style_arr[0];
    $style_font_weight = $style_arr[1] ? $style_arr[1] : '';

    $style = 'color:' . $this->data['style'];
    if (!$value)
        $value = $defaultvalue;
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:"标题不能为空！");
    }
    $str = '<input type="text" style="width:400px;' . ($style_color ? 'color:' . $style_color . ';' : '') . ($style_font_weight ? 'font-weight:' . $style_font_weight . ';' : '') . '" name="info[' . $field . ']" id="' . $field . '" value="' . $value . '" style="' . $style . '" class="input input_hd J_title_color" placeholder="请输入标题" onkeyup="strlen_verify(this, \''.$field.'_len\', '.$maxlength.')" />
                <input type="hidden" name="style_font_weight" id="style_font_weight" value="' . $style_font_weight . '">';
    if (defined('IN_ADMIN') && IN_ADMIN)
        $str .= '<input type="button" class="btn" id="check_title_alt" value="标题检测" onclick="$.get(\''.CONFIG_SITEURL_MODEL.'index.php?a=public_check_title&m=Content&g=Contents&catid=' . $this->catid . '&sid=\'+Math.random()*5, {data:$(\'#title\').val()}, function(data){if(data.status==false) {$(\'#check_title_alt\').val(\'标题重复\');$(\'#check_title_alt\').css(\'background-color\',\'#FFCC66\');} else if(data.status==true) {$(\'#check_title_alt\').val(\'标题不重复\');$(\'#check_title_alt\').css(\'background-color\',\'#F8FFE1\')}},\'json\')" style="width:73px;"/>
                    <span class="color_pick J_color_pick"><em style="background:' . $style_color . ';" class="J_bg"></em></span><input type="hidden" name="style_color" id="style_color" class="J_hidden_color" value="' . $style_color . '">
                    <img src="' . CONFIG_SITEURL_MODEL . 'statics/images/icon/bold.png" width="10" height="10" onclick="input_font_bold()" style="cursor:hand"/>';
    $str .= ' 还可输入<B><span id="title_len">' . $maxlength . '</span></B> 个字符';
    return $str;
}

?>