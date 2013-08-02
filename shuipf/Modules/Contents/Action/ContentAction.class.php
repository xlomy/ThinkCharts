<?php

/**
 * 内容管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ContentAction extends AdminbaseAction {

    public $categorys, $Content, $table_name, $fbtable_name, $Model;

    function _initialize() {
        parent::_initialize();
        //设置生成静态后缀为空，按URL规则生成相应后缀
        C('HTML_FILE_SUFFIX', "");
        //跳转时间
        $this->assign("waitSecond", 2000);

        $this->categorys = F("Category");
        $this->Model = F("Model");
        $catid = $this->_get("catid");
        if (empty($catid)) {
            $catid = $this->_post("catid");
        }
        //所有的权限 都分为 add(添加) edit(编辑) delete(删除) index(默认操作) listorder(排序) remove(移动文章) push(推送)
        //权限判断  如果方法是以 public_开头的，也不验证权限
        $ADMIN_AUTH_KEY = session(C("ADMIN_AUTH_KEY"));
        //非超级管理员需要进行权限控制
        if ($ADMIN_AUTH_KEY == "" || empty($ADMIN_AUTH_KEY) || $ADMIN_AUTH_KEY == false) {
            if (strpos(ACTION_NAME, 'public_') === false && ACTION_NAME != "index") {
                //操作
                $action = $this->categorys[$catid]['type'] == 0 ? ACTION_NAME : 'init';
                if ($action == "classlist") {
                    $action = "init";
                }
                $priv_datas = M("Category_priv")->where(array('catid' => $catid, 'is_admin' => 1, 'roleid' => session('roleid'), 'action' => $action))->select();
                if (!$priv_datas) {
                    //载入标签类
                    $this->error("您没有操作该项的权限！");
                }
            }
        }
        import('Form');
        if (isset($_GET['catid']) && intval($_GET['catid']) && empty($this->Model[$this->categorys[$catid]['modelid']])) {
            $this->error("模型或者栏目不存在！！");
        }
    }

    public function index() {
        $this->display();
    }

    /**
     * 显示对应栏目信息列表 
     */
    public function classlist() {
        $catid = $this->_get("catid");
        $Categorys = $this->categorys[$catid];
        $this->assign("Categorys", $Categorys);
        //是否搜索
        $search = $this->_get("search");
        $where = array();
        $where["catid"] = array("EQ", $catid);
        $Model = F("Model");
        if (!empty($Categorys)) {
            //取得主表名
            $tablename = $Model[$Categorys['modelid']]['tablename'];
            if (empty($tablename)) {
                $this->error("模型不存在！");
            }
            //检查模型是否被禁用
            if ($Model[$Categorys['modelid']]['disabled'] == 1) {
                $this->error("模型被禁用！");
            }
            $this->Content = new ContentModel(ucwords($tablename));
            //检查表是否存在
            if (!$this->Content->table_exists($tablename)) {
                $this->error("数据表不存在！");
            }
            //搜索相关开始
            if (!empty($search)) {
                //添加开始时间
                $start_time = $this->_get("start_time");
                if (!empty($start_time)) {
                    $start_time = strtotime($start_time);
                    $where["inputtime"] = array("EGT", $start_time);
                }
                //添加结束时间
                $end_time = $this->_get("end_time");
                if (!empty($end_time)) {
                    $end_time = strtotime($end_time);
                    $where["inputtime"] = array("ELT", $end_time);
                }

                if ($end_time > 0 && $start_time > 0) {
                    $where['inputtime'] = array(array('EGT', $start_time), array('ELT', $end_time));
                }

                //推荐
                $posids = $this->_get("posids");
                if (!empty($posids)) {
                    $where["posid"] = array("EQ", $posids);
                }
                //搜索字段
                $searchtype = (int) $this->_get("searchtype");
                //搜索关键字
                $keyword = Input::getVar($this->_get("keyword"));
                if (!empty($keyword)) {
                    $type_array = array('title', 'description', 'username');
                    if ($searchtype < 3) {
                        $searchtype = $type_array[$searchtype];
                        $where[$searchtype] = array("LIKE", "%" . $keyword . "%");
                    } elseif ($searchtype == 3) {
                        $where["id"] = array("EQ", (int) $keyword);
                    }
                }
                //状态
                $status = (int) $this->_get('status');
                if ($status > 0) {
                    $where['status'] = array("EQ", $status);
                }
            }

            //信息总数
            $count = $this->Content->where($where)->count();
            $page = $this->page($count, 20);
            $Content = $this->Content->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "DESC"))->select();
        } else {
            $this->error("该栏目不存在！");
        }

        $this->assign("search", $search);
        $this->assign("start_time", $start_time);
        $this->assign("end_time", $end_time);
        $this->assign("posids", $posids);
        $this->assign("searchtype", $searchtype);
        $this->assign("keyword", $keyword);

        $this->assign($Categorys);
        $this->assign("count", $count);
        $this->assign("catid", $catid);
        $this->assign("Content", $Content);
        $this->assign("Page", $page->show('Admin'));
        $this->display();
    }

    /**
     * 添加信息 
     */
    public function add() {
        if (IS_POST) {
            //栏目ID
            $catid = $_POST['info']['catid'] = intval($_POST['info']['catid']);
            if (empty($catid)) {
                $this->error("请指定栏目ID！");
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
                $Content = new Content();
                $status = $Content->add($_POST['info']);
                if ($status) {
                    $this->success("添加成功！");
                } else {
                    $this->error($Content->getError());
                }
            } else {
                $this->error("该栏目类型无法发布！");
            }
        } else {
            $catid = $this->_get("catid");
            //取得对应模型
            $category = $this->categorys[$catid];
            if (empty($category)) {
                $this->error("参数错误！");
            }
            //判断是否终极栏目
            if ($category['child']) {
                $this->error("只有终极栏目可以发布文章！");
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
            $this->display();
        }
    }

    /**
     * 编辑信息 
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
        if (empty($Categorys)) {
            $this->error("该栏目不存在！");
        }
        //栏目setting配置
        $cat_setting = unserialize($Categorys['setting']);

        //检查是否锁定
        $this->locking($catid, $id);

        $this->modelid = $modelid = $Categorys['modelid'];
        //取得表名
        $this->table_name = ucwords($this->Model[$Categorys['modelid']]['tablename']);
        $this->fbtable_name = $this->table_name . "_data";
        if (empty($this->table_name)) {
            $this->error("模型不存在！");
        }
        //检查模型是否被禁用
        if ($this->Model[$Categorys['modelid']]['disabled'] == 1) {
            $this->error("模型被禁用！");
        }

        if (IS_POST) {
            if (trim($_POST['info']['title']) == '') {
                $this->error("标题不能为空！");
            }

            import('Content');
            $Content = new Content();

            //取得原有文章信息
            $data = M($this->table_name)->where(array("catid" => $catid, "id" => $id))->find();

            //如果有自定义文件名，需要删除原来生成的静态文件
            if ($_POST['info']['prefix'] != $data['prefix'] && $cat_setting['content_ishtml']) {
                //删除原来的生成的静态页面
                $Content->deleteHtml($catid, $id, $data['inputtime'], $data['prefix']);
            }

            $status = $Content->edit($_POST['info'], $id);
            if ($status) {
                //解除信息锁定
                M("Locking")->where(array("userid" => AppframeAction::$Cache["uid"], "catid" => $catid, "id" => $id))->delete();
                $this->success("修改成功！");
            } else {
                $this->error($Content->getError());
            }
        } else {
            $this->Content = new ContentModel($this->table_name);
            //取得数据，这里使用关联查询
            $data = $this->Content->relation(true)->where(array("id" => $id))->find();
            if (!$data) {
                $this->error("该信息不存在！");
            }

            //锁定信息
            M("Locking")->add(array(
                "userid" => AppframeAction::$Cache["uid"],
                "username" => AppframeAction::$Cache["username"],
                "catid" => $catid,
                "id" => $id,
                "locktime" => time()
            ));

            //数据处理，把关联查询的结果集合并
            $datafb = $data[$this->fbtable_name];
            unset($data[$this->fbtable_name]);
            $data = array_merge($data, $datafb);

            //引入输入表单处理类
            require_cache(RUNTIME_PATH . 'content_form.class.php');
            $content_form = new content_form($modelid, $catid, $this->categorys);
            //字段内容
            $forminfos = $content_form->get($data);
            //生成对应的JS验证规则
            $formValidateRules = $content_form->formValidateRules;
            //js验证不通过提示语
            $formValidateMessages = $content_form->formValidateMessages;
            //js
            $formJavascript = $content_form->formJavascript;

            $this->assign("category", $Categorys);
            $this->assign("data", $data);
            $this->assign("catid", $catid);
            $this->assign("id", $id);
            $this->assign("uploadurl", CONFIG_SITEFILEURL);
            $this->assign("content_form", $content_form);
            $this->assign("forminfos", $forminfos);
            $this->assign("formValidateRules", $formValidateRules);
            $this->assign("formValidateMessages", $formValidateMessages);
            $this->assign("formJavascript", $formJavascript);
            $this->display();
        }
    }

    /**
     * 删除 
     */
    public function delete() {
        if (IS_POST) {
            $catid = (int) $this->_get("catid");
            if (!$catid) {
                $this->error("缺少栏目ID！");
            }
            if (empty($_POST['ids'])) {
                $this->error("没有信息被选中！");
            }
            import('Content');
            $Content = new Content();
            foreach ($_POST['ids'] as $id) {
                $Content->delete($id, $catid);
            }
            $this->success("删除成功！");
        } else {
            $catid = (int) $this->_get("catid");
            $id = (int) $this->_get("id");
            if (!$catid) {
                $this->error("缺少栏目ID！");
            }

            //检查是否锁定
            $this->locking($catid, $id);

            import('Content');
            $Content = new Content();
            if ($Content->delete($id, $catid)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * 文章审核 
     */
    public function public_check() {
        $catid = $this->_get("catid");
        import('Content');
        $Content = new Content();
        if (IS_POST) {
            $ids = $_POST['ids'];
            if (!$ids) {
                $this->error("没有信息被选中！");
            }
            foreach ($ids as $id) {
                $Content->check($catid, $id, 99);
            }
            $this->success("审核成功！");
        } else {
            $id = $this->_get("id");
            if (!$id) {
                $this->error("没有信息被选中！");
            }
            if ($Content->check($catid, $id, 99)) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        }
    }

    /**
     * 取消审核 
     */
    public function public_nocheck() {
        $catid = $this->_get("catid");
        import('Content');
        $Content = new Content();
        if (IS_POST) {
            $ids = $_POST['ids'];
            if (!$ids) {
                $this->error("没有信息被选中！");
            }
            foreach ($ids as $id) {
                $Content->check($catid, $id, 1);
            }
            $this->success("取消审核成功！");
        } else {
            $id = $this->_get("id");
            if (!$id) {
                $this->error("没有信息被选中！");
            }
            if ($Content->check($catid, $id, 1)) {
                $this->success("取消审核成功！");
            } else {
                $this->error("取消审核失败！");
            }
        }
    }

    /**
     * 排序 
     */
    public function listorder() {
        $listorders = $_POST['listorders'];
        $catid = $this->_get("catid");
        if (is_array($listorders)) {
            $category = $this->categorys[$catid];
            $modelid = $category['modelid'];
            $table_name = ucwords($this->Model[$modelid]['tablename']);
            $db = M($table_name);
            foreach ($listorders as $id => $v) {
                $db->where(array("id" => $id))->save(array("listorder" => $v));
            }
            $this->success("更新成功！", U("Contents/Content/classlist", "catid=$catid"));
        } else {
            $this->error("参数错误！");
        }
    }

    /**
     * 快速进入搜索
     */
    public function public_ajax_search() {
        if ($_GET['catname']) {
            if (preg_match('/([a-z]+)/i', $_GET['catname'])) {
                $field = 'letter';
                $catname = strtolower(trim($_GET['catname']));
            } else {
                $field = 'catname';
                $catname = trim($_GET['catname']);
            }
            $where = array();
            $where[$field] = array("LIKE", $catname . "%");
            $where['child'] = array("EQ", 0);
            $Category = M("Category");
            $data = $Category->where($where)->limit(10)->getField("catid,catid,type,catname,letter");

            echo json_encode($data);
        }
    }

    /**
     * 显示栏目菜单列表 
     */
    public function public_categorys() {
        //管理员uid
        $uid = AppframeAction::$Cache['uid'];
        $cache_class_list = S("cache_class_list_$uid");
        if (!$cache_class_list) {
            import('Tree');
            $tree = new Tree();
            //栏目权限 超级管理员例外
            if (session(C("ADMIN_AUTH_KEY")) == "") {
                $role_id = AppframeAction::$Cache['User']['role_id'];
                $priv_result = M("Category_priv")->where(array("roleid" => $role_id, 'action' => 'init'))->select();
                $priv_catids = array();
                foreach ($priv_result as $_v) {
                    $priv_catids[] = $_v['catid'];
                }
            }
            if (!empty($this->categorys)) {
                foreach ($this->categorys as $r) {
                    if ($r['type'] == 2 && $r['child'] == 0)
                        continue;
                    //只显示有init权限的，超级管理员除外
                    if (session(C("ADMIN_AUTH_KEY")) == "" && !in_array($r['catid'], $priv_catids)) {
                        $arrchildid = explode(',', $r['arrchildid']);
                        $array_intersect = array_intersect($priv_catids, $arrchildid);
                        if (empty($array_intersect)) {
                            continue;
                        }
                    }
                    $r['icon_type'] = $r['vs_show'] = '';
                    $r['type'] = 'classlist';
                    $r['add_icon'] = "<a target='right' href='" . U("Contents/Content/classlist", array("catid" => $r['catid'])) . "' onclick=javascript:openwinx('" . U("Contents/Content/add", array("catid" => $r['catid'])) . "','')><img src='" . AppframeAction::$Cache['Config']['siteurl'] . "statics/images/add_content.gif' alt='添加'></a> ";
                    $r['add_lists'] = "<a href='" . U("Contents/Content/classlist", array("catid" => $r['catid'])) . "' target='right' >" . $r['catname'] . "</a>";
                    $categorys[$r['catid']] = $r;
                }
            }
            if (!empty($categorys)) {
                $tree->init($categorys);
                $strs = "<span class='\$icon_type'>\$add_icon\$add_lists</span>";
                $strs2 = "<span class='folder'>\$catname</span>";
                $categorys = $tree->get_treeview(0, 'category_tree', $strs, $strs2, $ajax_show);
            } else {
                $categorys = "该站点下面还没有栏目，请先添加栏目";
            }
            //缓存
            S("cache_class_list_$uid", $categorys, 300);
        } else {
            $categorys = $cache_class_list;
        }
        $this->assign("categorys", $categorys);
        $this->display();
    }

    /**
     * 检测标题是否存在 
     */
    public function public_check_title($title = "", $catid = "") {
        $title = $title == "" ? $this->_get('data') : $title;
        $catid = $catid == "" ? $this->_get('catid') : $catid;
        if (empty($title)) {
            $this->ajaxReturn("", "标题没有重复！", true);
            exit;
        }
        $Cat = F("Category");
        $Model = F("Model");
        $tablename = ucwords($Model[$Cat[$catid]['modelid']]['tablename']);
        unset($Cat, $Model);
        $count = M($tablename)->where(array("title" => $title))->count();
        if ($count > 0) {
            $this->ajaxReturn("", "标题有重复！", false);
        } else {
            $this->ajaxReturn("", "标题没有重复！", true);
        }
    }

    /*
     *  相关文章选择
     */

    public function public_relationlist() {
        $this->Model = F("Model");
        if (!isset($_GET['modelid'])) {
            $this->error("缺少参数！");
        } else {
            $modelid = intval($_GET['modelid']);
            $this->table_name = ucwords($this->Model[$modelid]['tablename']);
            $this->Content = M($this->table_name);
            $where = array();
            $catid = intval($_GET['catid']);
            if ($catid) {
                $where['catid'] = array('eq', $catid);
            }

            $where['status'] = array('eq', 99);

            if (isset($_GET['keywords'])) {
                $keywords = trim($_GET['keywords']);
                $field = $_GET['searchtype'];
                if (in_array($field, array('id', 'title', 'keywords', 'description'))) {
                    if ($field == 'id') {
                        $where['id'] = array('eq', $keywords);
                    } else {
                        $where[$field] = array('like', '%' . $keywords . '%');
                    }
                }
            }
            $count = $this->Content->where($where)->count();
            $page = $this->page($count, 12);
            $data = $this->Content->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "DESC"))->select();

            $this->assign("Formcategory", Form::select_category($catid, 'name="catid"', "不限栏目", $modelid, 0, 1));
            $this->assign("data", $data);
            $this->assign("Page", $page->show('Admin'));
            $this->assign("modelid", $modelid);
            $this->display("relationlist");
        }
    }

    /**
     * 文章预览 
     */
    public function public_preview() {
        
    }

    /*
     * 审核所有内容
     */

    public function public_checkall() {
        
    }

    /**
     * 图片裁减 
     */
    public function public_imagescrop() {
        $picurl = $this->_get("picurl");
        $catid = $this->_get("catid");
        $module = $this->_get("module");
        $this->assign("picurl", $picurl);
        $this->assign("catid", $catid);
        $this->assign("module", $module);

        $this->display("imagescrop");
    }

    public function public_getsite_categorys() {
        $catid = $this->_get("catid");
        $models = F("Model");
        import('Tree');
        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $categorys = array();
        if (empty($_SESSION[C("ADMIN_AUTH_KEY")])) {
            $this->priv_db = M("Category_priv");
            $priv_result = $this->priv_db->where(array('action' => 'add', 'roleid' => $_SESSION['roleid'], 'is_admin' => 1))->select();
            $priv_catids = array();
            foreach ($priv_result as $_v) {
                $priv_catids[] = $_v['catid'];
            }
            if (empty($priv_catids))
                return '';
        }

        foreach ($this->categorys as $r) {
            if ($r['type'] != 0)
                continue;
            if (session("roleid") != 1 && !in_array($r['catid'], $priv_catids)) {
                $arrchildid = explode(',', $r['arrchildid']);
                $array_intersect = array_intersect($priv_catids, $arrchildid);
                if (empty($array_intersect))
                    continue;
            }
            $r['modelname'] = $models[$r['modelid']]['name'];
            $r['style'] = $r['child'] ? 'color:#8A8A8A;' : '';
            $r['click'] = $r['child'] ? '' : " id=\"cv" . $r['catid'] . "\" onclick=\"select_list(this,'" . safe_replace($r['catname']) . "'," . $r['catid'] . ")\" class='cu' title='" . safe_replace($r['catname']) . "'";
            $categorys[$r['catid']] = $r;
        }
        $str = "<tr \$click >
					<td align='center'>\$id</td>
					<td style='\$style'>\$spacer\$catname</td>
					<td align='center'>\$modelname</td>
				</tr>";
        $tree->init($categorys);
        $categorys = $tree->get_tree(0, $str);
        exit($categorys);
    }

    /**
     * 加载相关文章列表 
     */
    public function public_getjson_ids() {
        $this->modelid = $this->_get("modelid");
        $id = $this->_get("id");
        $this->Model = F("Model");
        $this->table_name = $this->Model[$this->modelid]['tablename'];
        if (empty($this->table_name)) {
            $this->ajaxReturn("", "", false);
            exit;
        }
        $this->Content = M(ucwords($this->table_name . "_data"));

        $r = $this->Content->where(array("id" => $id))->find();
        $this->Content = NULL;
        $where = array();
        if ($r['relation']) {
            $relation = str_replace('|', ',', $r['relation']);
            $where['id'] = array("in", $relation);
            $this->Content = M(ucwords($this->table_name));
            $datas = $this->Content->where($where)->select();
            foreach ($datas as $_v) {
                $_v['sid'] = 'v' . $_v['id'];
                $infos[] = $_v;
            }
        }
        unset($this->Content);
        unset($datas);
        $this->ajaxReturn($infos, "", true);
    }

    //批量移动文章
    public function remove() {
        if (IS_POST && isset($_POST['fromtype'])) {
            $catid = I('get.catid', '', 'intval');
            if (!$catid) {
                $this->error("请指定栏目！");
            }
            //移动类型
            $fromtype = I('post.fromtype', '', 'intval');
            //需要移动的信息ID集合
            $ids = $_POST['ids'];
            //需要移动的栏目ID集合
            $fromid = $_POST['fromid'];
            //目标栏目
            $tocatid = I('post.tocatid', '', 'intval');
            if (!$tocatid) {
                $this->error("目标栏目不正确！");
            }

            switch ($fromtype) {
                //信息移动
                case 0:
                    if ($ids) {
                        if ($tocatid == $catid) {
                            $this->error("目标栏目和当前栏目是同一个栏目！");
                        }
                        $modelid = $this->categorys[$tocatid]['modelid'];
                        if (!$modelid) {
                            $this->error("该模型不存在！");
                        }
                        //表名
                        $tablename = ucwords($this->Model[$modelid]['tablename']);
                        $where = array();
                        if (!$ids) {
                            $this->error("请选择需要移动信息！");
                        }
                        $ids = array_filter(explode('|', $_POST['ids']), "intval");
                        $where['id'] = array("IN", $ids);
                        $where['catid'] = array("EQ", $catid);
                        if (M($tablename)->where($where)->save(array("catid" => $tocatid))) {
                            //点击表
                            $hits = M("Hits");
                            foreach ($ids as $id) {
                                $hits->where(array("hitsid" => "c-$catid-$id", "catid" => $catid))->save(array("catid" => $tocatid, "hitsid" => "c-$tocatid-$id"));
                            }
                            $this->success("移动成功，请使用《批量更新URL》更新新的地址！", U("Create_html/update_urls"));
                        } else {
                            $this->error("移动失败");
                        }
                    } else {
                        $this->error("请选择需要移动的信息！");
                    }
                    break;
                //栏目移动
                case 1:
                    if (!$fromid) {
                        $this->error("请选择需要移动的栏目！");
                    }
                    $where = array();
                    $where['catid'] = array("IN", $fromid);
                    $modelid = $this->categorys[$catid]['modelid'];
                    if (!$modelid) {
                        $this->error("该模型不存在！");
                    }
                    $tablename = ucwords($this->Model[$modelid]['tablename']);
                    //进行栏目id更改
                    if (M($tablename)->where($where)->save(array("catid" => $tocatid))) {
                        //点击表
                        $hitsDb = M("Hits");
                        $classid = $fromid;
                        foreach ($classid as $catids) {
                            $hitsDb->execute("update " . C("DB_PREFIX") . "hits set `hitsid`=replace(hitsid,'c-$catids-','c-$tocatid-'),`catid`= $tocatid where `catid`=$catids");
                            /* $data = array();
                              $data['catid'] = $tocatid;
                              $data['hitsid'] = array('exp',"replace(hitsid,'c-{$catids}-','c-{$tocatid}-')");
                              $hitsDb->where(array("catid"=>$catids))->save($data); */
                        }
                        $this->success("移动成功，请使用《批量更新URL》更新新的地址！！", U("Create_html/update_urls"));
                    } else {
                        $this->error("移动失败");
                    }
                    break;
                default:
                    $this->error("请选择移动类型！");
                    break;
            }
        } else {
            $ids = $this->_request("ids");
            $ids = is_array($ids) ? implode("|", $ids) : $ids;
            $catid = I('get.catid', '', 'intval');
            if (!$catid) {
                $this->error("请指定栏目！");
            }
            $modelid = $this->categorys[$catid]['modelid'];
            import("Tree");
            $tree = new Tree();
            $tree->icon = array('&nbsp;&nbsp;│ ', '&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;';
            $categorys = array();
            foreach ($this->categorys as $cid => $r) {
                if ($r['type'])
                    continue;
                if ($modelid && $modelid != $r['modelid'])
                    continue;
                $r['disabled'] = $r['child'] ? 'disabled' : '';
                $r['selected'] = $cid == $catid ? 'selected' : '';
                $categorys[$cid] = $r;
            }
            $str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
            $tree->init($categorys);
            $string .= $tree->get_tree(0, $str);

            $str = "<option value='\$catid'>\$spacer \$catname</option>";
            $source_string = '';
            $tree->init($categorys);
            $source_string .= $tree->get_tree(0, $str);

            $this->assign("ids", $ids);
            $this->assign("string", $string);
            $this->assign("source_string", $source_string);
            $this->assign("catid", $catid);
            $this->display();
        }
    }

    /**
     * 文章推送
     */
    public function push() {
        if (IS_POST) {
            $id = $this->_post("id");
            $modelid = $this->_post('modelid');
            $catid = $this->_post("catid");
            $action = $this->_get("action");
            if (!$id || !$action || !$modelid || !$catid) {
                $this->error("参数不正确");
            }
            switch ($action) {
                //推荐位
                case "position_list":
                    $posid = $_POST['posid'];
                    if ($posid && is_array($posid)) {
                        $position_data_db = D('Position');
                        $fields = F("Model_field_" . $modelid);
                        $tablename = ucwords($this->Model[$modelid]['tablename']);
                        if (!$tablename) {
                            $this->error("模型不能为空！");
                        }
                        $ids = explode("|", $id);
                        $Content = new ContentModel($tablename);
                        foreach ($ids as $k => $aid) {
                            //取得信息
                            $re = $Content->relation(true)->where(array("id" => $aid))->find();
                            if ($re) {
                                //文章信息
                                $r = array_merge($re, $re[$tablename . '_data']);
                                unset($r[$tablename . '_data']);
                                //推送数据
                                $textcontent = array();
                                foreach ($fields AS $_key => $_value) {
                                    //判断字段是否入库到推荐位字段
                                    if ($_value['isposition']) {
                                        $textcontent[$_key] = $r[$_key];
                                    }
                                }
                                //推送到推荐位
                                $status = $position_data_db->position_update($aid, $modelid, $catid, $posid, $textcontent);
                                if ($status) {
                                    //更新信息推荐位标识
                                    $Content->relation(true)->where(array("id" => $aid))->save(array("posid" => 1));
                                }
                                $r = $re = null;
                            }
                        }
                        $this->success("推送到推荐位成功！");
                    } else {
                        $this->error("请选择推荐位！");
                    }
                    break;
                //同步发布到其他栏目
                case "push_to_category":
                    $ids = explode("|", $id);
                    $relation = $this->_post("relation");
                    if (!$relation) {
                        $this->error("请选择需要推送的栏目!");
                    }
                    $relation = explode("|", $relation);
                    if (is_array($relation)) {
                        //过滤相同栏目和自身栏目
                        foreach ($relation as $k => $classid) {
                            if ($classid == $catid) {
                                unset($relation[$k]);
                            }
                        }
                        //去除重复
                        $relation = array_unique($relation);
                        if (count($relation) < 1) {
                            $this->error("请选择需要推送的栏目！");
                        }
                        $tablename = ucwords($this->Model[$modelid]['tablename']);
                        if (!$tablename) {
                            $this->error("模型不能为空！");
                        }
                        $Content = new ContentModel($tablename);
                        import('Content');
                        $ContentAPI = new Content();
                        foreach ($ids as $k => $aid) {
                            //取得信息
                            $r = $Content->relation(true)->where(array("id" => $aid))->find();
                            $linkurl = $r['url'];
                            if ($r) {
                                $ContentAPI->othor_catid($relation, $linkurl, $r, $modelid);
                            }
                        }
                        $this->success("推送其他栏目成功！");
                    } else {
                        $this->error("请选择需要推送的栏目！");
                    }
                    break;
                default:
                    $this->error("请选择操作！");
                    break;
            }
        } else {
            $id = $this->_get("id");
            $action = $this->_get("action");
            $modelid = $this->_get('modelid');
            $catid = $this->_get("catid");
            if (!$id || !$action || !$modelid || !$catid) {
                $this->error("参数不正确！");
            }
            $tpl = $action == "position_list" ? "push_list" : "push_to_category";

            switch ($action) {
                //推荐位
                case "position_list":
                    $position = F("Position");
                    if (!empty($position)) {
                        $array = array();
                        foreach ($position as $_key => $_value) {
                            if ($_value['modelid'] && ($_value['modelid'] != $modelid) || ($_value['catid'] && strpos(',' . $this->categorys[$_value['catid']]['arrchildid'] . ',', ',' . $catid . ',') === false)) {
                                continue;
                            }
                            $array[$_key] = $_value['name'];
                        }
                        $this->assign("Position", $array);
                    }
                    break;
                //同步发布到其他栏目
                case "push_to_category":
                    break;
                default:
                    $this->error("请选择操作！");
                    break;
            }

            $this->assign("id", $id);
            $this->assign("action", $action);
            $this->assign("modelid", $modelid);
            $this->assign("catid", $catid);
            $this->assign("show_header", true);
            $this->display($tpl);
        }
    }

    /**
     * 同时发布到其他栏目 
     */
    public function add_othors() {
        $catid = $this->_get("catid");
        if (!empty($catid)) {
            $this->assign("catid", $catid);
        }
        $this->display();
    }

    /**
     * 信息锁定
     * @param type $catid 栏目ID
     * @param type $id 信息ID
     * @param type $userid 用户名ID
     * @param type $username 用户名
     * @return type
     */
    protected function locking($catid, $id, $userid = 0) {
        $db = M("Locking");
        $time = time();
        //锁定有效时间
        $Lock_the_effective_time = 300;
        if ($userid == 0) {
            $userid = AppframeAction::$Cache["uid"];
        }
        $where = array();
        $where['catid'] = array("EQ", $catid);
        $where['id'] = array("EQ", $id);
        $where['locktime'] = array("EGT", $time - $Lock_the_effective_time);
        $info = $db->where($where)->find();
        if ($info && $info['userid'] != AppframeAction::$Cache["uid"]) {
            $this->error("o(︶︿︶)o 唉，该信息已经被用户【<font color=\"red\">" . $info['username'] . "</font>】锁定~请稍后在修改！");
        }
        //删除失效的
        $where = array();
        $where['locktime'] = array("LT", $time - $Lock_the_effective_time);
        $db->where($where)->delete();
        return true;
    }

    /**
     * 锁定时间续期
     */
    public function public_lock_renewal() {
        $catid = (int) $this->_get("catid");
        $id = (int) $this->_get("id");
        $userid = AppframeAction::$Cache["uid"];
        $time = time();
        if ($catid && $id && $userid) {
            M("Locking")->where(array("id" => $id, "catid" => $catid, "userid" => $userid))->save(array("locktime" => $time));
        }
    }

}

?>