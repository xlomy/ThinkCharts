<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <div class="h_a">详细信息</div>
  <div class="table_full">
    <table width="100%">
      <tr>
        <td width="15%" align="center">名称</td>
        <td align="left">内容</td>
      </tr>
      <?php
if(is_array($forminfos)){
	foreach($forminfos as $key => $form){
?>  
	<tr>
		<th width="15%" align="right"><?php echo $fields[$key]['name']?>:</th>
		<th align="left"><?php echo $form?></th>
	</tr>
<?php 
	}
}
?>
    </table>
  </div>
</div>
</body>
</html>