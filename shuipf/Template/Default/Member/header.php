<?php if (!defined('SHUIPF_VERSION')) exit(); ?><div id="header">
  <div class="logo"><a href=""><img src="{$config_siteurl}statics/images/logo.gif" height="60" /></a>
    <h3>会员中心</h3>
  </div>
  <div class="link">你好 ({$User['username']})<span> | </span> <a href="{:U('Member/Index/logout')}">退出</a> <span> | </span><a href="{$config_siteurl}">首页</a> </div>
  <div class="nav-bar">
    <map>
      <ul class="nav-site cu-span">
        <li> <a href="{:U('Member/Index/index')}" <?php if(in_array(ACTION_NAME,array("index","add","mybutors"))): ?>class="on"<?php endif; ?>><span>管理中心</span></a> </li>
        <li class="line">|</li>
        <li> <a href="{:U('Member/Index/account_manage')}" <?php if(in_array(ACTION_NAME,array("account_manage","account_manage_info","account_manage_avatar","account_manage_password"))): ?>class="on"<?php endif; ?>><span>账号管理</span></a> </li>
      </ul>
    </map>
  </div>
</div>