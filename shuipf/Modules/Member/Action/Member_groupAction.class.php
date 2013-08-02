<?php

/**
 * 会员组管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Member_groupAction extends AdminbaseAction {

    /**
     * 会员组管理 
     */
    public function index() {
        $member_group = M("Member_group");
        $data = $member_group->order(array("sort" => "ASC", "groupid" => "DESC"))->select();
        $member = M("Member");
        foreach ($data as $k => $v) {
            //统计会员总数
            $data[$k]['_count'] = $member->where(array("groupid" => $v['groupid']))->count();
        }
        $this->assign("data", $data);
        $this->display();
    }

    /**
     * 添加会员组 
     */
    public function add() {
        import('Form');
        if (IS_POST) {
            $member_group = D("Member_group");
            $data = $member_group->create();
            if ($data) {
                if ($member_group->group_add($data)) {
                    $this->success("添加成功！", U("Member_group/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($member_group->getError());
            }
        } else {
            $this->display();
        }
    }

    /**
     * 编辑会员组 
     */
    public function edit() {
        import('Form');
        $member_group = D("Member_group");
        if (IS_POST) {
            $data = $member_group->create();
            if ($data) {
                if ($member_group->group_edit($data)) {
                    $this->success("修改成功！", U("Member_group/index"));
                } else {
                    $this->error("修改失败！");
                }
            } else {
                $this->error($member_group->getError());
            }
        } else {
            $groupid = (int) $this->_get("groupid");
            $data = $member_group->where(array("groupid" => $groupid))->find();
            if (!$data) {
                $this->error("该会员组不存在！", U("Member_group/index"));
            }
            $this->assign("data", $data);
            $this->display();
        }
    }

    /**
     * 删除会员组 
     */
    public function delete() {
        if (IS_POST) {
            $groupid = $_POST['groupid'];
            if (!$groupid) {
                $this->error("没有指定需要删除的会员组别！");
            }
            $member_group = D("Member_group");
            foreach ($groupid as $k => $id) {
                $info = $member_group->where(array("groupid" => $id))->find();
                if ($info['issystem']) {
                    $this->error('系统用户组不能删除！');
                }
                $member_group->where(array("groupid" => $id))->delete();
                //删除附件
                service("Attachment")->api_delete('member_group-' . $id);
            }
            //更新缓存
            $member_group->Membergroup_cache();
            $this->success("删除成功！");
        }
    }

    /**
     * 排序 
     */
    public function sort() {
        if(IS_POST){
            $sort = $this->_post("sort");
            $member_group = M("Member_group");
            if(is_array($sort)){
                foreach($sort as $gid=>$pxid){
                    $member_group->where(array("groupid"=>$gid))->save(array("sort"=>$pxid));
                }
            }
            $this->success("排序更新成功！",U("Member_group/index"));
        }else{
            $this->error("请求方式错误！");
        }
    }

}

?>
