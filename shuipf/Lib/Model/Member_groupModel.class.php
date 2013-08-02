<?php

//会有组
class Member_groupModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('name', 'require', '会员组名称不能为空！'),
        array('name', '', '该会员组已经存在！', 0, 'unique', 3),
        array('point', 'require', '积分不能为空！'),
        array('starnum', 'require', '星星数不能为空！'),
    );

    //编辑会员组
    public function group_edit($data) {
        if (!is_array($data)) {
            return false;
        }
        $data['allowpost'] = $data['allowpost'] ? $data['allowpost'] : 0;
        $data['allowpostverify'] = $data['allowpostverify'] ? $data['allowpostverify'] : 0;
        $data['allowupgrade'] = $data['allowupgrade'] ? $data['allowupgrade'] : 0;
        $data['allowsendmessage'] = $data['allowsendmessage'] ? $data['allowsendmessage'] : 0;
        $data['allowattachment'] = $data['allowattachment'] ? $data['allowattachment'] : 0;
        $data['allowsearch'] = $data['allowsearch'] ? $data['allowsearch'] : 0;
        $status = $this->save($data);
        if ($status !== false) {
            //更新附件状态
            if ($data['icon']) {
                //更新附件状态
                service("Attachment")->api_update('', 'member_group-' . $data['groupid'], 1);
            }
            return $status;
        } else {
            return false;
        }
    }

    //添加会员组
    public function group_add($data) {
        if (!is_array($data)) {
            return false;
        }
        $groupid = $this->add($data);
        if ($groupid !== false) {
            //更新附件状态
            if ($data['icon']) {
                //更新附件状态
                service("Attachment")->api_update('', 'member_group-' . $groupid, 1);
            }
            return $groupid;
        } else {
            return false;
        }
    }

    //生成会员组缓存
    public function Membergroup_cache() {
        $data = $this->select();
        $return = array();
        foreach ($data as $k => $v) {
            $return[$v['groupid']] = $v;
        }
        unset($data);
        F("Member_group", $return);
        return $return;
    }

    /**
     * 后台有更新则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("Member_group", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->Membergroup_cache();
    }

    //更新数据后更新缓存
    public function _after_update($data, $options) {
        parent::_after_update($data, $options);
        $this->Membergroup_cache();
    }

    //插入数据后更新缓存
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        $this->Membergroup_cache();
    }

}

?>
