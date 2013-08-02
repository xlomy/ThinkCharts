<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<style>
.pop_nav{
	padding: 0px;
}
.pop_nav ul{
	border-bottom:1px solid #266AAE;
	padding:0 5px;
	height:25px;
	clear:both;
}
.pop_nav ul li.current a{
	border:1px solid #266AAE;
	border-bottom:0 none;
	color:#333;
	font-weight:700;
	background:#F3F3F3;
	position:relative;
	border-radius:2px;
	margin-bottom:-1px;
}

</style>
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="pop_nav">
    <ul class="J_tabs_nav">
      <li class="current"><a href="javascript:;;">基本属性</a></li>
      <li class=""><a href="javascript:;;">选项设置</a></li>
      <li class=""><a href="javascript:;;">模板设置</a></li>
      <li class=""><a href="javascript:;;">生成设置</a></li>
      <li class=""><a href="javascript:;;">权限设置</a></li>
    </ul>
  </div>
  <form class="J_ajaxForms" name="myform" id="myform" action="{:U("Category/add")}" method="post">
    <div class="J_tabs_contents">
      <div>
        <div class="h_a">基本属性</div>
        <div class="table_full">
          <table width="100%" class="table_form ">
            <tr>
              <th width="200">请选择模型：</th>
              <td><select name="info[modelid]" id="modelid" onChange="change_tpl(this.value)">
                  <option value='' selected>请选择模型</option>
                  <volist name="models" id="vo">
                    <option value="{$vo.modelid}" >{$vo.name}</option>
                  </volist>
                </select></td>
            </tr>
            <tr>
              <th width="200">上级栏目：</th>
              <td><select name="info[parentid]" id="parentid">
                  <option value='0'>≡ 作为一级栏目 ≡</option>
                  {$category}
                </select></td>
            </tr>
            <tr>
              <th>栏目名称：</th>
              <td><input type="text" name="info[catname]" id="catname" class="input" value=""></td>
            </tr>
            <tr id="catdir_tr">
              <th>英文目录：</th>
              <td><input type="text" name="info[catdir]" id="catdir" class="input" value=""></td>
            </tr>
            <tr>
              <th>栏目缩略图：</th>
              <td><Form function="images" parameter="info[image],image,'',content"/><span class="gray"> 双击可以查看图片！</span></td>
            </tr>
            <tr>
              <th>是否终级栏目：</th>
              <td><input name="info[child]" type="checkbox" id="child"  value="0">
                终极栏目 (<font color="#FF0000">终极栏目才可以添加信息</font>)</td>
            </tr>
            <tr>
              <th>栏目简介：</th>
              <td><textarea name="info[description]" maxlength="255" style="width:300px;height:60px;"></textarea></td>
            </tr>
          </table>
        </div>
      </div>
      <div style="display:none">
        <div class="h_a">选项设置</div>
        <div class="table_full">
          <table width="100%" class="table_form ">
            <tr>
              <th width="200">是否在导航显示：</th>
              <td><ul class="switch_list cc ">
                  <li>
                    <label>
                      <input type='radio' name='info[ismenu]' value='1' checked>
                      <span>在导航显示</span></label>
                  </li>
                  <li>
                    <label>
                      <input type='radio' name='info[ismenu]' value='0'  >
                      <span>不在导航显示</span></label>
                  </li>
                </ul></td>
            </tr>
            <tr>
              <th>显示排序：</th>
              <td><input type="text" name="info[listorder]" id="listorder" class="input" value="0"></td>
            </tr>
            <tr>
              <th>后台增加/编辑信息：</th>
              <td><input type="checkbox"  value="1" checked  name="setting[generatehtml]">
                生成内容页； 生成列表：
                <select name="setting[generatelish]">
                  <option value="0" selected>不生成</option>
                  <option value="1" >生成当前栏目</option>
                  <option value="2" >生成首页</option>
                  <option value="3" >生成父栏目</option>
                  <option value="4" >生成当前栏目与父栏目</option>
                  <option value="5" >生成父栏目与首页</option>
                  <option value="6" >生成当前栏目、父栏目与首页</option>
                </select></td>
            </tr>
            <tr>
              <th>前台投稿审核：</th>
              <td><ul class="switch_list cc ">
                  <li>
                    <label>
                      <input type='radio' name="setting[member_check]" checked value='1'>
                      <span>需要审核</span></label>
                  </li>
                  <li>
                    <label>
                      <input type='radio' name="setting[member_check]" value='0'>
                      <span>无需审核</span></label>
                  </li>
                </ul></td>
            </tr>
            <tr>
              <th>管理投稿：</th>
              <td><select name="setting[member_admin]">
                  <option value="0">不能管理信息</option>
                  <option value="1" selected>可管理未审核信息</option>
                  <option value="2">只可编辑未审核信息</option>
                  <option value="3">只可删除未审核信息</option>
                  <option value="4">可管理所有信息</option>
                  <option value="5">只可编辑所有信息</option>
                  <option value="6">只可删除所有信息</option>
                </select>
                <input type="checkbox"  value="1" checked="" name="setting[member_editcheck]" >
                编辑信息需要审核</td>
            </tr>
            <tr>
              <th>投稿生成列表：</th>
              <td><select name="setting[member_generatelish]">
                  <option value="0" selected>不生成</option>
                  <option value="1">生成当前栏目</option>
                  <option value="2">生成首页</option>
                  <option value="3">生成父栏目</option>
                  <option value="4">生成当前栏目与父栏目</option>
                  <option value="5">生成父栏目与首页</option>
                  <option value="6">生成当前栏目、父栏目与首页</option>
                </select></td>
            </tr>
            <tr>
              <th>投稿增加点数：</th>
              <td><input type="text" class="input" value="0" name="setting[member_addpoint]">
                <span class="gray"><b class="red  ">点数</b> (不增加请设为0,扣点请设为负数)</span></td>
            </tr>
            <tr>
              <th ><strong>META Title（栏目标题）</strong><br/>
                针对搜索引擎设置的标题</th>
              <td><input name='setting[meta_title]' type='text' id='meta_title' class="input" value='' size='60' maxlength='60'></td>
            </tr>
            <tr>
              <th ><strong>META Keywords（栏目关键词）</strong><br/>
                关键字中间用半角逗号隔开</th>
              <td><textarea name='setting[meta_keywords]' id='meta_keywords' style="width:90%;height:40px"></textarea></td>
            </tr>
            <tr>
              <th ><strong>META Description（栏目描述）</strong><br/>
                针对搜索引擎设置的网页描述</th>
              <td><textarea name='setting[meta_description]' id='meta_description' style="width:90%;height:50px"></textarea></td>
            </tr>
          </table>
        </div>
      </div>
      <div style="display:none">
        <div class="h_a">模板设置</div>
        <div class="table_full">
          <table width="100%" class="table_form ">
            <tr id="fmmb">
              <th width="200">栏目首页模板：</th>
              <td><select name="setting[category_template]" id="category_template">
                  <option value="category<?php echo C("TMPL_TEMPLATE_SUFFIX")?>" selected>默认栏目首页：category<?php echo C("TMPL_TEMPLATE_SUFFIX")?></option>
                  <volist name="tp_category" id="vo">
                    <option value="{$vo}">{$vo}</option>
                  </volist>
                </select>
                <span class="gray">新增模板以category_x<?php echo C("TMPL_TEMPLATE_SUFFIX")?>形式</span>
                </td>
            </tr>
            <tr>
              <th width="200">栏目列表：</th>
              <td><select name="setting[list_template]" id="list_template">
                  <option value="list<?php echo C("TMPL_TEMPLATE_SUFFIX")?>" selected>默认列表页：list<?php echo C("TMPL_TEMPLATE_SUFFIX")?></option>
                  <volist name="tp_list" id="vo">
                    <option value="{$vo}">{$vo}</option>
                  </volist>
                </select>
                <span class="gray">新增模板以show_x<?php echo C("TMPL_TEMPLATE_SUFFIX")?>形式</span></td>
            </tr>
            <tr  id="plmb">
              <th width="200">栏目内容页：</th>
              <td><select name="setting[show_template]" id="show_template">
                  <option value="show<?php echo C("TMPL_TEMPLATE_SUFFIX")?>" selected>默认内容页：show<?php echo C("TMPL_TEMPLATE_SUFFIX")?></option>
                  <volist name="tp_show" id="vo">
                    <option value="{$vo}">{$vo}</option>
                  </volist>
                </select>
                <span class="gray">新增模板以show_x<?php echo C("TMPL_TEMPLATE_SUFFIX")?>形式</span></td>
            </tr>
          </table>
        </div>
      </div>
      <div style="display:none">
        <div class="h_a">生成设置</div>
        <div class="table_full">
          <table width="100%" class="table_form ">
            <tr>
              <th width="200">栏目生成Html：</th>
              <td><ul class="switch_list cc ">
                  <li>
                    <label>
                      <input type="radio" onClick="$('#category_php_ruleid').css('display','none');$('#category_html_ruleid').css('display','');$('#tr_domain').css('display','');$('.repagenum').css('display','');" value="1" name="setting[ishtml]">
                      <span>栏目生成静态</span></label>
                  </li>
                  <li>
                    <label>
                      <input type="radio" onClick="$('#category_php_ruleid').css('display','');$('#category_html_ruleid').css('display','none');$('#tr_domain').css('display','none');$('.repagenum').css('display','none');" checked="" value="0" name="setting[ishtml]">
                      <span>栏目不生成静态</span></label>
                  </li>
                </ul></td>
            </tr>
            <tr style="display:none" class="repagenum">
              <th width="200">栏目生成静态页数：</th>
              <td><input type="text" name="setting[repagenum]" id="listorder" class="input" value="{$setting.repagenum}"> <span class="gray"> 页(超过分页采用动态链接，0为不限)</span></td>
            </tr>
            <tr>
              <th>内容页生成Html：</th>
              <td><ul class="switch_list cc ">
                  <li>
                    <label>
                      <input type="radio" onClick="$('#show_php_ruleid').css('display','none');$('#show_html_ruleid').css('display','')" value="1" name="setting[content_ishtml]">
                      <span>内容页生成静态</span></label>
                  </li>
                  <li>
                    <label>
                      <input type="radio" onClick="$('#show_php_ruleid').css('display','');$('#show_html_ruleid').css('display','none')" checked="" value="0" name="setting[content_ishtml]">
                      <span>内容页不生成静态</span></label>
                  </li>
                </ul></td>
            </tr>
            <tr>
              <th>栏目页URL规则：</th>
              <td><div style="display:" id="category_php_ruleid"> {$category_php_ruleid} </div>
                <div style="display:none" id="category_html_ruleid"> {$category_html_ruleid} </div></td>
            </tr>
            <tr>
              <th>内容页URL规则：</th>
              <td><div style="display:" id="show_php_ruleid"> {$show_php_ruleid} </div>
                <div style="display:none" id="show_html_ruleid"> {$show_html_ruleid} </div></td>
            </tr>
            <tr style="display:none" id="tr_domain">
              <th>绑定域名：</th>
              <td><input type="text" value="" size="50" class="input" id="url" name="info[url]">
                <span class="gray"> 域名可为空，格式应该为http://www.abc3210.com/</span></td>
            </tr>
          </table>
        </div>
      </div>
      <div style="display:none">
        <div class="h_a">权限设置</div>
        <div class="table_full">
          <table width="100%" >
            <tr>
              <th width="200">角色权限：</th>
              <td><div class="user_group J_check_wrap">
                  <dl>
                  <volist name="Role_group" id="vo">
                    <dt>
                      <label><input type="checkbox" data-direction="y" data-checklist="J_check_priv_roleid{$vo.id}" class="checkbox J_check_all" <if condition=" $vo['id'] eq 1 "> disabled</if> />{$vo.name}</label>
                    </dt>
                    <dd>
                      <label><input  class="J_check" type="checkbox" data-yid="J_check_priv_roleid{$vo.id}"  name="priv_roleid[]" <if condition=" $vo['id'] eq 1 "> disabled</if>  value="init,{$vo.id}" ><span>查看</span></label>
                      <label><input class="J_check" type="checkbox" data-yid="J_check_priv_roleid{$vo.id}" name="priv_roleid[]" <if condition=" $vo['id'] eq 1 "> disabled</if>  value="add,{$vo.id}" ><span>添加</span></label>
                      <label><input class="J_check" type="checkbox" data-yid="J_check_priv_roleid{$vo.id}" name="priv_roleid[]" <if condition=" $vo['id'] eq 1 "> disabled</if>  value="edit,{$vo.id}" ><span>修改</span></label>
                      <label><input class="J_check" type="checkbox" data-yid="J_check_priv_roleid{$vo.id}" name="priv_roleid[]" <if condition=" $vo['id'] eq 1 "> disabled</if>  value="delete,{$vo.id}" ><span>删除</span></label>
                      <label><input class="J_check" type="checkbox" data-yid="J_check_priv_roleid{$vo.id}" name="priv_roleid[]" <if condition=" $vo['id'] eq 1 "> disabled</if>  value="listorder,{$vo.id}" ><span>排序</span></label>
                      <label><input class="J_check" type="checkbox" data-yid="J_check_priv_roleid{$vo.id}" name="priv_roleid[]" <if condition=" $vo['id'] eq 1 "> disabled</if>  value="push,{$vo.id}" ><span>推送</span></label>
                      <label><input class="J_check" type="checkbox" data-yid="J_check_priv_roleid{$vo.id}" name="priv_roleid[]" <if condition=" $vo['id'] eq 1 "> disabled</if>  value="move,{$vo.id}" ><span>移动</span></label>
                    </dd>
                   </volist>
                  </dl>
                </div></td>
            </tr>
            <tr>
              <th width="200">会员组权限：</th>
              <td><div class="user_group J_check_wrap">
                  <dl>
                  <volist name="Member_group" id="vo">
                    <dt>
                      <label><input type="checkbox" data-direction="y" data-checklist="J_check_priv_groupid{$vo.groupid}" class="checkbox J_check_all" <if condition=" $vo['id'] eq 1 "> disabled</if> />{$vo.name}</label>
                    </dt>
                    <dd>
                      <label><input  class="J_check" type="checkbox" data-yid="J_check_priv_groupid{$vo.groupid}"  name="priv_groupid[]" <if condition=" $vo['groupid'] eq 8 "> disabled</if>  value="add,{$vo.groupid}" ><span>允许投稿</span></label>
                    </dd>
                   </volist>
                  </dl>
                </div></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="">
      <div class="btn_wrap_pd">
        <button class="btn btn_submit mr10 " type="submit">提交</button>
      </div>
    </div>
  </form>
</div>
<script type="text/javascript" src="{$config_siteurl}statics/js/common.js?v"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/content_addtop.js"></script>
<script type="text/javascript">
$(function(){
    Wind.use('validate', 'ajaxForm', 'artDialog', function () {
        var form = $('form.J_ajaxForms');
        //ie处理placeholder提交问题
        if ($.browser.msie) {
            form.find('[placeholder]').each(function () {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            });
        }
        //表单验证开始
        form.validate({
			//是否在获取焦点时验证
			onfocusout:false,
			//是否在敲击键盘时验证
			onkeyup:false,
			//当鼠标掉级时验证
			onclick: false,
            //验证错误
            showErrors: function (errorMap, errorArr) {
				//errorMap {'name':'错误信息'}
				//errorArr [{'message':'错误信息',element:({})}]
				try{
					$(errorArr[0].element).focus();
					art.dialog({
						id:'error',
						icon: 'error',
						lock: true,
						fixed: true,
						background:"#CCCCCC",
						opacity:0,
						content: errorArr[0].message,
						cancelVal: '确定',
						cancel: function(){
							$(errorArr[0].element).focus();
						}
					});
				}catch(err){
				}
            },
            //验证规则
            rules: {
				"info[modelid]":{
					required:true
				},
				"info[catname]":{
					required:true
				},
				"info[catdir]":{
					required:true
				}
			},
            //验证未通过提示消息
            messages: {
				"info[modelid]":{
					required:"所属模型不能为空！"
				},
				"info[catname]":{
					required:"栏目名称不能为空！"
				},
				"info[catdir]":{
					required:"栏目目录不能为空！"
				}
			},
            //给未通过验证的元素加效果,闪烁等
            highlight: false,
            //是否在获取焦点时验证
            onfocusout: false,
            //验证通过，提交表单
            submitHandler: function (forms) {
                $(forms).ajaxSubmit({
                    url: form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                    dataType: 'json',
                    beforeSubmit: function (arr, $form, options) {
                        
                    },
                    success: function (data, statusText, xhr, $form) {
                        if(data.status){
							//添加成功
							Wind.use("artDialog", function () {
							    art.dialog({
							        id: "succeed",
							        icon: "succeed",
							        fixed: true,
							        lock: true,
							        background: "#CCCCCC",
							        opacity: 0,
							        content: data.info,
									button:[
										{
											name: '继续添加？',
											callback:function(){
												reloadPage(window);
												return true;
											},
											focus: true
										},{
											name: '返回栏目管理页',
											callback:function(){
												window.location.href = "{:U('Category/index',array('catid'=>$catid))}";
												return true;
											},
										}
									]
							    });
							});
						}else{
							isalert(data.info);
						}
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>