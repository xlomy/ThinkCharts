<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <form name="myform" class="J_ajaxForm" action="{:U('Tags/Tags/delete')}" method="post">
  <div class="table_list">
  <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td width="50"  align="center"><input type="checkbox" class="J_check_all" data-direction="x" data-checklist="J_check_x" onclick="selectall('tagid[]');">全选</td>
            <td width="50">排序</td>
            <td align="center">关键字</td>
            <td align="center">信息总数</td>
            <td align="center">最后使用时间</td>
            <td align="center">点击次数</td>
            <td align="center">最近访问时间</td>
            <td align="center">相关操作</td>
          </tr>
        </thead>
        <tbody>
        <volist name="data" id="vo">
          <tr>
            <td width="50" align="center"><input type="checkbox" value="{$vo.tagid}" class="J_check" data-yid="J_check_y" data-xid="J_check_x" name="tagid[]"></td>
            <td><input type="text" name="listorder[{$vo.tagid}]" class="input" value="{$vo.listorder}" size="5" /></td>
            <td align="center">{$vo.tag}</td>
            <td align="center">{$vo.usetimes}</td>
            <td align="center">{$vo.lastusetime|date="Y-m-d H:i:s",###}</td>
            <td align="center">{$vo.hits }</td>
            <td align="center">{$vo.lasthittime|date="Y-m-d H:i:s",###}</td>
            <td align="center"><a href="{:U('Tags/Tags/edit' , array('tagid'=>$vo['tagid']) )}">修改</a> | <a href="javascript:confirmurl('{:U('Tags/Tags/delete' , array('tagid'=>$vo['tagid']) )}', '确认要删除 『 {$vo.tag} 』 吗?')">删除</a></td>
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
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit" data-action="{:U('Tags/listorder')}">排序</button>
        <button class="btn  mr10 J_ajax_submit_btn" type="submit">删除</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>