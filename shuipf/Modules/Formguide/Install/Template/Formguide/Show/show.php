<?php if (!defined('SHUIPF_VERSION')) exit(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>{$name}- 反馈表单</title>
<link href="{$config_siteurl}statics/css/admin_style.css" rel="stylesheet" type="text/css" />
<link href="{$config_siteurl}statics/css/member.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
//全局变量，是Global Variables不是Gay Video喔
var GV = {
    DIMAUB: "{$config_siteurl}",
    JS_ROOT: "statics/js/"
};
</script>
<script src="{$config_siteurl}statics/js/wind.js"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/jquery.js"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/common.js"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/content_addtop.js"></script>
</head>
<body>
<div id="header">
</div>
<div id="memberArea">
  <div class="col-auto">
      <div class="col-1 ">
          <h5 class="title">{$name}</h5>
          <div class="content" style="padding: 5px 0px 3px;overflow: hidden;">
              <form method="post" action="{:U('Index/post')}" id="myform" name="myform">
                  <input type="hidden" name="formid" value="{$formid}"/>
                  <table width="925" cellspacing="0" class="table_form">
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
						<td width="800">{$info['form']}<if condition=" $info['tips'] ">{$info['tips']}</if></td>
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
			</div>
			<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
		</div>
	</div>
  <div class="clear"></div>
</div>
</body>
</html>