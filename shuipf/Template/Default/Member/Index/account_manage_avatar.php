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
</head>
<body>
<template file="Member/header.php"/>
<div id="memberArea">
  <template file="Member/Index/navigate_account.php"/>
  <div class="col-auto">
    <div class="col-1 ">
      <h5 class="title">修改头像</h5>
      <div class="content"> 
	  <script language="javascript" type="text/javascript" src="{$config_siteurl}statics/js/swfobject.js"></script>
        {$avatarhtml}
        <ul class="col-right col-avatar" id="avatarlist">
          <li> <img src="{$config_siteurl}api.php?m=Avatar&uid={$User.userid}" height="180" width="180" onerror="{$config_siteurl}statics/images/member/nophoto.gif'"> </li>
        </ul>
		<div class="col-auto">
			<div id="myContent"> 
			</div>
		</div>
      </div>
      <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span> </div>
  </div>
</div>
<template file="Member/footer.php"/>
</body>
</html>