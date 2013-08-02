<?php

/**
 * 推荐位管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class PositionAction extends AdminbaseAction {

    public function index() {
        $db = M("Position");
        $Model = F("Model");
        $Category = F("Category");
        $data = $db->order(array("listorder" => "ASC", "posid" => "DESC"))->select();
        foreach ($data as $k => $v) {
            $data[$k]['catid'] = $v['catid'] == 0 ? "所有栏目" : $Category[$v['catid']]['catname'];
            $data[$k]['modelid'] = $v['modelid'] == 0 ? "所有模型" : $Model[$v['modelid']]['name'];
        }
        $this->assign("data", $data);
        $this->display();
    }

    /**
     * 信息管理 
     */
    public function public_item() {

        if (IS_POST) {
            $items = count($_POST['items']) > 0 ? $_POST['items'] : $this->error("没有信息被选择！");
            $db = D("Position_data");
            if (is_array($items)) {
                foreach ($items as $item) {
                    $_v = explode('-', $item);
                    $db->delete_item((int) $_POST['posid'], (int) $_v[0], (int) $_v[1]);
                }
            }
            $this->success("移除成功！");
        } else {
            $posid = $this->_get("posid");
            $db = M("Position_data");
            $Category = F("Category");
            $Model = F("Model");
            $where = array();
            $where['posid'] = array("EQ", $posid);
            $count = $db->where($where)->count();
            $page = $this->page($count, 20);
            $data = $db->where($where)->order(array("listorder" => "DESC", "id" => "DESC"))->limit($page->firstRow . ',' . $page->listRows)->select();
            foreach ($data as $k => $v) {
                $data[$k]['data'] = unserialize($v['data']);
                $tab = ucwords($Model[$Category[$v['catid']]['modelid']]['tablename']);
                $data[$k]['data']['url'] = M($tab)->where(array("id" => $v['id']))->getField("url");
            }

            $this->assign("Category", $Category);
            $this->assign("Page", $page->show('Admin'));
            $this->assign("data", $data);
            $this->assign("posid", $posid);
            $this->display();
        }
    }

    /**
     * 添加推荐位 
     */
    public function add() {
        if (IS_POST) {
            $db = D("Position");
            $_POST['info'] = array_merge($_POST['info'], array(C("TOKEN_NAME") => $_POST[C("TOKEN_NAME")]));
            if ($db->create($_POST['info'])) {
                if ($db->add()) {
                    $this->success("添加成功！<font color=\"#FF0000\">请更新缓存！</font>", U("Contents/Position/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($db->getError());
            }
        } else {
            import('Form');
            $Model = F("Model");
            foreach ($Model as $k => $v) {
                $modelinfo[$v['modelid']] = $v['name'];
            }
            $this->assign("modelinfo", $modelinfo);
            $this->display();
        }
    }

    /**
     * 编辑推荐位 
     */
    public function edit() {
        $db = D("Position");
        if (IS_POST) {
            $_POST['info'] = array_merge($_POST['info'], array(C("TOKEN_NAME") => $_POST[C("TOKEN_NAME")]));
            if ($db->create($_POST['info'])) {
                if ($db->save() !== false) {
                    $this->success("更新成功！<font color=\"#FF0000\">请更新缓存！</font>", U("Contents/Position/index"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($db->getError());
            }
        } else {
            $posid = $this->_get("posid");
            $data = $db->where(array("posid" => $posid))->find();
            if (!$data) {
                $this->error('该推荐位不存在！');
            }
            import('Form');
            $Model = F("Model");
            foreach ($Model as $k => $v) {
                $modelinfo[$v['modelid']] = $v['name'];
            }
            $this->assign($data);
            $this->assign("modelinfo", $modelinfo);
            $this->display();
        }
    }

    /**
     *  删除 推荐位
     */
    public function delete() {
        $posid = $this->_get("posid");
        $db = M("Position");
        $status = $db->where(array("posid" => $posid))->delete();
        if ($status) {
            $d = M("Position_data")->where(array("posid" => $posid))->select();
            $Attachment = service("Attachment");
            foreach ($d as $k => $v) {
                M("Position_data")->where(array("posid" => $v['posid'], "id" => $v['id']))->delete();
                $Attachment->api_delete('position-' . $v['modelid'] . '-' . $v['id']);
            }
            $this->success("删除成功！<font color=\"#FF0000\">请更新缓存！</font>", U("Contents/Position/index"));
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 排序 
     */
    public function public_item_listorder() {
        if (IS_POST) {
            $db = M("Position_data");
            foreach ($_POST['listorders'] as $_k => $listorder) {
                $pos = array();
                $pos = explode('-', $_k);
                $db->where(array('id' => $pos[1], 'catid' => $pos[0], 'posid' => $_POST['posid']))->data(array('listorder' => $listorder))->save();
            }
            $this->success("排序更新成功！");
        } else {
            $this->error("请使用POST方式提交！");
        }
    }

    /**
     * 信息管理编辑 
     */
    public function public_item_manage() {
        $db = D("Position_data");
        if (IS_POST) {
            if($_POST['thumb']){
                $_POST['data']['thumb'] = $_POST['thumb'];
                $_POST['thumb'] = 1;
            }else{
                $_POST['thumb'] = 0;
            }
            if ($db->Position_edit($_POST)) {
                $this->success("更新成功！");
            } else {
                $this->error("更新失败！");
            }
        } else {
            $id = $this->_get("id");
            $modelid = $this->_get("modelid");
            $data = $db->where(array("id" => $id, "modelid" => $modelid))->find();
            if (!$data) {
                $this->error("该信息不存在！");
            }
            $data['data'] = unserialize($data['data']);
            import('Form');
            $this->assign($data);
            $this->display();
        }
    }

    /**
     * 推荐位添加栏目加载
     */
    public function public_category_load() {
        $modelid = intval($_GET['modelid']);
        import('Form');
        $category = Form::select_category('', 'name="info[catid]"', "=不限栏目=", $modelid, 0, 1);
        echo $category;
    }

}

?>
