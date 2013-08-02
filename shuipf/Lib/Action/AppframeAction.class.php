<?php

/**
 * Appframe项目公共Action
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class AppframeAction extends Action {

    //各种缓存 比如当前登陆用户信息等
    public static $Cache = array();

    protected function _initialize() {
        //初始化站点配置信息
        $this->initSite();
        //跳转时间
        $this->assign("waitSecond", 2000);
        $this->assign("__token__", $this->getToken());
    }

    /**
     * 获取表单令牌
     * @return String 表单
     */
    protected function getToken() {
        $token_on = C("TOKEN_ON");
        if (!$token_on) {
            return "";
        }
        $tokenName = C('TOKEN_NAME');
        $tokenType = C('TOKEN_TYPE');
        if (!isset($_SESSION[$tokenName])) {
            $_SESSION[$tokenName] = array();
        }
        // 标识当前页面唯一性
        $tokenKey = md5($_SERVER['REQUEST_URI']);
        if (isset($_SESSION[$tokenName][$tokenKey])) {// 相同页面不重复生成session
            $tokenValue = $_SESSION[$tokenName][$tokenKey];
        } else {
            $tokenValue = $tokenType(microtime(TRUE));
            $_SESSION[$tokenName][$tokenKey] = $tokenValue;
        }
        $tokenAray = session($tokenName);
        //获取令牌
        $tokenValue = $tokenAray[$tokenKey];
        return $tokenKey . '_' . $tokenValue;
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data, $type = '') {
        if (func_num_args() > 2) {// 兼容3.0之前用法
            $args = func_get_args();
            array_shift($args);
            $info = array();
            $info['data'] = $data;
            $info['info'] = array_shift($args);
            $info['status'] = array_shift($args);
            $data = $info;
            $type = $args ? array_shift($args) : '';
        }
        if (isset($data['url'])) {
            $data['referer'] = $data['url'];
            unset($data['url']);
        }
        //提示类型，success fail
        $data['state'] = $data['status'] ? "success" : "fail";
        if (empty($type))
            $type = C('DEFAULT_AJAX_RETURN');
        switch (strtoupper($type)) {
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:text/html; charset=utf-8');
                exit(json_encode($data));
            case 'XML' :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler = isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler . '(' . json_encode($data) . ');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
            default :
                // 用于扩展其他返回格式数据
                tag('ajax_return', $data);
        }
    }

    /**
     *  初始化当前登录用户信息
     * @return Boolean 没有登陆返回false，有登陆信息返回User Info
     */
    final protected function initUser() {
        //当然登陆用户ID
        $usDb = service("Passport")->isLogged();
        if ($usDb == false) {
            return false;
        }

        self::$Cache['uid'] = (int) $usDb['userid'];
        self::$Cache['username'] = $usDb['username'];
        $this->assign("uid", self::$Cache['uid']);
        $this->assign("username", self::$Cache['username']);
        $User = $usDb;

        self::$Cache['User'] = $User;
        $this->assign("User", self::$Cache['User']);
        unset($usDb);
        return $User;
    }

    /**
     * 初始化站点配置信息
     * @return Arry 配置数组
     */
    final protected function initSite() {
        $Config = F("Config");
        /**
         * 模块绑定域名相关
         * 前台模板，比如JS调用，建议使用 {$config_siteurl}，因为如果模块有绑定域名
         * 使用{$config.siteurl}会造成JS跨域等等问题。
         */
        if (C("APP_SUB_DOMAIN")) {
            $config_siteurl = (is_ssl() ? 'https://' : 'http://') . C("APP_SUB_DOMAIN") . "/";
            //用于在程序中调用
            define("CONFIG_SITEURL_MODEL", $config_siteurl);
            $this->assign("config_siteurl", $config_siteurl);
        } else {
            $config_siteurl = $Config['siteurl'];
            define("CONFIG_SITEURL_MODEL", $config_siteurl);
            $this->assign("config_siteurl", $config_siteurl);
        }
        //去除敏感信息
        unset($Config['mail_password'], $Config['ftphost'], $Config['ftpuppat'], $Config['ftpuser'], $Config['ftppassword']);
        self::$Cache['Config'] = $Config;
        $this->assign("Config", $Config);
        return $Config;
    }

    /**
     * 初始化模型 
     * @return 没有返回值
     */
    final protected function initModel() {
        if (!F("Model")) {
            D("Model")->model_cache();
        }
        //栏目缓存
        if (!F("Category")) {
            D("Category")->category_cache();
        }
        //20120615 增加
        if (!is_file(RUNTIME_PATH . "content_output.class.php")) {
            import("Cacheapi");
            $Cache = new Cacheapi();
            $Cache->model_content_cache();
        }
        //会员相关必要缓存
        if (!F("Model_Member") || !F("Member_group")) {
            D("Model")->MemberModelCache();
            D("Member_group")->Membergroup_cache();
        }
    }

    /**
     * Cookie 设置、获取、删除 
     * @param String $name cookie名称
     * @param String $value cookie值
     * @param Arry $option 传入的cookie设置参数，默认为空，以数组的形式传递
     */
    final static public function cookie($name, $value = '', $option = null) {
        return SiteCookie($name, $value, $option);
    }

    /**
     * 写入操作日志
     * @param String $info 操作说明
     * @param type $status 状态,1为写入，2为更新，3为删除
     * @param type $data 数据
     * @param type $options 条件
     */
    final public function addLogs($info, $status = 1, $data = array(), $options = array()) {
        $uid = self::$Cache['uid'];
        if (!$uid) {
            return false;
        }
        $data = serialize($data);
        $options = serialize($options);
        $get = $_SERVER['HTTP_REFERER'];
        $post = "";
        M("Operationlog")->add(array(
            "uid" => $uid,
            "time" => date("Y-m-d H:i:s"),
            "ip" => get_client_ip(),
            "status" => $status,
            "info" => $info,
            "data" => $data,
            "options" => $options,
            "get" => $get,
            "post" => $post
        ));
    }

    /**
     * 验证码验证
     * @param type $verify 验证码
     * @param type $type 验证码类型
     * @return boolean
     */
    static public function verify($verify, $type = "verify") {
        $verifyArr = session("_verify_");
        if (!is_array($verifyArr)) {
            $verifyArr = array();
        }
        if ($verifyArr[$type] == strtolower($verify)) {
            unset($verifyArr[$type]);
            if (!$verifyArr) {
                $verifyArr = array();
            }
            session('_verify_', $verifyArr);
            return true;
        } else {
            return false;
        }
    }

    //空操作
    public function _empty() {
        $this->error('该页面不存在！');
    }

}

?>
