<?php

/**
 * QQ空间帐号登陆
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ConnectqqAction extends AppframeAction {

    public $Config, $qq_akey, $qq_skey;

    public function _initialize() {
        parent::_initialize();
        $this->Config = F("Member_Config");
        if (!$this->Config) {
            $setting = M("Module")->where(array("module" => "Member"))->getField("setting");
            if (!$setting) {
                //获取不到配置，记录错误日志
                $msg = "获取不到相关配置，QQ互联无法进行！";
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
        $this->qq_akey = $this->Config['qq_akey'];
        $this->qq_skey = $this->Config['qq_skey'];
        if (!$this->qq_akey || !$this->qq_skey) {
            $msg = "没有进行QQ互联的相关配置，请配置后在继续使用！";
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
        $sState = authcode(get_client_ip(), "");
        session("state", $sState);
        //回调地址
        $sUri = (is_ssl() ? 'https://' : 'http://') . $_SERVER["HTTP_HOST"] . "/api.php?m=connectqq&a=callback";
        session("redirect_uri", $sUri);
        //请求用户授权时向用户显示的可进行授权的列表
        $scope = "get_user_info,add_share,check_page_fans";
        //请求参数
        $aParam = array(
            "response_type" => "code",
            "client_id" => $this->qq_akey,
            "redirect_uri" => $sUri,
            "scope" => $scope,
            "state" => $sState
        );

        //对参数进行URL编码
        $aGet = array();
        foreach ($aParam as $key => $val) {
            $aGet[] = $key . "=" . urlencode($val);
        }
        //请求地址
        $sUrl = "https://graph.qq.com/oauth2.0/authorize?";
        $sUrl .= join("&", $aGet);

        //跳转
        header("location:" . $sUrl);
    }

    //回调
    public function callback() {
        //安全验证，验证state是否合法
        $state = authcode($_GET['state']);
        if ($state != get_client_ip()) {
            $this->error("IP不正确");
        }
        import('Util.Curl',APP_PATH.'Lib');
        $curl = new Curl();
        $sUrl = "https://graph.qq.com/oauth2.0/token";
        $aGetParam = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->qq_akey,
            "client_secret" => $this->qq_skey,
            "code" => $_GET["code"],
            "state" => $_GET["state"],
            "redirect_uri" => session("redirect_uri")
        );
        session("redirect_uri", NULL);
        //Step2：通过Authorization Code获取Access Token
        foreach ($aGetParam as $key => $val) {
            $aGet[] = $key . "=" . urlencode($val);
        }
        $sContent = $curl->get($sUrl."?".  implode("&", $aGet));
        
        if ($sContent == FALSE) {
            $this->error("帐号授权出现错误！");
        }
        //参数处理
        $aTemp = explode("&", $sContent);
        $aParam = array();
        foreach ($aTemp as $val) {
            $aTemp2 = explode("=", $val);
            $aParam[$aTemp2[0]] = $aTemp2[1];
        }
        //保存access_token
        session("access_token", $aParam["access_token"]);
        $sUrl = "https://graph.qq.com/oauth2.0/me";
        $aGetParam = array(
            "access_token" => $aParam["access_token"]
        );
        //$sContent = $this->get($sUrl, $aGetParam);
        foreach ($aGetParam as $key => $val) {
            $aGet[] = $key . "=" . urlencode($val);
        }
        $sContent = $curl->get($sUrl."?".  implode("&", $aGet));
        
        if ($sContent == FALSE) {
            $this->error("帐号授权出现错误！");
        }
        $aTemp = array();
        //处理授权成功以后，返回的一串类似：callback( {"client_id":"000","openid":"xxx"} );
        preg_match('/callback\(\s+(.*?)\s+\)/i', $sContent, $aTemp);
        //把json数据转换为数组
        $aResult = json_decode($aTemp[1], true);
        //合并数组，把access_token和expires_in合并。
        $Result = array_merge($aResult,$aParam);
        $this->user($Result);
    }

    /**
     * 登陆/注册
     * @param type $openid 标识
     */
    protected function user($Result) {
        $openid = $Result['openid'];
        if (!$openid) {
            $this->error("登陆失败！");
        }
        $db = M("Connect");
        $uid = $db->where(array("openid" => $openid, "app" => "qq"))->getField("uid");

        if ($uid) {
            //更新access_token
            $db->where(array("openid" => $openid, "app" => "qq"))->save(array(
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
                }else{
                    $this->error("登陆失败！");
                }
            } else if ($status == -1) {
                $this->error("用户不存在！",U("Member/Index/connectregister"));
            } else {
                $this->error("登陆失败！",U("Member/Index/login"));
            }
        } else {
            header("Content-type: text/html; charset=utf-8");
            session("Connect_openid", $openid);
            session("Connect_expires", time()+(int)$Result['expires_in']);
            session("Connect_app", "qq");
            //不存在，跳转到注册页面
            redirect(U("Member/Index/connectregister"), 2, "授权成功，跳转中...");
        }
    }

}

?>
