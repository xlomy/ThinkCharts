<?php

/**
 * 会员模型管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Member_modelAction extends AdminbaseAction {

    /**
     * 会员模型管理
     */
    public function index() {
        $Model = M("Model");
        $data = $Model->where(array("type" => 2))->order(array("modelid" => "DESC"))->select();
        $this->assign("data", $data);
        $this->display();
    }

    /**
     * 添加模型 
     */
    public function add() {
        if (IS_POST) {
            $Model = D("Model");
            $_POST['type'] = 2;
            $_POST['tablename'] = $_POST['tablename']?"member_" . $_POST['tablename']:"";
            $data = $Model->create();
            if ($data) {
                //插入模型表
                $modelid = $Model->add($data);
                if ($modelid) {
                    //创建表
                    $Model->AddModelMember($data['tablename'], $modelid);
                    //更新缓存
                    D("Model")->MemberModelCache();
                    $this->success("添加模型成功！",U("Member_model/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->display();
        }
    }

    /**
     * 编辑模型
     */
    public function edit() {
        $Model = D("Model");
        if (IS_POST) {
            if (empty($_POST['disabled'])) {
                $_POST['disabled'] = 0;
            }
            $data = $Model->create();
            if ($data) {
                if ($Model->save() !== false) {
                    D("Model")->MemberModelCache();
                    $this->success("更新模型成功！",U("Member_model/index"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $modelid = $this->_get("modelid");
            $data = $Model->where(array("modelid" => $modelid))->find();
            $this->assign("data", $data);
            $this->display();
        }
    }

    /**
     * 删除模型 
     */
    public function delete() {
        $modelid = $this->_get("modelid");
        $Model = D("Model");
        //这里可以根据缓存获取表名
        $modeldata = $Model->where(array("modelid" => $modelid))->find();
        if (!$modeldata) {
            $this->error("要删除的模型不存在！");
        }

        if ($Model->delete_model($modelid)) {
            D("Model")->MemberModelCache();
            $this->success("删除成功！", U("Member_model/index"));
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 模型移动 
     */
    public function move() {
        if (IS_POST) {
            $modelid = (int) $this->_post("modelid");
            $model = F("Model_Member");
            if (!$model[$modelid]) {
                $this->error("该模型不存在！");
            }
            $to_modelid = (int) $this->_post("to_modelid");
            if ($to_modelid < 1) {
                $this->error("请选择目标模型！");
            }
            if (!$model[$to_modelid]) {
                $this->error("目标模型不存在！");
            }
            if ($to_modelid == $modelid) {
                $this->error("目标模型与当前模型相同，无需转移！");
            }
            $member = M("Member"); //modelid
            if ($member->create()) {
                if ($member->where(array("modelid" => $modelid))->save(array("modelid" => $to_modelid))) {
                    $this->success("会员转移成功！", U("Member_model/index"));
                } else {
                    $this->error("会员转移失败！", U("Member_model/index"));
                }
            } else {
                $this->error($member->getError());
            }
        } else {
            $modelid = (int) $this->_get("modelid");
            $model = F("Model_Member");
            if (!$model[$modelid]) {
                $this->error("该模型不存在！");
            }
            import('Form');
            foreach ($model as $k => $v) {
                $modelselect[$k] = $v['name'];
            }
            $this->assign("modelid", $modelid);
            $this->assign("modelselect", $modelselect);
            $this->display();
        }
    }

}

?>
