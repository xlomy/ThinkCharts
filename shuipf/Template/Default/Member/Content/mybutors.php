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
  <template file="Member/Index/navigate.php"/>
  <div class="col-auto">
		<div class="col-1 ">
      <h5 class="title">已发布稿件</h5>
      <div class="content">
        <table width="100%" cellspacing="0"  class="table-list">
          <thead>
            <tr>
              <th width="30">ID</th>
              <th>标题</th>
              <th width="80">栏目</th>
              <th width="80">添加时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
		  <volist name="data" id="r">
            <tr>
              <td align="center">{$r.id}</td>
              <td align="left"><if condition=" $r['status'] eq 1 "> {$r.title}<else /><a href="{$r.url}" target="_blank" title="{$r.title}">{$r.title}</a></if><if condition=" $r['status'] eq 1 "><font color="#1D94C7"> [待审核]</font><else /><font color="#1D94C7"> [通过]</font></if></td>
              <td align="center"><a href="{$Category[$r['catid']]['url']}" target="_blank">{$Category[$r['catid']]['catname']}</a></td>
              <td align="center">{$r.updatetime|date="Y-m-d",###}</td>
              <td align="center">
			  <?php
			    if($r['_setting']['member_admin'] > 0){
			  ?>
			  <switch name="r['_setting']['member_admin']" >
			  <case value="1">
			    <if condition=" $r['status'] eq 1 "><a href="{:U('Content/edit', array('id'=>$r['id'],'catid'=>$r['catid'])  )}" >修改</a> | <a href="javascript:confirmurl('{:U('Content/delete', array('id'=>$r['id'],'catid'=>$r['catid'])  )}','确认要删除吗？')" >删除</a></if>
			  </case>
			  <case value="2">
			     <if condition=" $r['status'] eq 1 "><a href="{:U('Content/edit', array('id'=>$r['id'],'catid'=>$r['catid'])  )}">修改</a></if>
			  </case>
			  <case value="3">
			     <if condition=" $r['status'] eq 1 "><a href="javascript:confirmurl('{:U('Content/delete', array('id'=>$r['id'],'catid'=>$r['catid'])  )}','确认要删除吗？')" >删除</a></if>
			  </case>
			  <case value="4">
			     <a href="{:U('Content/edit', array('id'=>$r['id'],'catid'=>$r['catid'])  )}">修改</a> | <a href="javascript:confirmurl('{:U('Content/delete', array('id'=>$r['id'],'catid'=>$r['catid'])  )}','确认要删除吗？')">删除</a>
			  </case>
			  <case value="5">
			     <a href="{:U('Content/edit', array('id'=>$r['id'],'catid'=>$r['catid'])  )}">修改</a>
			  </case>
			  <case value="6">
			     <a href="javascript:confirmurl('{:U('Content/delete', array('id'=>$r['id'],'catid'=>$r['catid'])  )}','确认要删除吗？')">删除</a>
			  </case>
			  </switch>
			  <?php
			     }else{
			  ?>
			  没有权限操作
			  <?php
			     }
			  ?>
			  
			  </td>
            </tr>
		  </volist>
          </tbody>
        </table>
        <div id="pages">{$Page}</div>
      </div>
      <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span> </div>
	</div>
  <div class="clear"></div>
</div>
<script type="text/javascript"> 
function confirmurl(url,message) {
	if(confirm(message)) redirect(url);
}
//-->
</script>
<template file="Member/footer.php"/>
</body>
</html>