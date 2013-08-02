<?php

/**
 * 前台会员中心Action Base
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class MemberbaseAction extends BaseAction {

    public $Member_config = array(); //会员模型相关配置

    protected function _initialize() {
        C("USER_AUTH_MODEL", "Member");
        parent::_initialize();
        $this->Member_config = F("Member_Config");
        //初始化当前登录用户信息
        $this->initUser();
        //所有以public_开头的方法都无需检测是否登陆
        if (substr(ACTION_NAME, 0, 7) != 'public_') {
            //登陆检测
            $this->check_member();
        }
        //============全局模板变量==============
        //会员组数组
        $this->assign("Member_group", F("Member_group"));
        //会员模型配置
        $this->assign("Member_config", $this->Member_config);
        //会员模型数组
        $this->assign("Model_member", F("Model_Member"));
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    public function error($message, $jumpUrl = '', $ajax = false) {
        parent::error($message, $jumpUrl, $ajax);
    }

    /**
     * 检测用户是否已经登陆 
     */
    final public function check_member() {

        if (GROUP_NAME == 'Member' && MODULE_NAME == 'Index' && in_array(ACTION_NAME, array('login', 'register', 'logout', 'connectregister'))) {
            return true;
        } else {
            if (AppframeAction::$Cache['uid']) {
                //禁止访问会员组
                if (AppframeAction::$Cache['User']['groupid'] == 1) {
                    service("Passport")->logoutLocal();
                    $this->error("您的会员组为禁止访问！", CONFIG_SITEURL);
                } else if (AppframeAction::$Cache['User']['groupid'] == 7) {//邮箱认证
                    service("Passport")->logoutLocal();
                    $this->error("您还没有进行邮箱认证！", CONFIG_SITEURL);
                }
                //锁定用户
                if (AppframeAction::$Cache['User']['islock'] == 1) {
                    service("Passport")->logoutLocal();
                    $this->error("您的帐号已经被锁定！", CONFIG_SITEURL);
                }
                return true;
            } else {
                service("Passport")->logoutLocal();
                $forward = isset($_REQUEST['forward']) ? $_REQUEST['forward'] : get_url();
                cookie("forward", $forward);
                $this->error("您的会话已过期，请重新登录。！", U("Member/Index/login"));
            }
        }
    }

    /**
     * 检查用户名
     */
    public function public_checkname_ajax() {
        $username = isset($_GET['username']) && trim($_GET['username']) ? trim($_GET['username']) : exit(0);
        if (service("Passport")->user_checkname($username) == 1) {
            exit('1');
        }
        exit('0');
    }

    /**
     * 检查邮箱
     */
    public function public_checkemail_ajax() {
        $email = isset($_GET['email']) && trim($_GET['email']) ? trim($_GET['email']) : exit(0);
        if (service("Passport")->user_checkemail($email) == 1) {
            exit("1");
        }
        exit('0');
    }

    /**
     * 检查昵称是否存在 
     */
    public function public_checknickname_ajax() {
        $nickname = isset($_GET['nickname']) && trim($_GET['nickname']) ? trim($_GET['nickname']) : exit(0);
        if (M(C("USER_AUTH_MODEL"))->where(array("nickname" => $nickname))->count()) {
            exit("0");
        }
        exit("1");
    }

    /**
     * 会员注册 
     * @param type $username 用户名
     * @param type $password 密码
     * @param type $email 邮箱
     * @param type $_data post
     * @return int 大于 0:返回用户 ID，表示用户注册成功
     *                              -1:用户名不合法
     *                              -2:包含不允许注册的词语
     *                              -3:用户名已经存在
     *                              -4:Email 格式有误
     *                              -5:Email 不允许注册
     *                              -6:该 Email 已经被注册
     *                              -7模型ID为空
     *                              -8用户注册成功，但添加模型资料失败
     */
    protected function registeradd($username, $password, $email, $_data) {
        $Model_Member = F("Model_Member");
        //模型ID
        $modelid = $_data['modelid'];
        if (!$modelid || !$Model_Member[$modelid]) {
            return -7;
        }
        require_cache(RUNTIME_PATH . 'content_input.class.php');
        $content_input = new content_input($modelid, $this);
        $inputinfo = $content_input->get($_data['info']);
        //取得模型内容
        $modedata = $inputinfo['model'];
        if (!$modedata) {
            $modedata = array();
        }
        //新注册用户积分
        $_data['point'] = $this->Member_config['defualtpoint'] ? $this->Member_config['defualtpoint'] : 0;
        //新会员注册默认赠送资金
        $_data['amount'] = $this->Member_config['defualtamount'] ? $this->Member_config['defualtamount'] : 0;
        //计算用户组
        $_data['groupid'] = $_data['groupid'] ? $_data['groupid'] : D("Member")->get_usergroup_bypoint($_data['point']);

        $userid = service("Passport")->user_register($username, $password, $email, $_data);

        if ($userid > 0) {
            //添加到相应模型
            $Model_Member = F("Model_Member");
            $tablename = $Model_Member[$modelid]['tablename'];
            $modedata = array_merge($modedata, array(
                "userid" => $userid
            ));
            //补充相应模型资料
            $status = M(ucwords($tablename))->add($modedata);
            if ($status) {
                return $userid;
            } else {
                return -8;
            }
        } else {
            return $userid;
        }
    }

    /**
     * 增加帐号绑定信息
     * @param type $uid 用户ID
     * @param type $app 应用名称
     * @param type $openid 标识
     */
    protected function connectAdd($uid, $app, $openid) {
        if (!$uid || !$app || !$openid) {
            return false;
        }
        $accesstoken = session("access_token");
        $expires = session("Connect_expires");
        $db = M("Connect");
        return $db->add(array(
                    "openid" => $openid,
                    "app" => $app,
                    "uid" => $uid,
                    "accesstoken" => $accesstoken,
                    "expires" => $expires,
        ));
    }

    /**
     * 删除帐号绑定信息
     * @param type $uid 用户ID
     * @param type $app 应用名称
     * @param type $openid 标识
     */
    protected function connectDel($uid, $app, $openid) {
        if (!$uid || !$app || !$openid) {
            return false;
        }
        $db = M("Connect");
        return $db->where(array(
                    "openid" => $openid,
                    "app" => $app,
                    "uid" => $uid
                ))->delete();
    }

}

?>
