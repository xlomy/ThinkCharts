<?php

/* * 
 * 通行证服务
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class PassportService {

    protected $_config, $UCenter = FALSE;

    function __construct() {
        $this->_config = F("Member_Config");
        //是否开启UCenter
        if ((int) $this->_config['ucuse'] == 1) {
            $this->UCenterInit();
        }
    }

    /**
     * 初始化 UCenter
     * @return boolean  true
     */
    public function UCenterInit() {
        //连接 UCenter 的方式
        define("UC_CONNECT", $this->_config['uc_connect']);
        //UCenter 数据库主机
        define("UC_DBHOST", $this->_config['uc_dbhost']);
        //UCenter 数据库用户名
        define("UC_DBUSER", $this->_config['uc_dbuser']);
        //UCenter 数据库密码.
        define("UC_DBPW", $this->_config['uc_dbpw']);
        //UCenter 数据库名称
        define("UC_DBNAME", $this->_config['uc_dbname']);
        //UCenter 数据库字符集
        define("UC_DBCHARSET", $this->_config['uc_dbcharset']);
        //UCenter 数据库表前缀
        define("UC_DBTABLEPRE", $this->_config['uc_dbtablepre']);
        //与 UCenter 的通信密钥, 要与 UCenter 保持一致
        define("UC_KEY", $this->_config['uc_key']);
        //UCenter 服务端的 URL 地址
        define("UC_API", $this->_config['uc_api']);
        //UCenter 的 IP
        define("UC_IP", $this->_config['uc_ip']);
        //UCenter 的字符集
        define("UC_CHARSET", "utf-8");
        //当前应用的 ID
        define("UC_APPID", $this->_config['uc_appid']);

        define('UC_PPP', '20');
        $this->UCenter = TRUE;
        return require_cache(SITE_PATH . DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "uc_client" . DIRECTORY_SEPARATOR . "client.php");
    }

    /**
     * 用户注册
     * @param type $username 用户名
     * @param type $password 明文密码
     * @param type $email
     * @param type $_data 附加数据
     * @return int 大于 0:返回用户 ID，表示用户注册成功
     *                              -1:用户名不合法
     *                              -2:包含不允许注册的词语
     *                              -3:用户名已经存在
     *                              -4:Email 格式有误
     *                              -5:Email 不允许注册
     *                              -6:该 Email 已经被注册
     */
    public function user_register($username, $password, $email, $_data = array()) {
        //检查用户名
        $ckname = $this->user_checkname($username);
        if ($ckname < 1) {
            return $ckname;
        }
        //检查邮箱
        $ckemail = $this->user_checkemail($email);
        if ($ckemail < 1) {
            return $ckemail;
        }
        if ($this->UCenter) {
            $userid = uc_user_register($username, $password, $email);
            if ($userid > 0) {
                //保存到本地
                $Member = D("Member");
                $encrypt = genRandomString(6);
                $password = $Member->encryption(0, $password, $encrypt);
                $data = array(
                    "userid" => $userid,
                    "username" => $username,
                    "password" => $password,
                    "email" => $email,
                    "encrypt" => $encrypt,
                );
                $data = array_merge($_data, $data);
                $Member->add($data);
                return $userid;
            }
        } else {
            $Member = D("Member");
            $encrypt = genRandomString(6);
            $password = $Member->encryption(0, $password, $encrypt);
            $data = array(
                "username" => $username,
                "password" => $password,
                "email" => $email,
                "encrypt" => $encrypt,
            );
            $data = array_merge($_data, $data);
            $userid = $Member->add($data);
            if ($userid) {
                return $userid;
            }
        }
        return 0;
    }

    /**
     * 更新用户基本资料
     * @param type $username 用户名
     * @param type $oldpw 旧密码
     * @param type $newpw 新密码，如不修改为空
     * @param type $email Email，如不修改为空
     * @param type $ignoreoldpw 是否忽略旧密码
     * @param type $_data 附加数据
     * @return int 1:更新成功
     *                      0:没有做任何修改
     *                     -1:旧密码不正确
     *                     -4:Email 格式有误
     *                     -5:Email 不允许注册
     *                     -6:该 Email 已经被注册
     *                     -7:没有做任何修改
     *                     -8:该用户受保护无权限更改
     */
    public function user_edit($username, $oldpw, $newpw, $email, $ignoreoldpw = 0, $_data = array()) {
        $Member = D("Member");
        $data = array();
        $data = array_merge($_data, $data);
        unset($data['username']);
        if ($this->UCenter) {
            $status = uc_user_edit($username, $oldpw, $newpw, $email, $ignoreoldpw);
            if ($status < 0) {
                return $status;
            }
        }
        //验证旧密码是否正确
        if ($ignoreoldpw == 0) {
            $info = $Member->where(array("username" => $username))->find();
            $pas = $Member->encryption(0, $oldpw, $info['encrypt']);
            if ($pas != $info['password']) {
                return -1;
            }
        }

        if ($newpw) {
            //随机密码
            $encrypt = genRandomString(6);
            //新密码
            $password = $Member->encryption(0, $newpw, $encrypt);
            $data['password'] = $password;
            $data['encrypt'] = $encrypt;
        } else {
            unset($data['password']);
            unset($data['encrypt']);
        }
        if ($email) {
            $data['email'] = $email;
        } else {
            unset($data['email']);
        }
        if ($Member->where(array("username" => $username))->save($data)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     *  删除用户
     * @param type $uid 用户名
     * @return int 1:成功
     *                      0:失败
     */
    public function user_delete($uid) {
        $modelid = M("Member")->where(array("userid" => $uid))->getField("modelid");
        if (!$modelid) {
            return 0;
        }
        $Model_Member = F("Model_Member");
        $tablename = ucwords($Model_Member[$modelid]['tablename']);
        if ($this->UCenter) {
            if (!uc_user_delete($uid)) {
                return 0;
            }
        }
        //删除本地用户数据开始
        if (M("Member")->where(array("userid" => $uid))->delete()) {
            M($tablename)->where(array("userid" => $uid))->delete();
            //删除connect
            M("Connect")->where(array("uid" => $uid))->delete();
            return 1;
        }
        return 0;
    }

    /**
     * 删除用户头像
     * @param type $uid 用户名
     * @return int 1:成功
     *                      0:失败
     */
    public function user_deleteavatar($uid) {
        if ($this->UCenter) {
            return uc_user_deleteavatar($uid);
        } else {
            $dr = C("UPLOADFILEPATH") . "avatar/" . $uid . '/';

            if (defined("CONFIG_FTPSTATUS") && CONFIG_FTPSTATUS) {
                $Attachment = service("Attachment");
                // 远程存放地址
                $remote = CONFIG_FTPUPPAT . str_replace(SITE_PATH . "/", "", $dr);
                $Attachment->FTPrmdir($remote, true);
            } else {
                if (is_dir($dr) == false) {
                    return 0;
                }
                import("Dir");
                $Dir = new Dir();
                $Dir->delDir($dr);
            }
            M("Member")->where(array("userid" => $uid))->save(array("userpic" => ""));
            return 1;
        }
    }

    /**
     * 检查 Email 地址
     * @param type $email 邮箱地址
     * @return int 1:成功
     *                      -4:Email 格式有误
     *                      -5:Email 不允许注册
     *                      -6:该 Email 已经被注册
     */
    public function user_checkemail($email) {
        if (strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)) {
            if ($this->UCenter) {
                return uc_user_checkemail($email);
            } else {
                $find = M("Member")->where(array("email" => $email))->find();
                if ($find) {
                    return -6;
                }
                return 1;
            }
        }
        return -4;
    }

    /**
     * 检查用户名
     * @param type $username 用户名
     * @return int 1:成功
     *                      -1:用户名不合法
     *                      -2:包含要允许注册的词语
     *                      -3:用户名已经存在
     */
    public function user_checkname($username) {
        $guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
        if (!preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", $username)) {
            if ($this->UCenter) {
                return uc_user_checkname($username);
            } else {
                $find = M("Member")->where(array("username" => $username))->find();
                if ($find) {
                    return -3;
                }
                return 1;
            }
        }
        return -1;
    }

    /**
     * 修改头像
     * @param type $uid 用户 ID
     * @param type $type 头像类型
     *                                       real:真实头像
     *                                       virtual:(默认值) 虚拟头像
     * @param type $returnhtml 是否返回 HTML 代码
     *                                                     1:(默认值) 是，返回设置头像的 HTML 代码
     *                                                     0:否，返回设置头像的 Flash 调用数组
     * @return string:返回设置头像的 HTML 代码
     *                array:返回设置头像的 Flash 调用数组
     */
    public function user_avatar($uid, $type = 'virtual', $returnhtml = 1) {
        if ($this->UCenter) {
            return uc_avatar($uid, $type, $returnhtml);
        } else {
            $auth_data = urlencode(authcode($uid, ''));
            $upurl = base64_encode(CONFIG_SITEURL . 'index.php?g=Member&m=Index&a=uploadavatar&auth_data=' . $auth_data);
            $html = '<script type="text/javascript">
    var flashvars = {
        "upurl": "' . $upurl . '&callback=return_avatar&"
    };
    var params = {
        "align": "middle",
        "play": "true",
        "loop": "false",
        "scale": "showall",
        "wmode": "window",
        "devicefont": "true",
        "id": "Main",
        "bgcolor": "#ffffff",
        "name": "Main",
        "allowscriptaccess": "always"
    };
    var attributes = {

    };
    swfobject.embedSWF("' . CONFIG_SITEURL . 'statics/images/main.swf", "myContent", "490", "434", "9.0.0", "' . CONFIG_SITEURL . 'statics/images/expressInstall.swf", flashvars, params, attributes);

    function return_avatar(data) {
        if (data == 1) {
            window.location.reload();
        } else {
            alert("failure");
        }
    }
</script>';
            return $html;
        }
    }

    /**
     * 获取用户头像 
     * @param type $uid 用户ID
     * @param int $format 头像规格，默认参数90，支持 180,90,45,30
     * @param type $dbs 该参数为true时，表示使用查询数据库的方式，取得完整的头像地址。默认false
     * @return type 返回头像地址
     */
    public function user_getavatar($uid, $format = 90, $dbs = false) {
        //该参数为true时，表示使用查询数据库的方式，取得完整的头像地址。
        //比如QQ登陆，使用QQ头像，此时可以使用该种方式
        if ($dbs) {
            $user_getavatar_cache = S("user_getavatar_$uid");
            if ($user_getavatar_cache) {
                return $user_getavatar_cache;
            } else {
                $Member = M("Member");
                $userpic = $Member->where(array("userid" => $uid))->getField("userpic");
                if ($userpic) {
                    S("user_getavatar_$uid", $userpic, 3600);
                } else {
                    $userpic = CONFIG_SITEURL . "statics/images/member/nophoto.gif";
                }
                return $userpic;
            }
        }

        if ($this->UCenter) {
            //头像规格
            $avatar = array(
                180 => "big",
                90 => "middle",
                45 => "small",
                30 => "small",
            );
            $format = in_array($format, $avatar) ? $format : 90;
            $picurl = $this->_config['uc_api'] . "/avatar.php?uid=" . $uid . "&size=" . $avatar[$format];
        } else {
            //头像规格
            $avatar = array(
                180 => "180x180.jpg",
                90 => "90x90.jpg",
                45 => "45x45.jpg",
                30 => "30x30.jpg",
            );
            $format = in_array($format, $avatar) ? $format : 90;
            $userpic = "avatar/{$uid}/" . $avatar[$format];
            if ($userpic) {
                $picurl = CONFIG_SITEFILEURL . $userpic;
            } else {
                $picurl = CONFIG_SITEURL . "statics/images/member/nophoto.gif";
            }
        }
        return $picurl;
    }

    /**
     * 用户积分变更
     * @param type $uid 数字为用户ID，其他为用户名
     * @param type $integral 正数增加积分，负数扣除积分
     * @return int 成功返回当前积分数，失败返回false，-1 表示当前积分不够扣除
     */
    public function user_integral($uid, $integral) {
        $map = array();
        if (is_numeric($uid)) {
            $map['id'] = $uid;
        } else {
            $map['username'] = $uid;
        }
        $member = D("Member");
        $info = $member->where($map)->find();
        $point = $info['point'] + $integral;
        if ($point < 0) {
            return -1;
        }
        //计算会员组
        $groupid = $member->get_usergroup_bypoint((int) $point);
        //更新
        if ($member->where($map)->save(array("point" => (int) $point, "groupid" => $groupid))) {
            return $point;
        }
        return false;
    }

    /**
     * 检验用户是否已经登陆
     */
    public function isLogged() {
        return $this->getCookieUid();
    }

    /**
     * 前台会员信息
     * 根据提示符(username)和未加密的密码(密码为空时不参与验证)获取本地用户信息，前后台公用方法
     * @param type $identifier 为数字时，表示uid，其他为用户名
     * @param type $password 
     * @return 成功返回用户信息array()，否则返回布尔值false
     */
    public function getLocalUser($identifier, $password = null) {
        if (empty($identifier)) {
            return false;
        }

        $map = array();
        if (is_numeric($identifier) && gettype($identifier) == "integer") {
            $map['id'] = $identifier;
            $isuid = 1;
        } else {
            $map['username'] = $identifier;
            $isuid = 0;
        }
        $UserMode = D(C("USER_AUTH_MODEL"));
        $user = $UserMode->where($map)->find();
        if (!$user) {
            return false;
        }
        if ($password) {
            if ($this->UCenter) {
                $user_login = uc_user_login($identifier, $password, $isuid);
                if ($user_login[0] < 1) {
                    return false;
                }
            } else {
                //验证本地密码是否正确
                if ($UserMode->encryption($identifier, $password, $user['encrypt']) != $user['password']) {
                    return false;
                }
            }
        }

        //去除敏感信息
        unset($user['password']);
        unset($user['verify']);
        return $user;
    }

    /**
     * 使用本地账号登陆 (密码为null时不参与验证)
     * @param type $identifier 用户标识，用户uid或者用户名
     * @param type $password 用户密码，未加密，如果为空，不参与验证
     * @param type $is_remember_me cookie有效期
     * return 返回状态，大于 0:返回用户 ID，表示用户登录成功
     *                                     -1:用户不存在，或者被删除
     *                                     -2:密码错
     *                                     -3会员注册登陆状态失败
     */
    public function loginLocal($identifier, $password = null, $is_remember_me = 3600) {
        $db = D("Member");
        if ($this->UCenter) {
            $user = uc_user_login($identifier, $password);
            if ($user[0] > 0) {
                $userid = $user[0];
                $username = $user[1];
                $ucpassword = $user[2];
                $ucemail = $user[3];
                $map = array();
                $map['userid'] = $userid;
                $map['username'] = $username;
                //取得本地相应用户
                $userinfo = $db->where($map)->find();
                //检查是否存在该用户信息
                if (!$userinfo) {
                    //UC中有该用户，本地没有时，创建本地会员数据
                    $data = array();
                    $data['userid'] = $userid;
                    $data['username'] = $username;
                    $data['nickname'] = $username;
                    $data['encrypt'] = genRandomString(6); //随机密码
                    $data['password'] = $db->encryption(0, $ucpassword, $data['encrypt']);
                    $data['email'] = $ucemail;
                    $data['regdate'] = time();
                    $data['regip'] = get_client_ip();
                    $data['modelid'] = $this->_config['defaultmodelid'];
                    $data['point'] = $this->_config['defualtpoint'];
                    $data['amount'] = $this->_config['defualtamount'];
                    $data['groupid'] = $db->get_usergroup_bypoint($this->_config['defualtpoint']);
                    $data['checked'] = 1;
                    $data['lastdate'] = time();
                    $data['loginnum'] = 1;
                    $data['lastip'] = get_client_ip();
                    $db->add($data);
                    $Model_Member = F("Model_Member");
                    $tablename = $Model_Member[$data['modelid']]['tablename'];
                    M(ucwords($tablename))->add(array("userid" => $userid));
                    $userinfo = $data;
                } else {
                    //更新密码
                    $encrypt = genRandomString(6); //随机密码
                    $pw = $db->encryption(0, $ucpassword, $encrypt);
                    $db->where(array("userid" => $userid))->save(array("encrypt" => $encrypt, "password" => $pw, "lastdate" => time(), "lastip" => get_client_ip(), 'loginnum' => $userinfo['loginnum'] + 1));
                    $userinfo['password'] = $pw;
                    $userinfo['encrypt'] = $encrypt;
                }
                if ($this->registerLogin($userinfo, $is_remember_me)) {
                    //登陆成功
                    return $userinfo['userid'];
                } else {
                    //会员注册登陆状态失败
                    return -3;
                }
            } else {
                //登陆失败
                return $user[0];
            }
        } else {
            $map = array();
            if (is_int($identifier)) {
                $map['userid'] = $identifier;
            } else {
                $map['username'] = $identifier;
            }
            $userinfo = $db->where($map)->find();
            if (!$userinfo) {
                //没有该用户
                return -1;
            }
            $encrypt = $userinfo["encrypt"];
            $password = $db->encryption($identifier, $password, $encrypt);
            if ($password == $userinfo['password']) {
                if ($this->registerLogin($userinfo, $is_remember_me)) {
                    //修改登陆时间，和登陆IP
                    $db->where($map)->save(array(
                        "lastdate" => time(),
                        "lastip" => get_client_ip(),
                        "loginnum" => $userinfo['loginnum'] + 1,
                    ));
                    //登陆成功
                    return $userinfo['userid'];
                } else {
                    //会员注册登陆状态失败
                    return -3;
                }
            } else {
                //密码错误
                return -2;
            }
        }
    }

    /**
     * 注册用户的登陆状态 (即: 注册cookie + 注册session + 记录登陆信息)
     * @param array $user 用户相信信息 uid , username
     * @param type $is_remeber_me 有效期
     * @return type 成功返回布尔值
     */
    public function registerLogin(array $user, $is_remeber_me = 604800) {
        //用户ID
        SiteCookie("mid", $user['userid'], $is_remeber_me);
        //用户名
        SiteCookie("username", $user['username'], $is_remeber_me);
        //用户组
        SiteCookie("groupid", $user['groupid'], $is_remeber_me);

        SiteCookie("auth", $user['password'], $is_remeber_me);
        if ($this->UCenter) {
            echo uc_user_synlogin($user['userid']);
        }
        //记录登陆
        $this->recordLogin($user['userid']);
        return true;
    }

    /**
     * 注销登陆
     */
    public function logoutLocal() {
        // 注销cookie
        cookie("mid", null);
        cookie("username", null);
        cookie("auth", null);
        cookie("groupid", null);
        if ($this->UCenter) {
            echo uc_user_synlogout();
        }
        return true;
    }

    /**
     * 获取cookie中记录的用户ID
     * @return type 成功返回用户ID，失败返回false
     */
    public function getCookieUid() {
        //用户ID
        $mid = SiteCookie("mid");
        $username = SiteCookie("username");
        $auth = SiteCookie("auth");
        if (!$username || !$mid) {
            return false;
        }
        $info = M("Member")->where(array("userid" => $mid, 'username' => $username))->find();
        if ($info['password'] == $auth) {
            return $info;
        }
        return false;
    }

    /**
     * 记录登陆信息
     * @param type $uid 用户ID
     */
    public function recordLogin($uid) {
        
    }

}

?>
