<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class UrlruleAction extends AdminbaseAction {

    protected $Urlrule;
    protected $Module;

    function _initialize() {
        parent::_initialize();
        $this->Urlrule = D("Urlrule");
        //可用模块缓存
        $this->Module=F("Module");
        if(!$this->Module){
            D("Module")->module_cache();
            $this->Module=F("Module");
        }
        //可用模块列表
        $Module = array();
        foreach($this->Module as $r){
            $Module[strtolower($r['module'])] = array(
                "module" => strtolower($r['module']),
                "name" => $r['name']
            );
        }
        //兼容，由于规则早期使用content而实际模块叫contents，所以这里做处理
        if($Module["contents"]){
            $Module["content"] = $Module["contents"];
            $Module["content"]["module"] = "content";
            unset($Module["contents"]);
        }
        $this->assign("Module", $Module);
    }

    /**
     * 显示 
     */
    public function index() {
        $infos = F("urlrules_detail");
        $this->assign("info", $infos);
        $this->display();
    }

    /**
     * 添加新规则
     */
    public function add() {
        if (IS_POST) {
             if ($this->Urlrule->create()) {
                $status = $this->Urlrule->add();
                if ($status) {
                    $this->success("添加成功！",U("Urlrule/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->Urlrule->getError());
            }
        } else {
            $this->display();
        }
    }

    /**
     * 编辑规则
     */
    public function edit() {
        if (IS_POST) {
            if ($this->Urlrule->create()) {
                $status = $this->Urlrule->save();
                if ($status !== false) {
                    $this->success("更新成功！",U("Urlrule/index"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($this->Urlrule->getError());
            }
        } else {
            $urlruleid = (int) $this->_get("urlruleid");
            $data = $this->Urlrule->where(array("urlruleid" => $urlruleid))->find();
            if (empty($data)) {
                $this->error("该规则不存在！");
            }
            $this->assign("data", $data);
            $this->display();
        }
    }

    /**
     * 删除规则
     */
    public function delete() {
        $urlruleid = $this->_get("urlruleid");
        $status = $this->Urlrule->where(array("urlruleid"=>$urlruleid))->delete();
        if($status){
            $this->success("删除成功！",U("Urlrule/index"));
        }else{
            $this->error("删除失败！");
        }
    }

}

?>
