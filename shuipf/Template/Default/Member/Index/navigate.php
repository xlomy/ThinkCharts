<?php if (!defined('SHUIPF_VERSION')) exit(); ?><div class="col-left col-1 left-memu">
    <h5 class="title"><img src="{$config_siteurl}statics/images/icon/m_1.png" width="15" height="15" /> 管理中心</h5>
    <ul>
      <li <if condition=" ACTION_NAME eq 'add' ">class="on"</if>><a href="{:U('Member/Content/add')}"><img src="{$config_siteurl}statics/images/icon/m_2.png" width="14" height="15" /> 在线投稿</a></li>
      <li <if condition=" ACTION_NAME eq 'mybutors' ">class="on"</if>><a href="{:U('Member/Content/mybutors')}"><img src="{$config_siteurl}statics/images/icon/m_3.png" width="14" height="16" /> 已发布稿件</a></li>
    </ul>
    <h6 class="title" style="display:none">短消息</h6>
    <ul style="display:none">
      <li <if condition=" ACTION_NAME eq 'send' ">class="on"</if>><a href="{:U('Member/Message/send')}"><img src="{$config_siteurl}statics/images/icon/m_9.png" width="16" height="14" /> 发送短消息</a></li>
      <li <if condition=" ACTION_NAME eq 'inbox' ">class="on"</if>><a href="{:U('Member/Message/inbox')}"><img src="{$config_siteurl}statics/images/icon/m_11.png" width="16" height="16" /> 收件箱</a></li>
      <li <if condition=" ACTION_NAME eq 'outbox' ">class="on"</if>><a href="{:U('Member/Message/outbox')}"><img src="{$config_siteurl}statics/images/icon/m_10.png" width="16" height="16" /> 发件箱</a></li>
      <li <if condition=" ACTION_NAME eq 'group' ">class="on"</if>><a href="{:U('Member/Message/group')}"><img src="{$config_siteurl}statics/images/icon/lightbulb.png" width="16" height="16" /> 系统消息</a></li>
    </ul>
    <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
 </div>