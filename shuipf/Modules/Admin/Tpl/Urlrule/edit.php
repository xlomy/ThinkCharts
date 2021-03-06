<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">温馨提示</div>
  <div class="prompt_text">
    <p>实现伪静态地址，规则需要自己写，同时也要服务器支持！<br/>
      例如实现伪静态这样的：http://www.abc3210.com/tag-标签.html ，URL规则是：tag-{$tag}.html|/tag-{$tag}-{$page}.html，服务器上设置，如果是.htaccess则规则是：RewriteRule ^(tag|Tag)-(.*?)\.html$ index.php?g=Tags&m=Index&a=index&tag=$2</p>
  </div>
  <div class="h_a">规则内容</div>
  <form action="{:U("Urlrule/edit")}" method="post" name="myform" class="J_ajaxForm">
    <div class="table_full">
      <table width="100%" cellpadding="2" cellspacing="1" class="table_form">
        <tr>
          <th width="100">URL规则名称 :</th>
          <td><input type="text" class="input" name="file" id="file" size="20" value="{$data.file}"></td>
        </tr>
        <tr>
          <th>模块名称 :</th>
          <td><select name='module' id='module'>
              <volist name="Module" id="r"> <option value="{$r['module']}" 
                <if condition="$data['module'] eq $r['module'] ">selected</if>
                >{$r['name']}
                </option>
              </volist>
            </select></td>
        </tr>
        <tr>
          <th>是否生成静态？ :</th>
          <td><input type="radio" class="input" value="1" name="ishtml" 
            <if condition="$data['ishtml'] eq '1' "> checked</if>
            />
            是 <input type="radio" class="input" value="0" name="ishtml" 
            <if condition="$data['ishtml'] eq '0' "> checked</if>
            />
            否 </td>
        </tr>
        <tr>
          <th>URL示例 :</th>
          <td><input type="text" class="input" name="example" id="example" size="70" value="{$data.example}"></td>
        </tr>
        <tr>
          <th>URL规则 :</th>
          <td><input type="text" class="input" name="urlrule" id="urlrule" value="{$data.urlrule}" size="70"><br/>如果以“=”开头，表示以自定义处理函数返回路径。函数存放于shuipf\Common\urlrule.php文件中。</td>
        </tr>
        <tr>
          <th>可用变量 :</th>
          <td>父栏目路径：
            <input type="text" class="input" name="f1" value="<literal>{$categorydir}</literal> " size="15" >
            ，栏目目录：
            <input type="text" class="input" name="f1" value="<literal>{$catdir}</literal>" size="10" >
            <div class="bk6"></div>
            年：
            <input type="text" class="input" name="f1" value="<literal>{$year}</literal>" size="7" >
            月：
            <input type="text class="input"" name="f1" value="<literal>{$month}</literal>" size="9" >
            ，日：
            <input type="text" class="input" name="f1" value="<literal>{$day}</literal>" size="7" >
            ID：
            <input type="text" class="input" name="f1" value="<literal>{$id}</literal>" size="4" >
            ， 分页：
            <input type="text" class="input" name="f1" value="<literal>{$page}</literal>" size="7" ></td>
        </tr>
      </table>
    </div>
    <div class="">
      <div class="btn_wrap_pd">
        <input type="hidden" name="urlruleid" id="urlruleid" value="{$data.urlruleid}">
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">提交</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>