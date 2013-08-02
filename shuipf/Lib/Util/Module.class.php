<?php

/**
 * 模块安装
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Module extends AppframeAction {

    public $configpath, $config;
    public $templatePath;

    function _initialize() {
        parent::_initialize();
        //模板安装目录，模板安装目录强制在Default是出于，如果用户安装了模块后，又切换了主题，会造成找不到模板报错，只好强制安装在Default主题下！---水平凡
        $this->templatePath = TEMPLATE_PATH . "Default" . DIRECTORY_SEPARATOR;
        //添加一个菜单到后台“模块->模块列表”ID=74
        define("MENUID", 74);
    }

    //安装
    public function install() {
        define("INSTALL", true);
        import("Dir");
        //安装目录
        $path = APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR . $this->config['module'] . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR;
        $Dir = new Dir();
        //SQL文件
        if (file_exists($path . $this->config['module'] . '.sql')) {
            $sql = file_get_contents($path . $this->config['module'] . '.sql');
            $sql_split = $this->sql_split($sql, C("DB_PREFIX"));
            $db = M('');
            if (is_array($sql_split)) {
                foreach ($sql_split as $s) {
                    $db->execute($s);
                }
            }
        }

        //Extention，菜单添加
        if (file_exists($path . 'Extention.inc.php')) {
            @include ($path . 'Extention.inc.php');
        }

        //前台模板
        if (file_exists($path . "Template" . DIRECTORY_SEPARATOR)) {
            $Dir->copyDir($path . "Template", $this->templatePath);
        }

        D("Module")->add(array(
            "module" => $this->config['module'],
            "name" => $this->config['modulename'],
            "iscore" => 0,
            "version" => $this->config['version'],
            "description" => $this->config['introduce'],
            "disabled" => 1,
            "installdate" => date("Y-m-d"),
            "updatedate" => date("Y-m-d"),
        ));
        return true;
    }

    //卸载
    public function uninstall($module) {
        if (!$module) {
            $this->error("参数出错！");
        }
        $info = M("Module")->where(array("module" => $module))->find();
        if ($info) {
            define("UNINSTALL", true);
            import("Dir");
            //卸载目录
            $path = APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Uninstall' . DIRECTORY_SEPARATOR;
            $Dir = new Dir();
            //SQL文件
            if (file_exists($path . $module . '.sql')) {
                $sql = file_get_contents($path . $module . '.sql');
                $sql_split = $this->sql_split($sql, C("DB_PREFIX"));
                $db = M('');
                if (is_array($sql_split)) {
                    foreach ($sql_split as $s) {
                        $db->execute($s);
                    }
                }
            }
            if (file_exists($path . 'Extention.inc.php')) {
                @include ($path . 'Extention.inc.php');
            }

            //前台模板
            if (file_exists($this->templatePath . $module . DIRECTORY_SEPARATOR)) {
                $Dir->delDir($this->templatePath . $module . DIRECTORY_SEPARATOR);
            }
            D("Module")->where(array("module" => $module))->delete();
            //删除权限
            M("Access")->where(array("g" => $module))->delete();
            return true;
        } else {
            $this->error("该模块不存在，无法卸载！");
        }
    }

    //验证安装
    public function check($module) {
        if (!$module) {
            $this->error("参数出错！");
        }
        $info = M("Module")->where(array("module" => $module))->find();
        if ($info) {
            $this->error("该模块已经安装过！");
        }
        $this->configpath = APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php';
        if (!file_exists($this->configpath)) {
            $this->error("配置文件不存在！$module");
        }
        require $this->configpath;
        $this->config = array(
            "module" => $module,
            "modulename" => $modulename,
            "introduce" => $introduce,
            "author" => $author,
            "authorsite" => $authorsite,
            "authoremail" => $authoremail,
            "version" => $version,
        );
        return $this->config;
    }

    /**
     * 处理sql语句，执行替换前缀都功能。
     * @param string $sql 原始的sql
     * @param string $tablepre 表前缀
     */
    private function sql_split($sql, $tablepre) {
        if ($tablepre != "shuipfcms_")
            $sql = str_replace("shuipfcms_", $tablepre, $sql);
        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        if ($r_tablepre != $s_tablepre)
            $sql = str_replace($s_tablepre, $r_tablepre, $sql);
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }

    //文件夹权限检测
    public function chechmod() {
        //检查模板文件夹是否有可写权限 TEMPLATE_PATH
        $tfile = "_test.txt";
        $fp = @fopen($this->templatePath . $tfile, "w");
        if (!$fp) {
            return false;
        }
        fclose($fp);
        $rs = @unlink($this->templatePath . $tfile);
        if (!$rs) {
            return false;
        }
        return true;
    }

}

?>
