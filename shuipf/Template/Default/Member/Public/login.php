<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>{$Config.sitename}- 会员登录 - 会员管理中心</title>
<link href="{$config_siteurl}statics/css/reset.css" rel="stylesheet" type="text/css" />
<link href="{$config_siteurl}statics/css/table_form.css" rel="stylesheet" type="text/css" />
<template file="Member/Public/Js.php"/>
<style type="text/css">
.submit, .pass-logo a, .form-login .input-lins label, .item span {
	display: inline-block;
	zoom: 1;
*display:inline;
}
.blue, .blue a {
	color: #377abe
}
.log {
	line-height: 24px;
	height: 24px;
	float: right;
	font-size: 12px
}
.log span {
	color: #ced9e7
}
.log a {
	color: #049;
	text-decoration: none;
}
.log a:hover {
	text-decoration: underline;
}
#header {
	height: 94px;
	background: url({$config_siteurl}statics/images/member/h.png) repeat-x
}
#header .logo {
	padding-right: 100px;
	float: left;
	background: url({$config_siteurl}statics/images/member/login-logo.png) no-repeat right 2px;
}
#header .content {
	width: 920px;
	margin: auto;
	height: 60px;
	padding: 10px 0 0 0
}
#content {
	width: 920px;
	margin: auto;
	padding: 36px 0 0 0
}
.form-login {
	width: 440px;
	padding-left: 40px
}
.form-login h2 {
	font-size: 25px;
	color: #494949;
	border-bottom: 1px dashed #CCC;
	padding-bottom: 3px;
	margin-bottom: 10px
}
.form-login .input-lins {
	padding: 7px 0
}
.form-login .input-lins label {
	width: 84px;
	font-size: 14px;
	color: #888;
	text-align: right
}
.take, .reg {
	padding: 0 0 0 84px
}
.take .submit {
	margin-top: 10px
}
.form-login .hr {
	background: url({$config_siteurl}statics/images/member/line.png) no-repeat left center;
	height: 50px;
}
.form-login .hr hr {
	display: none
}
.submit {
	padding-left: 3px
}
.submit, .submit input {
	background: url({$config_siteurl}statics/images/member/but.png) no-repeat;
	height: 29px;
	cursor: hand;
}
.submit input {
	background-position: right top;
	border: none;
	padding: 0 10px 0 7px;
	font-size: 14px
}
.reg {
	color: #666;
	line-height: 24px
}
.reg .submit {
	background-position: left -35px;
	height: 35px
}
.reg .submit input {
	background-position: right -35px;
	font-weight: 700;
	color: #fff;
	height: 35px
}
.col-1 {
	position: relative;
	float: right;
	border: 1px solid #c4d5df;
	zoom: 1;
	background: url({$config_siteurl}statics/images/member/member_title.png) repeat-x;
	width: 310px;
	margin: auto;
	height: 304px
}
.col-1 span.o1,  .col-1 span.o2,  .col-1 span.o3,  .col-1 span.o4 {
	position: absolute;
	width: 3px;
	height: 3px;
	overflow: hidden;
	background: url({$config_siteurl}statics/images/fillet.png) no-repeat
}
.col-1 span.o1 {
	background-position: left -6px;
	top: -1px;
	left: -1px
}
.col-1 span.o2 {
	background-position: right -6px;
	top: -1px;
	right: -1px
}
.col-1 span.o3 {
	background-position: left -9px;
	bottom: -1px;
	left: -1px
}
.col-1 span.o4 {
	background-position: right -9px;
	bottom: -1px;
	right: -1px;
}
.col-1 .title {
	color: #386ea8;
	padding: 5px 10px 3px
}
.col-1 div.content {
	padding: 0px 10px 10px
}
.col-1 div.content h5 {
	background: url({$config_siteurl}statics/images/member/ext-title.png) no-repeat 2px 10px;
	height: 34px
}
.col-1 div.content h5 strong {
	visibility: hidden
}
.pass-logo {
	margin: auto;
	width: 261px;
	padding-top: 15px
}
.pass-logo p {
	border-top: 1px solid #e1e4e8;
	padding-top: 15px
}
.item {
	padding: 10px 0;
	vertical-align: middle;
	margin-bottom: 10px
}
.item span {
	color: #8c8686
}
.login-list li {
	float: left;
	height: 26px;
	margin-bottom: 14px;
	width: 123px;
	background: url({$config_siteurl}statics/images/member/mbg.png) no-repeat
}
.login-list li a {
	display: block;
	background-repeat: no-repeat;
	background-position: 6px 5px;
	height: 26px;
	padding-left: 36px;
	line-height: 26px
}
.login-list li a:hover {
	text-decoration: none;
}
#footer {
	color: #666;
	line-height: 24px;
	width: 920px;
	margin: auto;
	text-align: center;
	padding: 12px 0;
	margin-top: 52px;
	border-top: 1px solid #e5e5e5
}
#footer a {
	color: #666;
}
</style>
</head>
<body>
<div id="header">
  <div class="content">
    <div class="logo"><a href="{$config_siteurl}"><img src="{$config_siteurl}statics/images/logo.gif" height="60"/></a></div>
    <span class="rt log"></span> </div>
</div>
<div id="content">
  <div class="col-left form-login" id="logindiv">
    <form method="post" action="{:U("Member/Index/public_loginverify")}" onsubmit="save_username();" id="myform" name="myform">
      <input type="hidden" name="forward" id="forward" value="{$forward}">
      <h2>会员登录</h2>
      <div class="input-lins">
        <label>用户名：</label>
        <input type="text" id="username" name="username" size="22" class="input">
      </div>
      <div class="input-lins">
        <label>密码：</label>
        <input type="password" id="password" name="password" size="22" class="input">
      </div>
      <if condition=" $Member_config['openverification'] ">
      <div class="input-lins">
        <label>验证码：</label>
        <input type="text" id="code" name="code" size="8" class="input">
        <img id='code_img' onclick='this.src=this.src+"&"+Math.random()' src='{:U("Api/Checkcode/index","type=userlogin&code_len=4&font_size=14&width=80&height=24&font_color=&background=")}'> 
      </div>
      </if>
      <div class="take">
        <input type="checkbox" name="cookietime" value="2592000" id="cookietime">
        记住用户名 <a href="{:U("Member/Index/public_forget_password")}" class="blue">密码找回</a><br />
        <div class="submit">
          <input type="submit" name="dosubmit" id="dosubmit" value="登录">
        </div>
      </div>
      <div class="hr">
        <hr />
      </div>
      <div class="reg">您还没有注册会员？<br />
        <div class="submit">
          <input type="button" name="register" value="立即注册" onclick="redirect('{:U("Member/Index/register")}')">
        </div>
      </div>
    </form>
  </div>
  <div class="col-auto">
    <div class="col-1">
      <div class="content">
        <h5><strong>使用外部通行证</strong></h5>
        <div class="pass-logo">
          <div style="margin-top: 5px;margin-bottom: 10px;">
            <if condition=" $Connectqq "> <a href="	{:U('Api/Connectqq/index')}"><img src="{$config_siteurl}statics/images/member/opensnsqq.png" style="margin-top: 5px;" /></a> </if>
            <if condition=" $Connectsina_weibo "> <a href="{:U('Api/Connectsina/index')}"><img src="{$config_siteurl}statics/images/member/sinaweib.png"style="margin-top: 5px;"  /></a> </if>
          </div>
          <p> <span class="blue">通行证介绍</span> <br />
            支持使用第三方账号登录。 </p>
        </div>
      </div>
      <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span> </div>
  </div>
</div>
<script language="JavaScript">
$(function () {
    $('#username').focus();
})

function save_username() {
    if ($('#cookietime').attr('checked') == true) {
        var username = $('#username').val();
        setCookie('username', username, 3);
    } else {
        delCookie('username');
    }
}
var username = getCookie('username');
if (username != '' && username != null) {
    $('#username').val(username);
    $('#cookietime').attr('checked', true);
}
</script>
<template file="Member/footer.php"/>
<script src="{$config_siteurl}statics/js/common.js"></script>
</body>
</html>