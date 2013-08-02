<?php if (!defined('SHUIPF_VERSION')) exit(); ?><div class="col-left col-1 left-memu">
    <h5 class="title">账号管理</h5>
    <ul>
      <li <if condition=" ACTION_NAME eq 'account_manage_info' ">class="on"</if>><a href="{:U('Member/Index/account_manage_info')}"><img src="{$config_siteurl}statics/images/icon/user_edit.png" width="16" /> 修改个人信息</a></li>
      <li <if condition=" ACTION_NAME eq 'account_manage_avatar' ">class="on"</if>><a href="{:U('Member/Index/account_manage_avatar')}"><img src="{$config_siteurl}statics/images/icon/vcard.png" width="16" /> 修改头像</a></li>
      <li <if condition=" ACTION_NAME eq 'account_manage_password' ">class="on"</if>><a href="{:U('Member/Index/account_manage_password')}"><img src="{$config_siteurl}statics/images/icon/icon_key.gif" width="16" height="16" /> 修改邮箱/密码</a></li>
    </ul>
    <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
</div>