<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ThemeAction extends AdminbaseAction {

    function _initialize() {
        parent::_initialize();
    }

    /**
     * 主题显示 
     */
    public function index() {
        $filed = glob(TEMPLATE_PATH . '*');
        $count = 0;
        foreach ($filed as $key => $v) {
            if(is_dir($v) == false){
                continue;
            }
            $arr[$key]['name'] = basename($v);
            if (is_file(TEMPLATE_PATH . $arr[$key]['name'] . '/preview.jpg')) {
                $arr[$key]['preview'] = CONFIG_SITEURL . str_replace(SITE_PATH . "/", "", TEMPLATE_PATH) . $arr[$key]['name'] . '/preview.jpg';
            } else {
                $arr[$key]['preview'] = CONFIG_SITEURL . 'statics/images/nopic.jpg';
            }
            if (AppframeAction::$Cache["Config"]['theme'] == $arr[$key]['name'])
                $arr[$key]['use'] = 1;
            $count ++;
        }

        $this->assign('themes', $arr);
        $this->assign('count', $count);
        $this->display();
    }

    /**
     * 风格选择 
     */
    public function chose() {
        $theme = $this->_get("theme");
        if (empty($theme)) {
            $this->error("主题名称不能为空！");
        }

        if ($theme == AppframeAction::$Cache["Config"]['theme']) {
            $this->error("主题未改变！",U("Theme/index"));
        }

        $status = D('Config')->where(array("varname" => "theme"))->save(array("value" => $theme));
        if ($status) {
            $this->success("更新成功！");
        } else {
            $this->error("更新失败！");
        }
    }

}

?>
