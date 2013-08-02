<?php

/**
 * 会员中心
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class IndexAction extends MemberbaseAction {

    function _initialize() {
        parent::_initialize();
        import('Form');
        $group = F("Member_group");
        $Model_Member = F("Model_Member");
        $this->assign("Member_group", $group);
        $this->assign("Model_Member", $Model_Member);
    }

    /**
     * 会员中心首页 
     */
    public function index() {
        $this->display();
    }

    /**
     * 帐号管理 
     */
    public function account_manage() {
        $userid = (int) AppframeAction::$Cache['uid'];
        $Member = M("Member");
        $data = $Member->where(array("userid" => $userid))->find();
        if (!$data) {
            service("Passport")->logoutLocal();
            $this->error("请先登陆！", U("Index/login"));
        }
        require_once RUNTIME_PATH . 'content_output.class.php';
        $modelid = AppframeAction::$Cache['User']['modelid'];
        $Model_Member = F("Model_Member");
        $tablename = $Model_Member[$modelid]['tablename'];
        //相应会员模型数据
        $modeldata = M(ucwords($tablename))->where(array("userid" => $userid))->find();

        $content_output = new content_output($modelid);
        $output_data = $content_output->get($modeldata);
        $Model_field = F("Model_field_" . $modelid);

        $this->assign("output_data", $output_data);
        $this->assign("Model_field", $Model_field);
        $this->assign($data);
        $this->display();
    }

    /**
     * 收藏 
     */
    public function favorite() {
        $this->display();
    }

    /**
     * 修改头像 
     */
    public function account_manage_avatar() {
        //取得头像上传代码
        $html = service("Passport")->user_avatar(AppframeAction::$Cache['uid']);
        $this->assign("avatarhtml", $html);
        $this->display();
    }

    /**
     * 上传头像 
     */
    public function uploadavatar() {
        $auth_data = authcode($_GET['auth_data'], "DECODE");
        if ($auth_data != AppframeAction::$Cache['uid']) {
            exit("0");
        }
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            //flashpost
            $avatardata = $GLOBALS['HTTP_RAW_POST_DATA'];
        }
        $dir = C("UPLOADFILEPATH") . "/avatar/" . AppframeAction::$Cache['uid'] . '/';
        //创建图片存储文件夹
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        //存储flashpost图片压缩包 member/avatar/用户id/
        $filename = $dir . AppframeAction::$Cache['uid'] . ".zip";
        file_put_contents($filename, $avatardata);
        import("Pclzip");
        $archive = new PclZip($filename);
        if ($archive->extract(PCLZIP_OPT_PATH, $dir) == 0) {
            die("Error : " . $archive->errorInfo(true));
        }
        //判断文件安全，删除压缩包和非jpg图片
        $avatararr = array('180x180.jpg', '30x30.jpg', '45x45.jpg', '90x90.jpg');
        if (defined("CONFIG_FTPSTATUS") && CONFIG_FTPSTATUS) {
            import("UploadFile");
            import("Dir");
            $Dir = new Dir();
            $UploadFile = new UploadFile();
        }
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file !== '.' && $file !== '..') {
                    if (!in_array($file, $avatararr)) {
                        @unlink($dir . $file);
                    } else {
                        $info = @getimagesize($dir . $file);
                        if (!$info || $info[2] != 2) {
                            @unlink($dir . $file);
                        }
                    }
                    if (defined("CONFIG_FTPSTATUS") && CONFIG_FTPSTATUS) {
                        //如果有开启FTP，则上传到FTP上
                        $UploadFile->FTPuplode($dir . $file, $dir . $file);
                    }
                }
            }
            closedir($handle);
        }
        //如果是上传FTP，删除
        if (defined("CONFIG_FTPSTATUS") && CONFIG_FTPSTATUS) {
            $Dir->delDir($dir);
        }
        //保存头像地址到数据库，默认保存90x90.jpg
        //20121214 废除，userpic用于保存非本地头像，例如QQ登陆后的第三方头像。
        //M("Member")->where(array("userid" => AppframeAction::$Cache['uid']))->save(array("userpic" => "avatar/" . AppframeAction::$Cache['uid'] . '/90x90.jpg'));
        exit('1');
    }

    /**
     * 修改个人信息
     */
    public function account_manage_info() {
        if (IS_POST) {
            $Member = D("Member");
            //判断昵称是否修改
            if (AppframeAction::$Cache['User']['nickname'] == $_POST['nickname']) {
                unset($_POST['nickname']);
            }
            $data = $Member->create();
            if ($data) {
                if (isset($data['nickname'])) {
                    $Member->where(array("userid" => AppframeAction::$Cache['uid']))->save(array("nickname" => $data['nickname']));
                }
                $modelid = AppframeAction::$Cache['User']['modelid'];
                require RUNTIME_PATH . 'content_input.class.php';
                $content_input = new content_input($modelid, $this);
                $inputinfo = $content_input->get($_POST['info']);
                //取得模型内容
                $modedata = $inputinfo['model'];
                $Model_Member = F("Model_Member");
                $tablename = $Model_Member[$modelid]['tablename'];
                $modedata = array_merge($modedata, array(
                    "userid" => AppframeAction::$Cache['uid']
                        ));
                M(ucwords($tablename))->save($modedata);
                $this->success("更新成功！", U("Index/account_manage_info"));
            } else {
                $this->error($Member->getError());
            }
        } else {
            $data = AppframeAction::$Cache['User'];
            $modelid = $data['modelid'];
            $Model_Member = F("Model_Member");
            $tablename = $Model_Member[$modelid]['tablename'];
            //相应会员模型数据
            $modeldata = M(ucwords($tablename))->where(array("userid" => AppframeAction::$Cache['uid']))->find();
            //引入输入表单处理类
            require RUNTIME_PATH . 'content_form.class.php';
            $content_form = new content_form($modelid);
            $data['modelid'] = $modelid;
            //字段内容
            $forminfos = $content_form->get($modeldata);
            //js提示
            $formValidator = $content_form->formValidator;

            $this->assign("forminfos", $forminfos);
            $this->assign("formValidator", $formValidator);
            $this->assign("data", $data);
            $this->display();
        }
    }

    /**
     * 修改邮箱/密码 
     */
    public function account_manage_password() {
        if (IS_POST) {
            $Member = D("Member");
            //原密码
            $password = $_POST['password'];
            if (!$this->_post('newpassword')) {
                unset($_POST['password']);
            } else {
                $_POST['password'] = $this->_post('newpassword');
            }
            if ($this->_post("email") == AppframeAction::$Cache['User']['email']) {
                unset($_POST['email']);
            }
            $data = $Member->create();
            if ($data) {
                $edit = service("Passport")->user_edit(AppframeAction::$Cache['username'], $password, $data['password'], $data['email']);
                if ($edit == 1) {
                    $this->success("修改成功！", U("Index/account_manage_password"));
                } else {
                    if ($edit == -1) {
                        $this->error("原密码不正确！", U("Index/account_manage_password"));
                    }
                    $this->error("修改失败！", U("Index/account_manage_password"));
                }
            } else {
                $this->error($Member->getError());
            }
        } else {
            $this->display();
        }
    }

    /**
     * 会员登陆界面 
     */
    public function login() {
        if (AppframeAction::$Cache['uid']) {
            $this->success("您已经是登陆状态！", U("Index/index"));
        } else {
            $this->assign("forward", $_REQUEST['forward'] ? $_REQUEST['forward'] : cookie("forward"));
            $this->assign("Connectqq", ($this->Member_config['qq_akey'] && $this->Member_config['qq_skey']) ? true : false);
            $this->assign("Connectsina_weibo", ($this->Member_config['sinawb_akey'] && $this->Member_config['sinawb_skey']) ? true : false);
            $this->display("Public:login");
        }
    }

    /**
     * 退出会员登陆 
     */
    public function logout() {
        service("Passport")->logoutLocal();
        session("Connect_openid", NULL);
        session("Connect_app", NULL);
        $this->success("退出成功！", U("Member/Index/login"));
    }

    /**
     * 会员注册 
     */
    public function register() {
        import('Form');
        C("TOKEN_ON", false);
        //允许新会员注册
        if (!$this->Member_config['allowregister']) {
            $this->error("系统不允许新会员注册！");
        }
        if (IS_POST) {
            $code = $this->_post("code");
            $username = $this->_post("username");
            $password = $this->_post("password");
            $email = $this->_post("email");
            //验证码开始验证
            if (!$this->verify($code)) {
                $this->error("验证码错误，请重新输入！");
            }
            $Member = D("Member");
            $data = $Member->create();
            if ($data) {
                //模型选择,如果是关闭模型选择，直接赋值默认模型
                if( (int)$this->Member_config['choosemodel'] ){
                    if(!isset($data['modelid']) || empty($data['modelid'])){
                        $data['modelid'] = (int)$this->Member_config['defaultmodelid'];
                    }else{
                        //检查模型id是否合法
                        $Model_Member = F("Model_Member");
                        if(!isset($Model_Member[$data['modelid']])){
                            $data['modelid'] = (int)$this->Member_config['defaultmodelid'];
                        }
                    }
                }else{
                    $data['modelid'] = (int)$this->Member_config['defaultmodelid'];
                }
                //新会员注册需要邮件验证
                if ($this->Member_config['enablemailcheck']) {
                    $data['groupid'] = 7;
                    $data['checked'] = 1;
                } else {
                    //新会员注册需要管理员审核
                    if ($this->Member_config['registerverify']) {
                        $data['checked'] = 0;
                    } else {
                        $data['checked'] = 1;
                    }
                }
                $data = array_merge($_POST, $data);
                $status = $this->registeradd($username, $password, $email, $data);
                if ($status > 0) {
                    if ($this->Member_config['enablemailcheck']) {
                        //发送邮件
                        $code = urlencode(authcode($status, ''));
                        $url = CONFIG_SITEURL . "index.php?g=member&c=index&a=public_verifyemail&code=$code";
                        $message = $this->Member_config['registerverifymessage'];
                        $message = str_replace(array('{$click}', '{$url}'), array('<a href="' . $url . '">请点击</a>', $url), $message);
                        SendMail($data['email'], "注册会员验证邮件", $message);
                        $this->success("邮件已经发送到你注册邮箱，根据邮件内容完成验证操作！", CONFIG_SITEURL);
                        exit;
                    } else {
                        if (!$data['checked']) {
                            $this->success("会员注册成功，但需要管理员审核通过！", CONFIG_SITEURL);
                            exit;
                        }
                    }
                    $this->success("会员注册成功！", U("Index/login"));
                    exit;
                } else {
                    if ($status == -8) {
                        $error = '用户注册成功，但附加资料写入失败，你可以登陆进行资料补填！';
                        $this->error($error, U("Index/login"));
                    }
                    switch ($status) {
                        case -1:
                            $error = '用户名不合法！';
                            break;
                        case -2:
                            $error = '包含不允许注册的词语！';
                            break;
                        case -3:
                            $error = '用户名已经存在！';
                            break;
                        case -4:
                            $error = 'Email 格式有误！';
                            break;
                        case -5:
                            $error = 'Email 不允许注册！';
                            break;
                        case -6:
                            $error = '该 Email 已经被注册！';
                            break;
                        case -7:
                            $error = '模型ID为空！';
                            break;
                        default:
                            $error = '注册会员失败！';
                            break;
                    }
                }
                $this->error($error);
            } else {
                $this->error($Member->getError());
            }
        } else {
            if (AppframeAction::$Cache['uid']) {
                $this->success("您已经是登陆状态！", U("Index/index"));
                exit;
            }
            $choosemodel = $this->Member_config['choosemodel'];
            $defaultmodelid = $this->Member_config['defaultmodelid'];
            $showregprotocol = $this->Member_config['showregprotocol'];
            //是否允许用户自己选择注册模型
            if ($choosemodel) {
                $modelid = $this->_get("modelid");
            }
            $modelid = $modelid ? $modelid : $defaultmodelid;
            //模型缓存
            $Model_Member = F("Model_Member");
            if (!$Model_Member[$modelid]) {
                $this->error("该会员模型不存在！");
            }
            require_cache(RUNTIME_PATH . 'content_form.class.php');
            //实例化表单类 传入 模型ID 栏目ID 栏目数组
            $content_form = new content_form($modelid);
            //生成对应字段的输入表单
            $forminfos = $content_form->get();
            //生成对应的JS提示等
            $formValidator = $content_form->formValidator;

            $this->assign('showregprotocol', $showregprotocol);
            $this->assign("protocol", $this->Member_config['regprotocol']);
            $this->assign("forminfos", $forminfos);
            $this->assign("formValidator", $formValidator);
            $this->assign("choosemodel", $this->Member_config['choosemodel']);
            $this->assign("modelid", $modelid);
            $this->assign("Model_Member", $Model_Member);
            $this->assign("Connectqq", ($this->Member_config['qq_akey'] && $this->Member_config['qq_skey']) ? true : false);
            $this->assign("Connectsina_weibo", ($this->Member_config['sinawb_akey'] && $this->Member_config['sinawb_skey']) ? true : false);
            $this->display("Public:register");
        }
    }

    /**
     * connect登陆注册 
     */
    public function connectregister() {
        import('Form');
        C("TOKEN_ON", false);
        $openid = session("Connect_openid");
        $app = session("Connect_app");
        if (!$openid || !$app) {
            $this->error("请授权后再操作！");
        }
        //允许新会员注册
        if (!$this->Member_config['allowregister']) {
            $this->error("系统不允许新会员注册！", U("Index/public_binding"));
        }
        if (AppframeAction::$Cache['uid']) {
            $this->success("您已经是登陆状态！", U("Index/index"));
            exit;
        }
        if (IS_POST) {
            $code = $this->_post("code");
            $username = $this->_post("username");
            $password = $this->_post("password");
            $email = $this->_post("email");
            //验证码开始验证
            if (!$this->verify($code)) {
                $this->error("验证码错误，请重新输入！");
            }
            $Member = D("Member");
            $data = $Member->create();
            if ($data) {
                //新会员注册需要管理员审核
                if ($this->Member_config['registerverify']) {
                    $data['checked'] = 0;
                } else {
                    $data['checked'] = 1;
                }
                $data = array_merge($_POST, $data);
                $status = $this->registeradd($username, $password, $email, $data);
                if ($status > 0 || $status == -8) {
                    $this->connectAdd($status, $app, $openid);
                    $Member = M("Member");
                    $info = $Member->where(array("userid" => $status))->find();
                    if (!$data['checked']) {
                        $this->success("会员注册成功，但需要管理员审核通过！", CONFIG_SITEURL);
                        exit;
                    }
                    service("Passport")->registerLogin($info);
                    $this->success("会员注册成功！", U("Index/index"));
                    exit;
                } else {
                    switch ($status) {
                        case -1:
                            $error = '用户名不合法！';
                            break;
                        case -2:
                            $error = '包含不允许注册的词语！';
                            break;
                        case -3:
                            $error = '用户名已经存在！';
                            break;
                        case -4:
                            $error = 'Email 格式有误！';
                            break;
                        case -5:
                            $error = 'Email 不允许注册！';
                            break;
                        case -6:
                            $error = '该 Email 已经被注册！';
                            break;
                        case -7:
                            $error = '模型ID为空！';
                            break;
                        default:
                            $error = '注册会员失败！';
                            break;
                    }
                }
                $this->error($error);
            } else {
                $this->error($Member->getError());
            }
        } else {
            $choosemodel = $this->Member_config['choosemodel'];
            $defaultmodelid = $this->Member_config['defaultmodelid'];
            $showregprotocol = $this->Member_config['showregprotocol'];
            $appid = $this->Member_config['qq_akey'];
            //是否允许用户自己选择注册模型
            if ($choosemodel) {
                $modelid = $this->_get("modelid");
            }
            $modelid = $modelid ? $modelid : $defaultmodelid;
            //模型缓存
            $Model_Member = F("Model_Member");
            if (!$Model_Member[$modelid]) {
                $this->error("该会员模型不存在！");
            }
            require_cache(RUNTIME_PATH . 'content_form.class.php');
            //实例化表单类 传入 模型ID 栏目ID 栏目数组
            $content_form = new content_form($modelid);
            //生成对应字段的输入表单
            $forminfos = $content_form->get();
            //生成对应的JS提示等
            $formValidator = $content_form->formValidator;

            import('Util.Curl', APP_PATH . 'Lib');
            $curl = new Curl();

            $connect = array();
            //授权的相关信息
            switch (session("Connect_app")) {
                case "qq":
                    $connect['name'] = "QQ授权登陆";
                    //取得授权用户基本信息
                    $sUrl = "https://graph.qq.com/user/get_user_info?";
                    $aGetParam = array(
                        "access_token" => session("access_token"),
                        "oauth_consumer_key" => $appid,
                        "openid" => session("Connect_openid"),
                        "format" => "json"
                    );
                    $user_info = $curl->get($sUrl . http_build_query($aGetParam));
                    //把json数据转换为数组
                    $user_info = json_decode($user_info, true);
                    $connect['userinfo'] = $user_info;
                    $connect['userinfo']['name'] = $user_info['nickname'];
                    break;
                case "sina_weibo":
                    $connect['name'] = "新浪微博授权登陆";
                    //取得授权用户基本信息
                    $sUrl = "https://api.weibo.com/2/users/show.json?";
                    $aGetParam = array(
                        "access_token" => session("access_token"),
                        "uid" => session("Connect_openid"),
                    );
                    $user_info = $curl->get($sUrl . http_build_query($aGetParam));
                    //把json数据转换为数组
                    $user_info = json_decode($user_info, true);
                    $connect['userinfo'] = $user_info;
                    break;
                default:
                    break;
            }

            $this->assign('showregprotocol', $showregprotocol);
            $this->assign("protocol", $this->Member_config['regprotocol']);
            $this->assign("forminfos", $forminfos);
            $this->assign("formValidator", $formValidator);
            $this->assign("choosemodel", $this->Member_config['choosemodel']);
            $this->assign("modelid", $modelid);
            $this->assign("Model_Member", $Model_Member);
            $this->assign("connect", $connect);
            $this->display("Public:connectregister");
        }
    }

    //授权登陆成功后进行已有帐号绑定
    public function public_binding($loginName = "", $password = "") {
        $username = $loginName ? $loginName : $_POST['username'];
        $password = $password ? $password : $_POST['password'];
        //登陆有效时间
        $cookietime = (int) $this->_post("cookietime");
        //授权成功后返回的授权标识
        $openid = session("Connect_openid");
        $access_token = session("access_token");
        $app = session("Connect_app");
        $appid = $this->Member_config['qq_akey'];

        if (!$openid || !$access_token) {
            $this->error("请先授权！");
        }

        if (IS_POST) {
            //验证码
            $code = $this->_post("code");
            if (!$this->verify($code)) {
                $this->error("验证码错误，请重新输入！");
            }

            if (!$username || !$password) {
                $this->error("帐号密码不能为空！");
            }
            $userinfo = service("Passport")->getLocalUser($username, $password);

            if ($userinfo == false) {
                $this->error("帐号密码错误请重新输入！");
            }

            //待审核
            if ($userinfo['checked'] == 0) {
                $this->error("该帐号还未审核通过，暂无法绑定！");
            }

            //检查该用户是否已经绑定过
            $uid = M("Connect")->where(array("openid" => $openid, "app" => $app))->getField("uid");
            if ($uid) {
                $this->error("该帐号已经绑定过，无法重新绑定！");
            }
            //执行绑定
            $status = $this->connectAdd($userinfo['userid'], $app, $openid);

            if ($status !== false) {
                service("Passport")->loginLocal($username, $password, $cookietime ? $cookietime : 86400);
                $this->success("帐号绑定成功！", U("Member/Index/index"));
            } else {
                $this->error("帐号绑定失败！");
            }
        } else {

            $this->display("Public:binding");
        }
    }

    /**
     * 会员登陆验证 
     */
    public function public_loginverify() {
        //用户名
        $username = (string) $this->_post("username");
        //密码
        $password = $this->_post("password");
        //验证码
        $code = $this->_post("code");
        //登陆有效时间
        $cookietime = (int) $this->_post("cookietime");
        if (!$username || !$password) {
            $this->error("请填写用户名或者密码！");
        }
        
        //登陆是否开启验证码验证 0为关闭 1为开启
        $openverification = (int)$this->Member_config['openverification'];
        
        if ($openverification && !$this->verify($code,"userlogin")) {
            $this->error("验证码错误，请重新输入！");
        }
        $Member = M("Member");
        $status = service("Passport")->loginLocal($username, $password, $cookietime ? $cookietime : 86400);
        if ($status > 0) {
            $info = $Member->where(array("userid" => $status))->find();
            //待审核
            if ($info['checked'] == 0) {
                service("Passport")->logoutLocal();
                $this->error("该帐号还未审核通过，暂无法登陆！");
            }
            $forward = $_REQUEST['forward'] ? $_REQUEST['forward'] : cookie("forward");
            cookie("forward",null);
            $this->success("登陆成功！", $forward ? $forward : U("Member/Index/index"));
        } else {
            $this->error("用户名或者密码错误，登陆失败！", U("Index/login"));
        }
    }

    /**
     * 会员注册邮件认证 
     */
    public function public_verifyemail() {
        $code = $this->_get('code');
        $code = authcode($code, 'DECODE');
        $ip = get_client_ip();
        if ($code) {
            $Member = D("Member");
            $info = $Member->where(array("userid" => (int) $code))->find();
            if ($info) {
//                if($ip != $info['regip']){
//                    $this->error("IP不正确！");
//                }
                if ($info['groupid'] != 7) {
                    $this->error("已经验证过无需重复验证！", U("Index/login"));
                }
                if ($Member->where(array("userid" => $info['userid']))->save(array("groupid" => $Member->get_usergroup_bypoint())) !== false) {
                    $this->success("邮箱验证成功！", U("Index/login"));
                } else {
                    $this->error("验证过程中出现错误，请联系管理员！", CONFIG_SITEURL);
                }
            } else {
                $this->error("验证地址有误！", CONFIG_SITEURL);
            }
        } else {
            $this->error("验证地址有误！", CONFIG_SITEURL);
        }
    }

    /**
     * 密码找回 
     */
    public function public_forget_password() {
        if (IS_POST) {
            $email = $this->_post("email");
            $code = $this->_post("code");
            //验证码开始验证
            if (!$this->verify($code)) {
                $this->error("验证码错误，请重新输入！");
            }
            $Member = M("Member");
            $info = $Member->where(array("email" => $email))->find();
            if ($info) {
                //发送邮件
                $code = urlencode(authcode($info['username'], '', '', 3600));
                $url = CONFIG_SITEURL . "index.php?g=member&c=index&a=public_forget_password&code=$code";
                $message = $this->Member_config['forgetpassword'];
                $message = str_replace(array('{$click}', '{$url}'), array('<a href="' . $url . '">请点击</a>', $url), $message);
                SendMail($info['email'], "会员密码找回", $message);
                $this->success("邮件已经发送到你注册邮箱！", U("Index/login"));
            } else {
                $this->error("邮箱地址有误！");
            }
        } else {
            if ($_GET['code']) {
                //设置密码
                $code = $this->_get("code");
                $code = authcode($code, 'DECODE');
                $password = genRandomString(6);
                $status = service("Passport")->user_edit($code, "", $password, "", 1);
                if ($status) {
                    $this->assign("waitSecond", 10000);
                    $this->success("密码初始化成功，新密码：" . $password . "，请尽快修改！", U("Index/login"));
                    exit;
                } else {
                    $this->error("密码初始失败，请联系管理员！");
                }
            }
            $this->display("Public:forget_password");
        }
    }

}

?>
