<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>{$Config.sitename} - 会员管理中心</title>
<link href="{$config_siteurl}statics/css/reset.css" rel="stylesheet" type="text/css" />
<link href="{$config_siteurl}statics/css/member.css" rel="stylesheet" type="text/css" />
<link href="{$config_siteurl}statics/css/table_form.css" rel="stylesheet" type="text/css" />
<template file="Member/Public/Js.php"/>
</head>
<body>
<template file="Member/header.php"/>
<div id="memberArea">
  <template file="Member/Index/navigate.php"/>
  <div class="col-auto">
    <div class="col-auto">
      <div class="col-1 member-info">
        <div class="content">
          <div class="col-left himg"> <a title="修改头像" href="{:U('Member/Index/account_manage_avatar')}"><img src="{$config_siteurl}api.php?m=Avatar&uid={$User.userid}" width="60" height="60" onerror="this.src='{$config_siteurl}statics/images/member/nophoto.gif'"></a> </div>
          <div class="col-auto">
            <h5>
              <if condition=" $Member_group[$User['groupid']]['icon'] "><img src="{$Member_group[$User['groupid']]['icon']}"  /></if>
              <font color="{$Member_group[$User['groupid']]['usernamecolor']}"> {$User['username']} </font> （{$User.email}） </h5>
            <p class="blue"> 会员组：{$Member_group[$User['groupid']]['name']}，
              账户余额：<font style="color:#F00; font-size:22px;font-family:Georgia,Arial; font-weight:700">{$User.amount}</font> 元，
              积分点数：<font style="color:#F00; font-size:12px;font-family:Georgia,Arial; font-weight:700">{$User.point}</font> 点 </p>
          </div>
        </div>
        <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span> </div>
      <div class="bk10"></div>
      <div class="col-1 " style="display:none">
        <h5 class="title">收藏夹</h5>
        <div class="content">
          <ul class="title-list">
          </ul>
        </div>
        <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span> </div>
    </div>
  </div>
  <div class="clear"></div>
</div>
<script language="JavaScript">
<!--
$(document).ready(function() {
	var announcement = getCookie('announcement_2_');
	if(announcement==null || announcement=='') {
		$("#announcement").fadeIn("slow");
	}
});
//-->
</script>
<template file="Member/footer.php"/>
</body>
</html>