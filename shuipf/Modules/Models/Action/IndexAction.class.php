<?php

/* * 
 * 模型管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class IndexAction extends AdminbaseAction {

    protected $Model;

    function _initialize() {
        parent::_initialize();
        $this->Model = D("Model");
    }

    /**
     * 显示模型列表
     */
    public function index() {
        $data = $this->Model->where(array("type" => 0))->select();
        $this->assign("data", $data);
        $this->display();
    }

    /**
     * 添加模型
     */
    public function add() {
        if (IS_POST) {
            $data = $this->Model->create();
            if ($data) {
                //插入模型表
                $modelid = $this->Model->add();
                if ($modelid) {
                    //创建表
                    if ($this->Model->AddModelTable($data['tablename'], $modelid)) {
                        $this->success("添加模型成功！");
                    } else {
                        $this->Model->where(array("modelid" => $modelid))->delete();
                        $this->error("模型创建失败！");
                    }
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->Model->getError());
            }
        } else {
            $this->display();
        }
    }

    /**
     * 编辑模型
     */
    public function edit() {
        if (IS_POST) {
            $data = $this->Model->create();
            if ($data) {
                if ($this->Model->save() !== false) {
                    $this->success("更新模型成功！", U("Models/Index/index"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($this->Model->getError());
            }
        } else {
            $modelid = $this->_get("modelid");
            $data = $this->Model->where(array("modelid" => $modelid))->find();
            $this->assign("data", $data);
            $this->display();
        }
    }

    /**
     * 删除模型
     */
    public function delete() {
        $modelid = $this->_get("modelid");
        //检查该模型是否已经被使用
        $count = M("Category")->where(array("modelid" => $modelid))->count();
        if ($count > 0) {
            $this->error("该模型已经在使用中，请删除栏目后再进行删除！");
        }
        //这里可以根据缓存获取表名
        $modeldata = $this->Model->where(array("modelid" => $modelid))->find();
        if (!$modeldata) {
            $this->error("要删除的模型不存在！");
        }
        if ($this->Model->delete_model($modelid)) {
            $this->success("删除成功！", U("Models/Index/index"));
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 导出模型
     */
    public function export() {
        
    }

    /**
     * 导入模型
     */
    public function import() {
        
    }

    /**
     * 检查表是否已经存在
     */
    public function public_check_tablename() {
        $tablename = $this->_get("tablename");
        $count = $this->Model->where(array("tablename" => $tablename))->count();
        if ($count == 0) {
            $this->ajaxReturn("", "表名不存在！", true);
        } else {
            $this->ajaxReturn("", "表名已经存在！", false);
        }
    }

    /**
     * 模型的禁用与启用
     */
    public function disabled() {
        $modelid = intval($_GET['modelid']);
        $disabled = $_GET['disabled'] ? 0 : 1;
        $status = $this->Model->where(array('modelid' => $modelid))->save(array('disabled' => $disabled));
        if ($status !== false) {
            $this->success("操作成功！");
        } else {
            $this->error("操作失败！");
        }
    }

}

?>
