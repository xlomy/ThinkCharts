<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">模块信息</div>
  <form class="J_ajaxForm" action="{:U('Module/install')}" method="post">
  <div class="table_full">
    <table width="100%"  class="table_form">
      <tr>
        <th width="80">模块名称：</th>
        <td >{$modulename}</td>
      </tr>
      <tr>
        <th width="80">模块简介：</th>
        <td >{$introduce}</td>
      </tr>
      <tr>
        <th width="80">作者：</th>
        <td >{$author}</td>
      </tr>
      <tr>
        <th width="80">E-mail：</th>
        <td >{$authoremail}</td>
      </tr>
      <tr>
        <th width="80">作者主页：</th>
        <td >{$authorsite}</td>
      </tr>
    </table>
    </div>
     <div class="">
      <div class="btn_wrap_pd">
        <input type="hidden" name="module" value="{$module}">
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">确定安装</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>