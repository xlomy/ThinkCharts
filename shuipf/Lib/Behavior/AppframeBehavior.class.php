<?php

/**
 * 应用初始化前进行相关配置操作和初始操作
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class AppframeBehavior extends Behavior {

    //行为参数定义
    protected $options = array();

    public function run(&$params) {
        //注册
        spl_autoload_register(array('AppframeBehavior', 'autoload'));
        //消除所有的magic_quotes_gpc转义
        Input::noGPC();
        //初始化网站基本配置
        $Config = F("Config");
        if (false == $Config) {
            $Config = D("Config")->config_cache();
        }
        //网站访问地址
        define("SITEURL", $Config['siteurl']);
        foreach ($Config as $k => $v) {
            define('CONFIG_' . strtoupper($k), $v);
        }
        //取得已安装模块缓存
        $App = F("App");
        if (false == $App) {
            //生成好缓存
            D("Module")->module_cache();
            $App = F("App");
        }
        //配置已安装模块列表
        C("APP_GROUP_LIST", implode(",", $App));
    }

    /**
     * 系统自动加载类库
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        //加载分组模型
        if (substr($class, -5) == 'Model') {
            $Model = ucwords(str_replace('Model', "", $class));
            if (require_cache(APP_PATH . C("APP_GROUP_PATH") . "/{$Model}/Model/{$class}.class.php")) {
                return;
            }
        } elseif (substr($class, -7) == 'Service') {//加载 Service
            if (require_cache(LIB_PATH . "/Service/{$class}.class.php")) {
                return;
            }
        } elseif (substr($class, -6) == 'TagLib') {//加载 TagLib
            if (require_cache(LIB_PATH . "/TagLib/{$class}.class.php")) {
                return;
            }
        } elseif (substr($class, 0, 6) == 'TagLib') {//加载 TagLib
            if (require_cache(LIB_PATH . "/TagLib/{$class}.class.php")) {
                return;
            }
        } else {//加载 Util下 自定义类
            if (require_cache(LIB_PATH . "/Util/{$class}.class.php")) {
                return;
            }
        }
    }

}

?>
