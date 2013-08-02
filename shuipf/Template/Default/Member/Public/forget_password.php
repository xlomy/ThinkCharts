<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>{$Config.sitename} - 密码找回 - 会员管理中心</title>
<link href="{$config_siteurl}statics/css/reset.css" rel="stylesheet" type="text/css" />
<link href="{$config_siteurl}statics/css/table_form.css" rel="stylesheet" type="text/css" />
<link href="{$config_siteurl}statics/css/table_form.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.submit, .pass-logo a, .form-login .input-lins label, .item span, #content h2 span em {
	display: inline-block;
	display: -moz-inline-stack;
	zoom: 1;
*display:inline;
}
.blue, .blue a {
	color: #377abe
}
, .submit input {
cursor:hand;
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
#content {
	width: 920px;
	margin: auto;
	padding: 20px 0 0 0;
	overflow: auto
}
.form-login {
	width: 420px;
	padding-left: 40px
}
#content h2 {
	font-size: 25px;
	color: #494949;
	border-bottom: 1px dashed #CCC;
	padding-bottom: 3px;
	margin-bottom: 10px
}
#content h2 span {
	font-size: 12px;
	font-weight: normal
}
#content h2 span em {
	background: url({$config_siteurl}statics/images/member/order.png) no-repeat 0px -16px;
	width: 15px;
	height: 15px;
	line-height: 15px;
	text-align: center;
	margin-right: 5px;
	color: #FFF
}
#content h2 span.on {
	color: #333;
	font-weight: 700
}
#content h2 span.on em {
	background-position: 0px 0px;
}
.form-login .input-lins {
	padding: 7px 0;
	overflow: hidden;
	clear: both
}
.form-login .input-lins label {
	width: 84px;
	font-size: 14px;
	color: #8c8686;
	text-align: right;
	float: left
}
.form-login .input-lins .form {
	width: 560px;
	float: left
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
.form-reg {
	padding: 10px 0 0 14px;
	width: 700px;
	border-right: 1px solid #ccc
}
.form-reg .input-lins label {
	width: 120px
}
.form-reg .input-lins label.type {
	width: auto;
	color: #000;
	padding-right: 10px
}
.form-reg .reg {
	padding: 10px 0 0 120px
}
.form-reg .reg .submit {
	margin-bottom: 5px
}
.submit {
	padding-left: 3px
}
.submit, .submit input {
	background: url({$config_siteurl}statics/images/member/but.png) no-repeat;
	height: 29px;
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
.reg-auto {
	padding: 10px 0 0 20px
}
.reg-auto p {
	margin-bottom: 10px;
	color: #666;
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
	background: url(images/fillet.png) no-repeat
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
	background-position: left bottom;
	bottom: -1px;
	left: -1px
}
.col-1 span.o4 {
	background-position: right bottom;
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
.pass-logo a img {
	border: 1px solid #ddd
}
.pass-logo a {
	border: 3px solid #fff
}
.pass-logo a.logo, .pass-logo a:hover {
	border: 3px solid #e8f1f1;
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
.point {
	border: 1px solid #ffbf7a;
	background: #fffced;
	margin-bottom: 10px;
	margin-right: 100px;
	margin-left: 50px;
	position: relative
}
.point .content {
	padding: 8px 10px;
}
.point .content .title {
	color: #ff8400
}
.point .content p {
	color: #777;
	text-indent: 20px
}
.point span.o1,  .point span.o2,  .point span.o3,  .point span.o4 {
	position: absolute;
	width: 3px;
	height: 3px;
	background: url({$config_siteurl}statics/images/fillet.png) no-repeat;
	overflow: hidden
}
.point span.o1 {
	background-position: left top;
	top: -1px;
	left: -1px
}
.point span.o2 {
	background-position: right top;
	top: -1px;
	right: -1px
}
.point span.o3 {
	background-position: left -3px;
	bottom: -1px;
	left: -1px
}
.point span.o4 {
	background-position: right -3px;
	bottom: -1px;
	right: -1px;
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
  <h2>密码找回</h2>
  <form method="post" action="" id="myform">
    <div class="col-left form-login form-reg">
      <div class="point">
        <div class="content"> <strong class="title">注意：</strong>
          <p>提交后请在一小时内登录邮箱重置会员密码。</p>
        </div>
      </div>
      <div class="input-lins">
        <label>请输入邮箱：</label>
        <input type="text" id="email" name="email" size="36" class="input">
      </div>
      <div class="input-lins">
        <label>验证码：</label>
        <input type="text" id="code" name="code" size="10" class="input">
        <img id='code_img' onclick='this.src=this.src+"&"+Math.random()' src='{:U("Api/Checkcode/index","code_len=4&font_size=14&width=80&height=24&font_color=&background=")}'></div>
      <div class="reg">
        <div class="submit">
          <input type="submit" name="dosubmit" value="提交" style="width:60px">
        </div>
      </div>
    </div>
  </form>
  <div class="col-auto reg-auto">
    <p class="f14"> 已经有了账号？<br />
      请直接登录 </p>
    <div class="submit">
      <input type="submit" name="dosubmit" value="登录" onclick="redirect('{:U("Member/Index/login")}')">
    </div>
  </div>
</div>
<template file="Member/footer.php"/>
<template file="Member/Public/Js.php"/>
<script src="{$config_siteurl}statics/js/common.js"></script>
</body>
</html>