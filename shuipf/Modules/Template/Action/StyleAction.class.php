<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class StyleAction extends AdminbaseAction {

    //模板文件夹
    private $filepath;
    //模板属性
    private $style_info;

    function _initialize() {
        parent::_initialize();
        if (empty(AppframeAction::$Cache['Config']['theme'])) {
            $this->error("主题风格为空！");
        }
        $this->filepath = TEMPLATE_PATH;
        if (file_exists($this->filepath . 'Config.php')) {
            $this->style_info = json_decode(file_get_contents($this->filepath . 'Config.php'), true);
        }
        $this->assign("show_header", true);
    }

    /**
     * 显示文件目录 
     * @ 中华英雄 2010-7-1
     * @ note: 增加了对后台页面的友好显示
     */
    public function index() {
        $ext = SITE_PATH . '/statics/images/ext/';
        $ExtUrl = CONFIG_SITEURL . 'statics/images/ext/';
        $extList = glob($ext . '*.*');
        $TplExtList = array();
        $dirico = 'dir.gif';

        $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\', '.',), '', trim(urldecode($_GET['dir']))) : '';

        if ($dir == ".") {
            $dir = "";
        }

        $dir = str_replace("-", "/", $dir);
        $filepath = $this->filepath . $dir;
        //echo $dir;
        $list = glob($filepath . DIRECTORY_SEPARATOR . '*');
        if (!empty($list))
            ksort($list);
        $local = str_replace(array(SITE_PATH, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR), array('', DIRECTORY_SEPARATOR), $filepath);
        if (substr($local, -1, 1) == '.') {
            $local = substr($local, 0, (strlen($local) - 1));
        }
        foreach ($list as $k => $v) {
            if (basename($v) == 'Config.php' || basename($v) == 'Thumbs.db') {
                unset($list[$k]);
            } else {
                //获取拓展名
                $thisExt = pathinfo($filepath . $v, PATHINFO_EXTENSION);
                //如果获取为空说明这是文件夹
                $thisExt == '' && $thisExt = 'dir';
                //检测是否有此类型的试图文件
                in_array($ext . $thisExt . '.jpg', $extList) && $TplExtList[$v] = $ExtUrl . $thisExt . '.jpg';
                in_array($ext . $thisExt . '.gif', $extList) && $TplExtList[$v] = $ExtUrl . $thisExt . '.gif';
                in_array($ext . $thisExt . '.png', $extList) && $TplExtList[$v] = $ExtUrl . $thisExt . '.png';
                in_array($ext . $thisExt . '.bmp', $extList) && $TplExtList[$v] = $ExtUrl . $thisExt . '.bmp';
                //兼容不存在视图的文件
                (!in_array($TplExtList[$v], $TplExtList) || $TplExtList[$v] == '') && $TplExtList[$v] = 'hlp.gif';
            }
        }
        $encode_local = str_replace(array('/', '\\'), '|', $local);
        $file_explan = $this->style_info['file_explan'];

        $this->assign("tplist", $list);
        $this->assign("dir", $dir);
        $this->assign("local", $local);
        $this->assign("file_explan", $file_explan);
        $this->assign("encode_local", $encode_local);

        $this->assign("tplextlist", $TplExtList);
        $this->assign("dirico", $dirico);
        $this->assign("diricolen", strlen($dirico));

        $this->display();
    }

    /**
     * 更新 
     */
    public function updatefilename() {
        $file_explan = isset($_POST['file_explan']) ? $_POST['file_explan'] : '';
        if (!isset($this->style_info['file_explan']))
            $this->style_info['file_explan'] = array();
        $this->style_info['file_explan'] = array_merge($this->style_info['file_explan'], $file_explan);
        if (file_put_contents($this->filepath . "Config.php", json_encode($this->style_info))) {
            $this->success("更新成功！");
        } else {
            $this->error('更新失败！');
        }
    }

    /**
     * 添加模板 
     */
    public function add() {
        if (IS_POST) {
            $file = explode(".", $this->_post("file"));
            $file = $file[0];
            $content = Input::getVar($this->_post("content"));
            $dir = $this->filepath . $this->_post("dir");
            $dir = str_replace(array("//"), array("/"), $dir);
            //完整新增文件路径
            $filepath = $dir . $file . C("TMPL_TEMPLATE_SUFFIX");
            if (file_exists($filepath)) {
                $this->error("该文件已经存在！");
            }
            $status = file_put_contents($filepath, htmlspecialchars_decode(stripslashes($content)));
            if ($status) {
                //@chmod($filepath, 0777);
                $this->success("保存成功！", U("Template/Style/index"));
            } else {
                $this->error("保存失败，请检查模板文件权限是否设置为可写！");
            }
        } else {
            $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\', '.',), '', trim(urldecode($_GET['dir']))) : '';
            $dir = str_replace("-", "/", $dir);
            if (!file_exists($this->filepath . $dir)) {
                $this->error("该目录不存在！");
            }
            $this->assign("dir", $dir);
            $this->display();
        }
    }

    /**
     * 删除模板 
     */
    public function delete() {
        $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\'), '', urldecode(trim($_GET['dir']))) : '';
        $dir = str_replace("-", "/", $dir);
        $file = isset($_GET['file']) && trim($_GET['file']) ? trim($_GET['file']) : '';
        $path = $this->filepath . $dir . "/" . $file;
        $path = str_replace(array("//"), array("/"), $path);
        if (file_exists($path)) {
            $status = unlink($path);
            if ($status) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败，请检查模板文件权限是否设置为可写！");
            }
        } else {
            $this->error("需要删除的文件不存在！");
        }
    }

    /**
     * 编辑文件 
     */
    public function edit_file() {
        if (IS_POST) {
            $dir = $this->_post("dir");
            $file = $this->_post("file");
            $path = $this->filepath . $dir . "/" . $file;
            $path = str_replace(array("//"), array("/"), $path);
            $content = Input::getVar($this->_post("content"));
            $status = file_put_contents($path, htmlspecialchars_decode(stripslashes($content)));
            if ($status) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败，请检查模板文件权限是否设置为可写！");
            }
            exit;
        } else {
            $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\'), '', urldecode(trim($_GET['dir']))) : '';
            $dir = str_replace("-", "/", $dir);
            $file = isset($_GET['file']) && trim($_GET['file']) ? trim($_GET['file']) : '';
            $path = $this->filepath . $dir . "/" . $file;
            if ($file) {
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    $content = Input::forTarea($content);
                } else {
                    $this->error("文件不存在！");
                }
            }
            $this->assign("content", $content);
            $this->assign("dir", $dir);
            $this->assign("file", $file);
        }
        $this->display();
    }

}

?>
