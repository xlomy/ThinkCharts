<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <form name="myform" class="J_ajaxForm" action="{:U('Info/delete')}" method="post" >
  <div class="table_list">
  <table width="100%" border="0" cellpadding="5" cellspacing="1" class="tableClass">
      <thead>
        <tr>
          <td width="35" align="center"><input type="checkbox" class="J_check_all" data-direction="y" data-checklist="J_check_y"></td>
          <td align="left">用户名</td>
          <td width='250' align="center">用户ip</td>
          <td width='250' align="center">时间</td>
          <td width='250' align="center">操作</td>
        </tr>
      </thead>
      <tbody>
        <volist name="data" id="vo">
        <tr>
          <td align="center"><input type="checkbox" class="J_check" data-yid="J_check_y" data-xid="J_check_x" name="did[]" value="{$vo.dataid}"></td>
          <td>{$vo.username}</td>
          <td align="center"><font color=blue>{$vo.ip}</font></td>
          <td align="center">{$vo.datetime|date="Y-m-d H:i:s",###}</td>
          <td align="center"><a href="javascript:check('{$formid}', '{$vo.dataid}', '{$vo.username}');">查看</a> | <a href="javascript:confirmurl('{:U('Info/delete',array('formid'=>$formid,'did'=>$vo['dataid']))}','确认要删除 『 删除 』 吗？');">删除</a></td>
        </tr>
       </volist>
      </tbody>
    </table>
    <div class="p10">
        <div class="pages"> {$Page} </div>
      </div>
  </div>
  <div class="">
      <div class="btn_wrap_pd">             
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">删除</button>
        <input type="hidden"name="formid" value="{$formid}">
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
<script src="{$config_siteurl}statics/js/content_addtop.js"></script>
<script type="text/javascript">
//详细信息查看
function check(id, did, title) {
	omnipotent("check", GV.DIMAUB+'index.php?a=public_view&m=Info&g=Formguide&formid=' + id +'&dataid='+did, '查看 ' + title+'---提交的信息', 1, '700px', '500px');
}
</script>
</body>
</html>