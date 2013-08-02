<?php

/**
 * 网站配置信息管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ConfigAction extends AdminbaseAction {

    protected $site_config, $user_config, $Config;

    function _initialize() {
        parent::_initialize();
        $this->Config = D("Config");
        import('Form');
        $config = $this->Config->select();
        foreach ($config as $key => $r) {
            if ($r['groupid'] == 1)
                $this->user_config[$r['varname']] = Input::forShow($r['value']);
            if ($r['groupid'] == 2)
                $this->site_config[$r['varname']] = Input::forShow($r['value']);
        }
        $this->assign('Site', $this->site_config);
    }

    /**
     * 网站基本设置
     */
    public function index() {
        if (IS_POST) {
            $this->dosite();
        } else {
            //首页模板
            $filepath = TEMPLATE_PATH . (empty(AppframeAction::$Cache["Config"]['theme']) ? "Default" : AppframeAction::$Cache["Config"]['theme']) . "/Contents/Index/";
            $indextp = str_replace($filepath, "", glob($filepath . 'index*'));
            $urlrules_detail = F("urlrules_detail");
            $IndexURL = array();
            $TagURL = array();
            foreach ($urlrules_detail as $k => $v) {
                if ($v['module'] == 'tags' && $v['file'] == 'tags') {
                    $TagURL[$v['urlruleid']] = $v['example'];
                }
                if ($v['module'] == 'content' && $v['file'] == 'index') {
                    $IndexURL[$v['ishtml']][$v['urlruleid']] = $v['example'];
                }
            }

            $this->assign("TagURL", $TagURL);
            $this->assign("IndexURL", $IndexURL);
            $this->assign("indextp", $indextp);
            $this->display();
        }
    }

    /**
     *  邮箱参数
     */
    public function mail() {
        if (IS_POST) {
            $this->dosite();
        } else {
            $this->display();
        }
    }

    /**
     *  附件参数
     */
    public function attach() {
        if (IS_POST) {
            $this->dosite();
        } else {
            $config = $this->Config->select();
            foreach ($config as $key => $r) {
                if ($r['groupid'] == 1)
                    $this->user_config[$r['varname']] = Input::forShow($r['value']);
                if ($r['groupid'] == 2)
                    $this->site_config[$r['varname']] = Input::forShow($r['value']);
            }
            $this->assign('Site', $this->site_config);
            $this->display();
        }
    }

    //高级配置
    public function addition() {
        if (IS_POST) {
            if (isset($_POST[C('TOKEN_NAME')])) {
                unset($_POST[C('TOKEN_NAME')]);
            }
            //默认值
            $_POST['DEFAULT_GROUP'] = $_POST['DEFAULT_GROUP']?$_POST['DEFAULT_GROUP']:"Contents";
            $_POST['INTERFACE_PASSPORT'] = $_POST['INTERFACE_PASSPORT']?$_POST['INTERFACE_PASSPORT']:"Passport";
            $_POST['TOKEN_ON'] = (int)$_POST['TOKEN_ON']?true:false;
            $_POST['URL_MODEL'] = isset($_POST['URL_MODEL'])?(int)$_POST['URL_MODEL']:0;
            $_POST['DEFAULT_TIMEZONE'] = $_POST['DEFAULT_TIMEZONE']?$_POST['DEFAULT_TIMEZONE']:"PRC";
            $_POST['DATA_CACHE_TYPE'] =$_POST['DATA_CACHE_TYPE']?$_POST['DATA_CACHE_TYPE']:"File";
            $_POST['DEFAULT_LANG'] = $_POST['DEFAULT_LANG']?$_POST['DEFAULT_LANG']:"zh-cn";
            $_POST['DEFAULT_AJAX_RETURN'] = $_POST['DEFAULT_AJAX_RETURN']?$_POST['DEFAULT_AJAX_RETURN']:"JSON";
            $_POST['SESSION_OPTIONS'] = $_POST['SESSION_OPTIONS']?$_POST['SESSION_OPTIONS']:array();
            $_POST['URL_PATHINFO_DEPR'] = $_POST['URL_PATHINFO_DEPR']?$_POST['URL_PATHINFO_DEPR']:"/";
            //URL区分大小写设置
            $_POST['URL_CASE_INSENSITIVE'] = (int)$_POST['URL_CASE_INSENSITIVE']?true:false;
            try {
                $filename = SITE_PATH . '/shuipf/Conf/addition.php';
                file_exists($filename) or touch($filename);
                $return = var_export($_POST, TRUE);
                if ($return) {
                    file_put_contents($filename, "<?php \r\n return " . $return . ";");
                }
            } catch (Exception $exc) {
                $this->error("保存失败，请检查文件[{$filename}]是否有可读写权限！");
            }
            $this->success("修改成功！", U("Admin/Index/public_cache", "type=site"));
        } else {
            $addition = include SITE_PATH . '/shuipf/Conf/addition.php';
            if (!$addition) {
                $addition = array();
            }
            $this->assign("addition", $addition);
            $this->display();
        }
    }

    //更新配置
    protected function dosite() {
        if (!$this->Config->autoCheckToken($_POST)) {
            $this->error(L('_TOKEN_ERROR_'));
        }
        unset($_POST[C("TOKEN_NAME")]);
        foreach ($_POST as $key => $value) {
            $data["value"] = trim($value);
            $this->Config->where(array("varname" => $key))->save($data);
        }
        $this->success("更新成功！");
    }

}

?>
