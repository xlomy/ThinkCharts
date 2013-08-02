<?php

/**
 * 模块管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ModuleAction extends AdminbaseAction {

    public function index() {
        $dirs = glob(APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR . '*');
        foreach ($dirs as $path) {
            if (is_dir($path)) {
                $path = basename($path);
                $dirs_arr[] = $path;
            }
        }

        //数量
        $total = count($dirs_arr);
        //把一个数组分割为新的数组块
        $dirs_arr = array_chunk($dirs_arr, 20, true);
        //当前分页
        $page = max(intval($_GET['page']), 1);
        $directory = $dirs_arr[intval($page - 1)];

        $pages = $this->page($total, 20);
        $modulesdata = M("Module")->select();
        foreach ($modulesdata as $v) {
            $modules[$v['module']] = $v;
        }
        $this->assign("Page", $pages->show("Admin"));
        $this->assign("data", $directory);
        $this->assign("modules", $modules);
        $this->display();
    }

    /**
     * 模块安装 
     */
    public function install() {
        import("Module");
        $Module = new Module();
        if ($Module->chechmod() == false) {
            $this->error("模板文件夹目录不可写！");
        }
        $module = $_POST['module'] ? $_POST['module'] : $_GET['module'];
        $info = $Module->check($module);
        if (IS_POST) {
            if ($Module->install()) {
                $this->success("安装成功，请及时更新缓存！", U("Module/index"));
            } else {
                $this->error("安装失败！");
            }
        } else {
            $this->assign($info);
            $this->display();
        }
    }

    /**
     * 模块卸载 
     */
    public function uninstall() {
        import("Module");
        $Module = new Module();
        if ($Module->chechmod() == false) {
            $this->error("模板文件夹目录不可写！");
        }
        $module = $_POST['module'] ? $_POST['module'] : $_GET['module'];
        if (!$module) {
            $this->error("请选择需要卸载的模块！");
        }
        if ($Module->uninstall($module)) {
            $this->success("模块卸载成功，请及时更新缓存！", U("Module/index"));
        } else {
            $this->error("模块卸载失败！", U("Module/index"));
        }
    }

}

?>
