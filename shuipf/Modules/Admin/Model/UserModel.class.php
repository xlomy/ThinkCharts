<?php

// 用户模型
class UserModel extends RelationModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('username', 'require', '用户名不能为空！'),
        array('nickname', 'require', '真实姓名不能为空！'),
        array('role_id', 'require', '帐号所属角色不能为空！'),
        array('password', 'require', '密码不能为空！', 0, 'regex', 1),
        //array('password', array(6,28), '密码长度太短！',1,'length'),
        array('email', 'email', '邮箱地址有误！'),
        array('username', '', '帐号名称已经存在！', 0, 'unique', 3),
        array('pwdconfirm', 'password', '两次输入的密码不一样！', 0, 'confirm'),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
    );
    //关联定义
    protected $_link = array(
        //和角色吧关联，一对一
        'User_Role' => array(
            "mapping_type" =>HAS_ONE,
            //关联表名
            "class_name" =>"Role_user",
            "foreign_key" =>"user_id",
        ),
    );

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
        if (is_int($identifier)) {
            $term['id'] = $identifier;
        } else {
            $term['username'] = $identifier;
        }
        $verify = genRandomString();
        $data['verify'] = $verify;
        $data['password'] = $this->encryption($identifier, $password, $verify);
        $up = $this->where($term)->save($data);
        if ($up !== false) {
            return true;
        }
        return false;
    }
    
    /**
     * 插入成功后的回调方法
     * @param type $data
     * @param type $options
     */
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        //添加一条记录到 Role_user 表
        M("Role_user")->add(array("role_id" => $data['role_id'], "user_id" => $data['id']));
    }
    
    /**
     * 删除成功后的回调方法
     * @param type $data
     * @param type $options
     */
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        M("Role_user")->where(array("user_id" => $data['id']))->delete();
    }

}

?>