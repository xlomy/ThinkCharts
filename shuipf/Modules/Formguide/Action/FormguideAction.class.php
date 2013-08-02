<?php

/**
 * 表单管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class FormguideAction extends AdminbaseAction {

    protected $db, $filepath, $tpl;

    function _initialize() {
        parent::_initialize();
        $this->db = D("Formguide");
        //模块安装后，模板字段安装在Default主题下！
        $this->filepath = TEMPLATE_PATH . "Default/Formguide/";
        $this->tpl = str_replace($this->filepath, "", glob($this->filepath . "Show" . DIRECTORY_SEPARATOR . 'show*'));
        $this->tpl = str_replace(array("Show" . DIRECTORY_SEPARATOR, C("TMPL_TEMPLATE_SUFFIX")), "", $this->tpl);
        C('HTML_FILE_SUFFIX', "");
    }

    /**
     * 表单列表
     */
    public function index() {
        if (IS_POST) {
            //删除
            $formid = $_POST['formid'];
            if (is_array($formid)) {
                foreach ($formid as $modelid) {
                    $this->DeleteTable($modelid);
                }
                $this->success("删除成功！");
            } else {
                $this->error("请选择需要删除的表单！");
            }
        } else {
            $where = array("type" => 3);
            $count = $this->db->where($where)->count();
            $page = $this->page($count, 20);
            $data = $this->db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("modelid" => "DESC"))->select();
            $this->assign("Page", $page->show('Admin'));
            $this->assign("menuid", $this->_get("menuid"));
            $this->assign("big_menu", $big_menu);
            $this->assign("data", $data);
            $this->display();
        }
    }

    /**
     * 添加表单
     */
    public function add() {
        if (IS_POST) {
            $data = array();
            //表单令牌
            $data[C("TOKEN_NAME")] = $_POST[C("TOKEN_NAME")];
            if ($_POST['setting']['starttime']) {
                $_POST['setting']['starttime'] = strtotime($_POST['setting']['starttime']);
            }
            if ($_POST['setting']['endtime']) {
                $_POST['setting']['endtime'] = strtotime($_POST['setting']['endtime']);
            }
            $data['setting'] = serialize($_POST['setting']); //扩展配置
            $data['js_template'] = $_POST['info']['show_js_template']; //js模板
            $data['name'] = $_POST['info']['name']; //表单名称
            $data['tablename'] = $_POST['info']['tablename']?"form_" . $_POST['info']['tablename']:""; //表名
            $data['description'] = $_POST['info']['description']; //表单介绍
            $data['show_template'] = $_POST['info']['show_template']; //模板选择
            $data = $this->db->create($data);
            if ($data) {
                $data['type'] = 3; //类型
                $modelid = $this->db->add($data);
                if ($modelid) {
                    $TableName = $data['tablename'];
                    $statis = $this->AddTable($TableName);
                    if (!$statis) {
                        $this->db->where(array("modelid" => $modelid))->delete();
                        $this->error("表创建失败！");
                    }
                    //创建表
                    $this->success("添加表单成功！");
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->db->getError());
            }
        } else {
            import("Form");
            foreach ($this->tpl as $v) {
                $tpl[$v] = $v;
            }
            $this->assign("template", $tpl);
            $this->display();
        }
    }

    /**
     * 表单编辑
     */
    public function edit() {
        if (IS_POST) {
            $data = array();
            //表单令牌
            $data[C("TOKEN_NAME")] = $_POST[C("TOKEN_NAME")];
            $data['modelid'] = (int) $_POST['modelid'];
            if (!$data['modelid']) {
                $this->error("该表单不存在！");
            }
            if ($_POST['setting']['starttime']) {
                $_POST['setting']['starttime'] = strtotime($_POST['setting']['starttime']);
            }
            if ($_POST['setting']['endtime']) {
                $_POST['setting']['endtime'] = strtotime($_POST['setting']['endtime']);
            }
            $data['setting'] = serialize($_POST['setting']); //扩展配置
            $data['js_template'] = $_POST['info']['show_js_template']; //js模板
            $data['name'] = $_POST['info']['name']; //表单名称
            $data['description'] = $_POST['info']['description']; //表单介绍
            $data['show_template'] = $_POST['info']['show_template']; //模板选择
            unset($data['type'], $data['tablename']);
            if ($_POST['_name'] == $data['name']) {
                unset($data['name']);
            }
            
            $data = $this->db->create($data);
            if ($data) {
                $status = $this->db->save($data);
                if ($status !== false) {
                    $this->success("更新成功！", U("Formguide/index"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($this->db->getError());
            }
        } else {
            $formid = (int) $this->_get('formid');
            $r = $this->db->where(array("modelid" => $formid))->find();
            if (!$r) {
                $this->error("该表单不存在！");
            }
            $r['setting'] = unserialize($r['setting']);
            $r['tablename'] = str_replace("form_", "", $r['tablename']);
            import("Form");
            $this->assign($r);
            foreach ($this->tpl as $v) {
                $tpl[$v] = $v;
            }
            $this->assign("template", $tpl);
            $this->display();
        }
    }

    /**
     * 删除表单
     */
    public function delete() {
        if (IS_POST) {
            
        } else {
            $formid = intval($_GET['formid']);
            if ($this->DeleteTable($formid)) {
                $this->success("删除成功！");
            } else {
                $this->error('删除失败！');
            }
        }
    }

    /**
     * 禁用/启用状态转换
     */
    public function status() {
        $modelid = intval($_GET['formid']);
        $disabled = $_GET['disabled'] ? 0 : 1;
        $status = $this->db->where(array('modelid' => $modelid))->save(array('disabled' => $disabled));
        if ($status) {
            $this->success("操作成功，请更新缓存！");
        } else {
            $this->error("操作失败！");
        }
    }
    
    //调用
    public function public_call(){
        $formid = $this->_get("formid");
        $this->assign("formid",$formid);
        $this->assign("show_header", true);
        $this->display("call");
    }

    /**
     * ajax 检测表是重复
     */
    public function public_checktable() {
        if (isset($_GET['formid']) && !empty($_GET['formid'])) {
            $formid = intval($_GET['formid']);
        }
        $r = $this->db->where(array('tablename' => "form_" . $_GET['tablename']))->find();
        if (!$r['modelid']) {
            exit('1');
        } elseif ($r['modelid'] && ($r['modelid'] == $formid)) {
            exit('1');
        } else {
            exit('0');
        }
    }

    /**
     * 创建数据表
     * @param type $TableName 表名
     * @return boolean
     */
    private function AddTable($TableName) {
        if (!$TableName) {
            return false;
        }
        $sql = "CREATE TABLE IF NOT EXISTS `think_form_table` (
                        `dataid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                        `userid` mediumint(8) unsigned NOT NULL,
                       `username` varchar(20) NOT NULL,
                       `datetime` int(10) unsigned NOT NULL,
                       `ip` char(15) NOT NULL,
                       PRIMARY KEY (`dataid`)
                    ) ENGINE=MyISAM;";
        //表名替换
        $sql = str_replace("think_form_table", C("DB_PREFIX") . $TableName, $sql);
        $status = $this->db->execute($sql);
        if ($status == false && is_bool($status)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 删除表单模型
     * @param type $modelid 表单ID
     * @return boolean
     */
    private function DeleteTable($modelid) {
        if (!$modelid) {
            return false;
        }
        $r = $this->db->where(array("modelid" => $modelid, 'type' => 3))->find();
        if (!$r) {
            return false;
        }
        //删除表
        $this->db->DeleteTable($r['tablename']);
        $this->db->where(array("modelid" => $modelid, 'type' => 3))->delete();
        return true;
    }

}

?>
