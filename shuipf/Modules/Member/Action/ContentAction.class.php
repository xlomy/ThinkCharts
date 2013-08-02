<?php

/**
 * 投稿管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ContentAction extends MemberbaseAction {

    public $categorys, $Model;

    function _initialize() {
        parent::_initialize();
        $this->categorys = F("Category");
        $this->Model = F("Model");
        import('Form');
    }

    /**
     * 在线投稿 
     */
    public function add() {
        $CategoryPriv = M("CategoryPriv");
        if (IS_POST) {
            //栏目ID
            $catid = $_POST['info']['catid'] = intval($_POST['info']['catid']);
            if (empty($catid)) {
                $this->error("请指定栏目ID！");
            }
            $Prv = $CategoryPriv->where(array("catid" => $catid, "roleid" => AppframeAction::$Cache['User']['groupid'], "is_admin" => 0, "action" => "add"))->find();
            if (!$Prv) {
                $this->error("您没有该栏目投稿权限！");
            }
            if (trim($_POST['info']['title']) == '') {
                $this->error("标题不能为空！");
            }
            //获取当前栏目配置
            $category = $this->categorys[$catid];
            //栏目类型为0
            if ($category['type'] == 0) {
                //模型ID
                $this->modelid = $modelid = $this->categorys[$catid]['modelid'];
                //检查模型是否被禁用
                if ($this->Model[$modelid]['disabled'] == 1) {
                    $this->error("模型被禁用！");
                }
                //setting 配置
                $setting = unserialize($category['setting']);
                import('Content');
                define("GROUP_MODULE", "Contents");
                $Content = new Content();
                $id = $Content->add($_POST['info']);
                if ($id) {
                    //前台投稿，根据栏目配置和用户配置
                    $Member_group = F("Member_group");
                    $groupid = AppframeAction::$Cache['User']['groupid'];
                    //如果会员组设置中设置，投稿不需要审核，直接无视栏目设置
                    if ($Member_group[$groupid]['allowpostverify'] || (int) $setting['member_check'] == 0) {
                        if (service("Passport")->user_integral(AppframeAction::$Cache['uid'], $setting['member_addpoint'])) {
                            $integral = 1;
                        } else {
                            $integral = 0;
                        }
                    } else {
                        $integral = 0;
                    }
                    //添加投稿记录
                    M("MemberContent")->add(array(
                        "catid" => $catid,
                        "content_id" => $id,
                        "userid" => AppframeAction::$Cache['uid'],
                        'integral' => $integral,
                        "time" => time(),
                    ));
                    $this->success("投稿成功！", U("Content/mybutors"));
                } else {
                    $this->error($Content->getError());
                }
            } else {
                $this->error("该栏目类型无法发布！");
            }
        } else {
            import("Tree");
            $tree = new Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
            //栏目列表 可以用缓存的方式
            $array = $this->categorys;
            //栏目ID
            $catid = $this->_get("catid");
            //查询出全部权限
            $Prv = $CategoryPriv->where(array("is_admin" => 0, "action" => "add"))->select();
            foreach ($array as $k => $v) {
                if ($v['catid'] == $catid) {
                    $array[$k]['selected'] = "selected";
                }
                if (in_array(array("catid" => $v['catid'], "roleid" => AppframeAction::$Cache['User']['groupid'], "is_admin" => 0, "action" => "add"), $Prv)) {
                    //只有终极栏目可以发表
                    if ($v['child'] == 1) {
                        $array[$k]['disabled'] = "disabled";
                    } else {
                        $array[$k]['disabled'] = "";
                    }
                } else {
                    $array[$k]['disabled'] = "disabled";
                }
            }
            $tree->init($array);
            $categorydata = $tree->get_tree(0, $str, $parentid);
            $this->assign("catid", false);
            $this->assign("Category", $categorydata);
            //如果取不到栏目ID，显示栏目列表
            if (!$catid) {
                $this->display();
                exit;
            }
            $catid = $this->_get("catid");
            //取得对应模型
            $category = $this->categorys[$catid];
            if (empty($category)) {
                $this->error("参数错误！");
            }
            //判断是否终极栏目
            if ($category['child']) {
                $this->error("该栏目不允许投稿！");
            }
            $Prv = $CategoryPriv->where(array("catid" => $catid, "roleid" => AppframeAction::$Cache['User']['groupid'], "is_admin" => 0, "action" => "add"))->find();
            if (!$Prv) {
                $this->error("您没有该栏目投稿权限！");
            }
            //模型ID
            $modelid = $category['modelid'];
            //检查模型是否被禁用
            if ($this->Model[$modelid]['disabled'] == 1) {
                $this->error("模型被禁用！");
            }
            //取模型ID，依模型ID来生成对应的表单
            require_cache(RUNTIME_PATH . 'content_form.class.php');
            //实例化表单类 传入 模型ID 栏目ID 栏目数组
            $content_form = new content_form($modelid, $catid, $this->categorys);
            //生成对应字段的输入表单
            $forminfos = $content_form->get();
            $forminfos = array_merge($forminfos['base'], $forminfos['senior']);
            //生成对应的JS验证规则
            $formValidateRules = $content_form->formValidateRules;
            //js验证不通过提示语
            $formValidateMessages = $content_form->formValidateMessages;
            //js
            $formJavascript = $content_form->formJavascript;
            //取得当前栏目setting配置信息
            $setting = unserialize($category['setting']);

            $workflowid = $setting['workflowid'];

            //当前登陆用户名
            $admin_username = AppframeAction::$Cache["username"];
            //var_dump($category);exit;
            $this->assign("catid", $catid);
            $this->assign("uploadurl", CONFIG_SITEFILEURL);
            $this->assign("content_form", $content_form);
            $this->assign("forminfos", $forminfos);
            $this->assign("formValidateRules", $formValidateRules);
            $this->assign("formValidateMessages", $formValidateMessages);
            $this->assign("formJavascript", $formJavascript);
            $this->assign("setting", $setting);
            $this->assign("admin_username", $admin_username);
            $this->assign("category", $category);
            $this->assign("workflowid", $workflowid);
            $this->assign("Category", $categorydata);
            $this->assign("uploadurl", CONFIG_SITEFILEURL);
            $this->display();
        }
    }

    /**
     * 编辑 
     */
    public function edit() {
        $catid = $this->_get("catid");
        $catid = empty($catid) ? (int) $_POST['info']['catid'] : $catid;
        $id = $this->_get("id");
        $id = empty($id) ? $this->_post("id") : $id;

        if (empty($catid) || empty($id)) {
            $this->error("参数不完整！");
        }

        $Categorys = $this->categorys[$catid];
        $this->modelid = $modelid = $Categorys['modelid'];

        //检查模型是否被禁用
        if ($this->Model[$modelid]['disabled'] == 1) {
            $this->error("模型被禁用！");
        }
        if (IS_POST) {
            if (trim($_POST['info']['title']) == '') {
                $this->error("标题不能为空！");
            }
            import('Content');
            $Content = new Content();
            define("GROUP_MODULE", "Contents");
            $status = $Content->edit($_POST['info'], $id);
            if ($status) {
                $this->success("修改成功！", U("Content/mybutors"));
            } else {
                $this->error($Content->getError());
            }
        } else {
            $this->table_name = ucwords($this->Model[$Categorys['modelid']]['tablename']);
            $this->fbtable_name = $this->table_name . "_data";
            $this->Content = new ContentModel($this->table_name);
            //取得数据，这里使用关联查询
            $data = $this->Content->relation(true)->where(array("id" => $id))->find();
            if (!$data) {
                $this->error("该信息不存在！");
            }
            //数据处理，把关联查询的结果集合并
            $datafb = $data[$this->fbtable_name];
            unset($data[$this->fbtable_name]);
            $data = array_merge($data, $datafb);
            //引入输入表单处理类
            require_cache(RUNTIME_PATH . 'content_form.class.php');
            $content_form = new content_form($modelid, $catid, $this->categorys);
            //字段内容
            $forminfos = $content_form->get($data);
            $forminfos = array_merge($forminfos['base'], $forminfos['senior']);
            //生成对应的JS验证规则
            $formValidateRules = $content_form->formValidateRules;
            //js验证不通过提示语
            $formValidateMessages = $content_form->formValidateMessages;
            //js
            $formJavascript = $content_form->formJavascript;

            $this->assign("data", $data);
            $this->assign("catid", $catid);
            $this->assign("id", $id);
            $this->assign("uploadurl", CONFIG_SITEFILEURL);
            $this->assign("content_form", $content_form);
            $this->assign("forminfos", $forminfos);
            $this->assign("formValidateRules", $formValidateRules);
            $this->assign("formValidateMessages", $formValidateMessages);
            $this->assign("formJavascript", $formJavascript);
            $this->assign("Categorys", $this->categorys);
            $this->display();
        }
    }

    /**
     * 删除 
     */
    public function delete() {
        $id = (int) $this->_get("id");
        $catid = (int) $this->_get("catid");
        $member_content = M("MemberContent");
        $mb_con = $member_content->where(array("content_id" => $id, "catid" => $catid, "userid" => AppframeAction::$Cache['uid']))->find();
        if ($mb_con) {
            $category = $this->categorys[$catid];
            $setting = unserialize($category['setting']);
            //模型ID
            $modelid = $category['modelid'];
            $tablename = ucwords($this->Model[$modelid]['tablename']);
            $status = M($tablename)->where(array("id" => $id, "catid" => $catid, "sysadd" => 0))->getField("status");
            if ($status) {
                //审核通过的文章
                if ($status == 99) {
                    if ((int) $setting['member_admin'] == 4 || (int) $setting['member_admin'] == 6) {
                        import('Content');
                        $Content = new Content();
                        if ($Content->delete($id, $catid)) {
                            $this->success("删除成功！");
                        } else {
                            $this->error("删除失败！");
                        }
                    } else {
                        $this->error("你没有权限操！");
                    }
                } else {
                    if ((int) $setting['member_admin'] == 1 || (int) $setting['member_admin'] == 3 || (int) $setting['member_admin'] == 6) {
                        import('Content');
                        $Content = new Content();
                        if ($Content->delete($id, $catid)) {
                            $this->success("删除成功！");
                        } else {
                            $this->error("删除失败！");
                        }
                    } else {
                        $this->error("你没有权限操！");
                    }
                }
            } else {
                $this->error("出现错误，系统找不到该信息的相关记录！");
            }
        } else {
            $this->error("该信息不存在！");
        }
    }

    /**
     * 已经发布的稿件
     */
    public function mybutors() {
        $member_content = M("MemberContent");
        $userid = AppframeAction::$Cache['uid'];
        $count = $member_content->where(array("userid" => $userid))->count();
        $page = $this->page($count, 20);
        $databutors = $member_content->where(array("userid" => $userid))->limit($page->firstRow . ',' . $page->listRows)->order(array("time" => "DESC"))->select();
        foreach ($databutors as $r) {
            $modelid = $this->categorys[$r['catid']]['modelid'];
            $tablename = ucwords($this->Model[$modelid]['tablename']);
            $info = M($tablename)->where(array("id" => $r['content_id'], "sysadd" => 0))->find();
            $info["_setting"] = unserialize($this->categorys[$r['catid']]['setting']);
            $data[$info['id']] = $info;
        }

        $this->assign("data", $data);
        $this->assign("Category", $this->categorys);
        $this->assign("Page", $page->show('Admin'));
        $this->display();
    }

}

?>
