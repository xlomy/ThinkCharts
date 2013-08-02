<?php

//缩略图
function image($field, $value, $fieldinfo) {
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
    $html = '';
    //图片裁减功能只在后台使用
    if (defined('IN_ADMIN') && IN_ADMIN) {
        $html = "<input type=\"button\" class=\"btn\" onclick=\"crop_cut_" . $field . "($('#$field').val());return false;\" value=\"裁减图片\"> 
            <input type=\"button\"  class=\"btn\" onclick=\"$('#" . $field . "_preview').attr('src','" . CONFIG_SITEURL_MODEL . "statics/images/icon/upload-pic.png');$('#" . $field . "').val('');return false;\" value=\"取消图片\"><script type=\"text/javascript\">
            function crop_cut_" . $field . "(id){
	if ( id =='' || id == undefined ) { 
                      isalert('请先上传缩略图！');
                      return false;
                    }
                    var catid = $('input[name=\"info[catid]\"]').val();
                    if(catid == '' ){
                        isalert('请选择栏目ID！');
                        return false;
                    }
                    Wind.use('artDialog','iframeTools',function(){
                      art.dialog.open(GV.DIMAUB+'index.php?a=public_imagescrop&m=Content&g=Contents&catid='+catid+'&picurl='+encodeURIComponent(id)+'&input=$field&preview=" . ($show_type && defined('IN_ADMIN') ? $field . "_preview" : '') . "', {
                        title:'裁减图片', 
                        id:'crop',
                        ok: function () {
                            var iframe = this.iframe.contentWindow;
                            if (!iframe.document.body) {
                                 alert('iframe还没加载完毕呢');
                                 return false;
                            }
                            iframe.uploadfile();
                            return false;
                        },
                        cancel: true
                      });
                    });
            };
</script>";
    }
    //生成上传附件验证
    $authkey = upload_key("1,$upload_allowext,$isselectimage,$images_width,$images_height,$watermark");
    if ($show_type && defined('IN_ADMIN') && IN_ADMIN) {
        $preview_img = $value ? $value : CONFIG_SITEURL_MODEL . 'statics/images/icon/upload-pic.png';
        return $str . "<div  style=\"text-align: center;\"><input type='hidden' name='info[$field]' id='$field' value='$value'>
			<a href='javascript:void(0);' onclick=\"flashupload('{$field}_images', '附件上传','{$field}',thumb_images,'1,{$upload_allowext},$isselectimage,$images_width,$images_height,$watermark','content','$this->catid','$authkey');return false;\">
			<img src='$preview_img' id='{$field}_preview' width='135' height='113' style='cursor:hand' /></a>" . $html . "</div>";
    } else {
        return $str . "<input type='text' name='info[$field]' id='$field' value='$value' size='$size' class='input' />  <input type='button' class='button' onclick=\"flashupload('{$field}_images', '附件上传','{$field}',submit_images,'1,{$upload_allowext},$isselectimage,$images_width,$images_height,$watermark','content','$this->catid','$authkey')\"/ value='上传图片'>" . $html;
    }
}

?>