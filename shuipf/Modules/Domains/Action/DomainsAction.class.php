<?php

/**
 * 二级域名绑定
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class DomainsAction extends AdminbaseAction {
    
    public $Module;
    
    function _initialize() {
        parent::_initialize();
        //可用模块列表
        $Module = array();
        foreach(F("Module") as $r){
            $Module[$r['module']] = array(
                "module" => $r['module'],
                "name" => $r['name']
            );
        }
        $this->assign("Module", $Module);
        $this->Module = $Module;
    }

    public function index() {
        $db = M("Domains");
        $data = $db->select();
        
        $this->assign("data",$data);
        $this->display();
    }
    
    //添加
    public function add(){
        if(IS_POST){
            $db = D("Domains");
            $data = $db->create();
            if($data){
                $status = $db->AddDomains($data);
                if($status !== false){
                    $this->success("添加成功，请及时更新缓存！");
                }else{
                    $this->error("添加失败！");
                }
            }else{
                $this->error($db->getError());
            }
        }else{
            $this->display();
        }
    }

    //编辑
    public function edit(){
        if(IS_POST){
            $db = D("Domains");
            $data = $db->create();
            if($data){
                $status = $db->editDomains($data);
                if($status !== false){
                    $this->success("编辑成功，请及时更新缓存！");
                    F("Domains_list",null);
                }else{
                    $this->error("编辑失败！");
                }
            }else{
                $this->error($db->getError());
            }
        }else{
            $db = M("Domains");
            $id = (int)$this->_get("id");
            $info = $db->where(array("id"=>$id))->find();
            if(!$info){
                $this->error("该信息不存在！");
            }
            
            $this->assign($info);
            $this->display();
        }
    }
    
    //删除
    public function delete(){
        $id = (int)$this->_get("id");
        $db = M("Domains");
        $status = $db->where(array("id"=>$id))->delete();
        if($status !== false){
            $this->success("删除成功，请更新缓存！");
        }else{
            $this->error("删除失败！");
        }
    }
}

?>
