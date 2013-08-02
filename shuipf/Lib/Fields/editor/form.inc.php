<?php

//编辑器字段
function editor($field, $value, $fieldinfo) {
    $setting = unserialize($fieldinfo['setting']);
    
    extract($setting);
    extract($fieldinfo);

    $disabled_page = isset($disabled_page) ? $disabled_page : 0;
    //编辑器高度
    if (!$height)
        $height = 300;
    //是否允许上传
    if(defined('IN_ADMIN') && IN_ADMIN){
        $allowupload = 1;
    }else{
        $Member_group = F("Member_group");
        //获取当前登陆会员组id
        $groupid = SiteCookie('groupid');
        $allowupload = $Member_group[$groupid]['allowattachment']?1:0;
        $toolbar = $mbtoolbar?$mbtoolbar:"basic";
    }
    
    //内容
    if (!$value)
        $value = $defaultvalue;
    if ($minlength || $pattern)
        $allow_empty = '';
    $module = ( in_array(GROUP_NAME,array("Contents", "contents")) )?'Contents':GROUP_NAME;
    $form = Form::editor($field, $toolbar, $module, $this->catid, $allowupload, $allowupload, '',10, $height, $disabled_page);
    //javascript
    $this->formJavascript .= "
            //编辑器
            //editor$field = new baidu.editor.ui.Editor(editor_config_$field);
            //editor$field.render( '$field' );
            //增加编辑器验证规则
            jQuery.validator.addMethod('editor$field',function(){
                try{editor$field.sync();}catch(err){};
                return ".($minlength?"editor$field.hasContents();":"true")."
            });
    ";
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    //20130428 由于没有设置必须输入时，ajax提交会造成获取不到编辑器的值。所以这里强制进行验证，使其触发编辑器的sync()方法
   // if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("editor$field"=>"true");
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("editor$field"=>$errortips?$errortips:$name."不能为空！");
   // }
    return "<div id='{$field}_tip'></div>" . '<textarea id="' . $field . '" name="info[' . $field . ']">' . $value . '</textarea>' . $form;
}

?>