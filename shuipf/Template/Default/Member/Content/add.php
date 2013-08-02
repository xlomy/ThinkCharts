<?php if (!defined('SHUIPF_VERSION')) exit(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>{$Config.sitename} - 会员管理中心</title>
<link href="{$config_siteurl}statics/css/reset.css" rel="stylesheet" type="text/css" />
<link href="{$config_siteurl}statics/css/member.css" rel="stylesheet" type="text/css" />
<link href="{$config_siteurl}statics/css/table_form.css" rel="stylesheet" type="text/css" />
<template file="Member/Public/Js.php"/>
<script type="text/javascript">
    var catid = "{$catid}";
</script>
</head>
<body>
<template file="Member/header.php"/>
<div id="memberArea">
  <template file="Member/Index/navigate.php"/>
  <div class="col-auto">
		<div class="col-1 ">
			<h5 class="title">在线投稿</h5>
			<div class="content">
			<if condition=" $catid == false ">
			<select  id="catid" size="20" onchange="javascript:c_c(this.value);">
			{$Category}
			</select>
			<else />
			<form method="post" action="{:U('Content/add')}" id="myform" name="myform" class="J_ajaxForms" enctype="multipart/form-data">
				<table width="100%" cellspacing="0" class="table_form">
					<tr>
						<th>栏目：</th>
						<td><select  id="catid" name="info[catid]" onchange="javascript:c_c(this.value);">{$Category}</select></td>
					</tr>
					<?php
					 if(is_array($forminfos)) {
					     foreach($forminfos as $field=>$info) {
						     if($info['isomnipotent']) continue;
							 if($info['formtype']=='omnipotent') {
							     foreach($forminfos as $_fm=>$_fm_value) {
								     if($_fm_value['isomnipotent']) {
									     $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
									 }
								 }
							 }
					?>
					<tr>
						<th width="100"><if condition=" $info['star'] "><font color="red">*</font></if> {$info['name']}：</th> 
						<td>{$info['form']}<if condition=" $info['tips'] ">{$info['tips']}</if></td>
					</tr>
					<?php
					     }
					}
					?>
					<tr>
						<th></th>
						<td>
						<!--提交成功返回地址-->
						<input name="forward" type="hidden" value="{$forward}">
						<input name="dosubmit" type="submit" id="dosubmit" value="提交" class="button"></td>
					</tr>
				</table>
			</form>
			</if>
			</div>
			<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
		</div>
	</div>
  <div class="clear"></div>
</div>
<script type="text/javascript"> 
function c_c(catid) {
	location.href='index.php?a=add&m=Content&g=Member&catid='+catid;
}
</script>
<template file="Member/footer.php"/>
<script src="{$config_siteurl}statics/js/common.js"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/content_addtop.js"></script>
<script type="text/javascript"> 
$(function () {
	$(".J_ajax_close_btn").on('click', function (e) {
	    e.preventDefault();
	    Wind.use("artDialog", function () {
	        art.dialog({
	            id: "question",
	            icon: "question",
	            fixed: true,
	            lock: true,
	            background: "#CCCCCC",
	            opacity: 0,
	            content: "您确定需要关闭当前页面嘛？",
	            ok:function(){
					window.close();
					return true;
				}
	        });
	    });
	});
    Wind.use('validate', 'ajaxForm', 'artDialog', function () {
		//javascript
        {$formJavascript}
        var form = $('form.J_ajaxForms');
        //ie处理placeholder提交问题
        if ($.browser.msie) {
            form.find('[placeholder]').each(function () {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            });
        }
        //表单验证开始
        form.validate({
			//是否在获取焦点时验证
			onfocusout:false,
			//是否在敲击键盘时验证
			onkeyup:false,
			//当鼠标掉级时验证
			onclick: false,
            //验证错误
            showErrors: function (errorMap, errorArr) {
				//errorMap {'name':'错误信息'}
				//errorArr [{'message':'错误信息',element:({})}]
				try{
					$(errorArr[0].element).focus();
					art.dialog({
						id:'error',
						icon: 'error',
						lock: true,
						fixed: true,
						background:"#CCCCCC",
						opacity:0,
						content: errorArr[0].message,
						cancelVal: '确定',
						cancel: function(){
							$(errorArr[0].element).focus();
						}
					});
				}catch(err){
				}
            },
            //验证规则
            rules: {$formValidateRules},
            //验证未通过提示消息
            messages: {$formValidateMessages},
            //给未通过验证的元素加效果,闪烁等
            highlight: false,
            //是否在获取焦点时验证
            onfocusout: false,
            //验证通过，提交表单
            submitHandler: function (forms) {
                forms.submit();
                return true;
            }
        });
    });
});
</script>
</body>
</html>