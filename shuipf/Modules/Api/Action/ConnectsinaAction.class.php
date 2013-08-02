<?php

/**
 * 新浪互联帐号登陆
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ConnectsinaAction extends AppframeAction {

    public $Config;
    public $sinawb_akey; //新浪微博App key 
    public $sinawb_skey; //App secret key

    public function _initialize() {
        parent::_initialize();
        $this->Config = F("Member_Config");
        if (!$this->Config) {
            $setting = M("Module")->where(array("module" => "Member"))->getField("setting");
            if (!$setting) {
                //获取不到配置，记录错误日志
                $msg = "获取不到相关配置，新浪互联无法进行！";
                if (APP_DEBUG) {
                    // 模块不存在 抛出异常
                    throw_exception($msg);
                } else {
                    if (C('LOG_EXCEPTION_RECORD')) {
                        Log::write($msg);
                    }
                    send_http_status(404);
                    exit;
                }
            }
            $this->Config = unserialize($setting);
            F("Member_Config", $this->Config);
        }
        $this->sinawb_akey = $this->Config['sinawb_akey'];
        $this->sinawb_skey = $this->Config['sinawb_skey'];
        if (!$this->sinawb_akey || !$this->sinawb_skey) {
            $msg = "没有进行新浪互联的相关配置，请配置后在继续使用！";
            if (APP_DEBUG) {
                // 模块不存在 抛出异常
                throw_exception($msg);
            } else {
                if (C('LOG_EXCEPTION_RECORD')) {
                    Log::write($msg);
                }
                send_http_status(404);
                exit;
            }
        }
        //跳转时间
        $this->assign("waitSecond", 2000);
    }

    public function index() {
        $sState = $this->key(get_client_ip());
        //回调地址
        $redirect_uri = (is_ssl() ? 'https://' : 'http://')  . $_SERVER["HTTP_HOST"] . "/api.php?m=connectsina&a=callback";
        session("redirect_uri", $redirect_uri);
        //请求参数
        $aParam = array(
            "client_id" => $this->sinawb_akey, //申请应用时分配的AppKey。
            "redirect_uri" => $redirect_uri, //授权回调地址
            "state" => $sState
        );

        //对参数进行URL编码
        $aGet = array();
        foreach ($aParam as $key => $val) {
            $aGet[] = $key . "=" . urlencode($val);
        }
        //请求地址
        $sUrl = "https://api.weibo.com/oauth2/authorize?";
        $sUrl .= join("&", $aGet);

        //跳转
        header("location:" . $sUrl);
    }

    //回调
    public function callback() {
        //安全验证，验证state是否合法
        $state = $_GET['state'];
        if ($state != $this->key(get_client_ip())) {
            $this->error("IP不正确");
        }
        import('Util.Curl', APP_PATH . 'Lib');
        $curl = new Curl();
        $sUrl = "https://api.weibo.com/oauth2/access_token";
        $aGetParam = array(
            "code" => $_GET["code"],//用于调用access_token，接口获取授权后的access token
            "client_id" => $this->sinawb_akey,//申请应用时分配的AppSecret
            "client_secret" => $this->sinawb_skey,//申请应用时分配的AppSecret
            "grant_type" => "authorization_code",//请求的类型，可以为authorization_code、password、refresh_token。
            "redirect_uri" => session("redirect_uri"),//回调地址
        );
        session("redirect_uri", NULL);

        $sContent = $curl->post($sUrl,$aGetParam);

        if ($sContent == FALSE) {
            $this->error("帐号授权出现错误！");
        }
        //参数处理
        $aParam = json_decode($sContent, true);
        //保存access_token
        session("access_token", $aParam["access_token"]);

        //新浪微博没有类似腾讯还需取得openid，直接以新浪uid作为标识
        $this->user($aParam);
    }

    /**
     * 登陆/注册
     * @param type $openid 标识
     */
    protected function user($Result) {
        $openid = $Result['uid'];
        if (!$openid) {
            $this->error("授权失败！",U("Connectsina/index"));
        }
        
        $db = M("Connect");
        $uid = $db->where(array("openid" => $openid, "app" => "sina_weibo"))->getField("uid");

        if ($uid) {
            //更新access_token
            $db->where(array("openid" => $openid, "app" => "sina_weibo"))->save(array(
                "accesstoken" => $Result['access_token'],
                "expires" => time()+(int)$Result['expires_in'],
            ));
            //存在直接登陆
            $Member = M("Member");
            $info = $Member->where(array("userid" => $uid))->find();
            if ($info) {
                //待审核
                if ($info['checked'] == 0) {
                    $this->error("该帐号还未审核通过，暂无法登陆！");
                }
                if (service("Passport")->registerLogin($info)) {
                    $forward = $_REQUEST['forward'] ? $_REQUEST['forward'] : cookie("forward");
                    $this->success("登陆成功！", $forward ? $forward : U("Member/Index/index"));
                } else {
                    $this->error("登陆失败！");
                }
            } else if ($status == -1) {
                $this->error("用户不存在！", U("Member/Index/connectregister"));
            } else {
                $this->error("登陆失败！", U("Member/Index/login"));
            }
        } else {
            header("Content-type: text/html; charset=utf-8");
            session("Connect_openid", $openid);
            session("Connect_expires", time()+(int)$Result['expires_in']);
            session("Connect_app", "sina_weibo");
            //不存在，跳转到注册页面
            redirect(U("Member/Index/connectregister"), 2, "授权成功，跳转中...");
        }
    }

    //生成验证码
    private function key($args) {
        $auth_key = md5(C("AUTHCODE") . $_SERVER['HTTP_USER_AGENT']);
        $authkey = md5($args . $auth_key);
        return $authkey;
    }

}

?>
