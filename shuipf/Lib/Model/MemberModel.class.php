<?php

// 用户模型
class MemberModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('username', 'require', '用户名不能为空！'),
        array('password', 'require', '密码不能为空！', 0, 'regex', 1),
        array('email', 'email', '邮箱地址有误！'),
        array('username', '', '帐号名称已经存在！', 0, 'unique', 1),
        array('nickname', '', '该昵称已经存在！', 0, 'unique', 1),
        array('pwdconfirm', 'password', '两次输入的密码不一样！', 0, 'confirm'),
        //callback
        array('username', 'checkName', '用户名已经存在或不合法！', 0, 'callback',1),
        array('email', 'checkEmail', '邮箱已经存在或者不合法！', 0, 'callback', 1),
        array('groupid', 'checkGroupid', '该会员组不存在！', 0, 'callback'),
        array('modelid', 'checkModelid', '该会员模型不存在！', 0, 'callback'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('regdate', 'time', 1, 'function'),
        array('regip', 'get_client_ip', 1, 'function'),
    );

    //检查用户名
    public function checkName($name) {
        if (service("Passport")->user_checkname($name) > 0) {
            return true;
        }
        return false;
    }

    //检查邮箱
    public function checkEmail($email) {
        if (service("Passport")->user_checkemail($email) == 1) {
            return true;
        }
        return false;
    }

    //检查会有组
    public function checkGroupid($groupid) {
        $Member_group = F("Member_group");
        if (!$Member_group[$groupid]) {
            return false;
        }
        return true;
    }

    //检查会员模型
    public function checkModelid($modelid) {
        $Model_Member = F("Model_Member");
        if (!$Model_Member[$modelid]) {
            return false;
        }
        return true;
    }

    /**
     * 对明文密码，进行加密，返回加密后的密码
     * @param $identifier 为数字时，表示uid，其他为用户名
     * @param type $pass 明文密码，不能为空
     * @return type 返回加密后的密码
     */
    public function encryption($identifier, $pass, $verify = "") {
        $v = array();
        if (is_numeric($identifier)) {
            $v["id"] = $identifier;
        } else {
            $v["username"] = $identifier;
        }
        $pass = md5($pass . md5($verify));
        return $pass;
    }

    /**
     * 根据标识修改对应用户密码
     * @param type $identifier
     * @param type $password
     * @return type 
     */
    public function ChangePassword($identifier, $password) {
        if (empty($identifier) || empty($password)) {
            return false;
        }
        $term = array();
        if (is_numeric($identifier)) {
            $term['userid'] = $identifier;
        } else {
            $term['username'] = $identifier;
        }
        $verify = $this->where($term)->getField('verify');

        $data['password'] = $this->encryption($identifier, $password, $verify);

        $up = $this->where($term)->save($data);
        if ($up) {
            return true;
        }
        return false;
    }

    /**
     * 根据积分算出用户组
     * @param $point int 积分数 
     */
    public function get_usergroup_bypoint($point = 0) {
        $groupid = 2;
        if (empty($point)) {
            $member_setting = F("Member_Config");
            //新会员默认点数
            $point = $member_setting['defualtpoint'] ? $member_setting['defualtpoint'] : 0;
        }
        //获取会有组缓存
        $grouplist = F("Member_group");

        foreach ($grouplist as $k => $v) {
            $grouppointlist[$k] = $v['point'];
        }
        //对数组进行逆向排序
        arsort($grouppointlist);

        //如果超出用户组积分设置则为积分最高的用户组
        if ($point > max($grouppointlist)) {
            $groupid = key($grouppointlist);
        } else {
            foreach ($grouppointlist as $k => $v) {
                if ($point >= $v) {
                    $groupid = $tmp_k;
                    break;
                }
                $tmp_k = $k;
            }
        }
        return $groupid;
    }

}

?>