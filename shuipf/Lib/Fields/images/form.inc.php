<?php

function images($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    $setting = unserialize($fieldinfo['setting']);
    extract($setting);
    $list_str = '';
    if ($value) {
        $value = unserialize(html_entity_decode($value, ENT_QUOTES));
        if (is_array($value)) {
            foreach ($value as $_k => $_v) {
                $list_str .= "<div id='image_{$field}_{$_k}' style='padding:1px'><input type='text' name='{$field}_url[]' value='{$_v[url]}' style='width:310px;' ondblclick='image_priview(this.value);' class='input'> <input type='text' name='{$field}_alt[]' value='{$_v[alt]}' style='width:160px;' class='input'> <a href=\"javascript:remove_div('image_{$field}_{$_k}')\">移除</a></div>";
            }
        }
    } else {
        $list_str .= "<center><div class='onShow' id='nameTip'>您最多可以同时上传 <font color='red'>{$upload_number}</font>张</div></center>";
    }
    $string = '<input name="info[' . $field . ']" type="hidden" value="1">
		<fieldset class="blue pad-10">
        <legend>图片列表</legend>';
    $string .= $list_str;
    $string .= '<div id="' . $field . '" class="picList"></div>
		</fieldset>
		<div class="bk10"></div>
		';
    //生成上传附件验证
    $authkey = upload_key("$upload_number,$upload_allowext,$isselectimage");
    $string .= $str . "<a herf='javascript:void(0);' onclick=\"javascript:flashupload('{$field}_images', '图片上传','{$field}',change_images,'{$upload_number},{$upload_allowext},{$isselectimage}','content','$this->catid','{$authkey}')\" class=\"btn\"><span class=\"add\"></span>选择图片 </a>";
    return $string;
}

?>