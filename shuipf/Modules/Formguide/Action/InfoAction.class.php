<?php

/**
 * 表单信息管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class InfoAction extends AdminbaseAction {

    /**
     * 信息列表
     */
    public function index() {
        $formid = (int) $this->_get("formid");
        $tablename = M('Model')->where(array("modelid" => $formid))->getField("tablename");
        if (!$tablename) {
            $this->error("该表单不存在！");
        }
        $tablename = ucwords($tablename);
        $db = M($tablename);
        $count = $db->count();
        $page = $this->page($count, 20);
        $data = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("dataid" => "DESC"))->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign("data", $data);
        $this->assign("formid", $formid);
        $this->display();
    }

    /**
     * 删除信息
     */
    public function delete() {
        if (IS_POST) {
            $formid = (int)$this->_post("formid");
            $did = $_POST['did'];
            if(!is_array($did) || !$formid){
                $this->error("操作失败！");
            }
            foreach($did as $id){
                $this->del($id, $formid);
            }
            $this->success("删除成功！");
        } else {
            $formid = (int) $this->_get("formid");
            $dataid = (int) $this->_get("did");
            if($this->del($dataid, $formid)){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        }
    }

    /**
     *  删除
     * @param type $dataid 信息ID
     * @param type $formid 表单模型ID
     * @return boolean
     */
    protected function del($dataid, $formid) {
        static $tablename;
        if (!$formid || !$dataid) {
            return false;
        }
        if(!$tablename){
            $tablename = M('Model')->where(array("modelid" => $formid))->getField("tablename");
        }
        if (!$tablename) {
            return false;
        }
        $tablename = ucwords($tablename);
        //信息量-1
        M("Model")->where(array("modelid"=>$formid))->setDec("items");
        return M($tablename)->where(array("dataid" => $dataid))->delete();
    }

    /**
     * 信息查看
     */
    public function public_view() {
        $formid = (int) $this->_get("formid");
        $dataid = (int) $this->_get("dataid");
        if (!$formid || !$dataid) {
            $this->error("该信息不存在！<script>setTimeout(function(){window.top.art.dialog.list['check'].close();},1500);</script>");
        }
        $tablename = M('Model')->where(array("modelid" => $formid))->getField("tablename");
        if (!$tablename) {
            $this->error("该表单不存在！<script>setTimeout(function(){window.top.art.dialog.list['check'].close();},1500);</script>");
        }
        $tablename = ucwords($tablename);
        $data = M($tablename)->where(array("dataid" => $dataid))->find();
        if (!$data) {
            $this->error("该信息不存在！<script>setTimeout(function(){window.top.art.dialog.list['check'].close();},1500);</script>");
        }
        //引入输入表单处理类
        require RUNTIME_PATH . 'content_output.class.php';
        $content_form = new content_output($formid);
        $data['modelid'] = $formid;
        //字段内容
        $forminfos = $content_form->get($data);
        $fields = $content_form->fields;

        $this->assign("show_header", true);
        $this->assign("forminfos", $forminfos);
        $this->assign("data", $data);
        $this->assign("fields", $fields);
        $this->display("view");
    }

}

?>
