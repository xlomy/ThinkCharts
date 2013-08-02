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
</head>
<body>
<template file="Member/header.php"/>
<div id="memberArea">
  <template file="Member/Index/navigate_account.php"/>
  <div class="col-auto">
    <div class="point" id='announcement'> <a href="javascript:hide_element('announcement');" hidefocus="true" class="close"><span>关闭</span></a>
      <div class="content"> <strong class="title">注意：</strong>
        <p>带*号的字段为必填资料</p>
      </div>
    </div>
    <div class="col-1 ">
      <h5 class="title">修改个人信息</h5>
      <div class="content">
        <form method="post" action="" id="myform" name="myform">
          <table width="100%" cellspacing="0" class="table_form">
            <tr>
              <th width="100">昵称</th>
              <td><input id="nickname" name="nickname" value="<?php echo $User['nickname']?$User['nickname']:$User['username']; ?>" type="text" class="input" size="30"></td>
            </tr>
            <?php foreach($forminfos['base'] as $k=>$v) {?>
            <tr>
              <th width="100"> <?php echo $v['name']?>：</th>
              <td><?php echo $v['form']?></td>
            </tr>
            <?php }?>
            <tr>
              <th></th>
              <td><input name="dosubmit" type="submit" id="dosubmit" value="提交" class="button"></td>
            </tr>
          </table>
        </form>
      </div>
      <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span> </div>
  </div>
</div>
<template file="Member/footer.php"/>
<template file="Member/Public/Js.php"/>
<script src="{$config_siteurl}statics/js/common.js"></script>
</body>
</html>