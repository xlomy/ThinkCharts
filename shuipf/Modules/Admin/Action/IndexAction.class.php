<?php

/**
 * 后台首页
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class IndexAction extends AdminbaseAction {

    //后台框架首页
    public function index() {
        $this->assign("SUBMENU_CONFIG", json_encode(D("Menu")->menu_json()));
        $this->display();
    }

    //缓存更新
    public function public_cache() {
        if (isset($_GET['type'])) {
            import("Dir");
            import('Cacheapi');
            $Cache = new Cacheapi();
            $Cachepath = RUNTIME_PATH;
            $Dir = new Dir();
            $type = $this->_get("type");
            switch ($type) {
                case "site":
                    try {
                        $Dir->del($Cachepath);
                        $Dir->del($Cachepath . "Data/");
                        $Dir->del($Cachepath . "Data/_fields/");
                    } catch (Exception $exc) {
                        
                    }
                    try{
                        $cache = Cache::getInstance();
                        $cache->clear();
                    }catch (Exception $exc) {}
                    
                    $modules = array(
                        array('name' => "菜单，模型，栏目缓存更新成功！", 'function' => 'site_cache', 'param' => ''),
                        array('name' => "模型字段缓存更新成功！", 'function' => 'model_field_cache', 'param' => ''),
                        array('name' => "模型content处理类缓存更新成功！", 'function' => 'model_content_cache', 'param' => ''),
                        array('name' => "会员相关缓存更新成功！", 'function' => 'member_cache', 'param' => ''),
                        array('name' => "应用更新成功！", 'function' => 'appstart_cache', 'param' => ''),
                        array('name' => "敏感词缓存生成成功！", 'function' => 'censorword_cache', 'param' => ''),
                    );
                    foreach ($modules as $k => $v) {
                        try {
                            if ($v['function']) {
                                $Cache->$v['function']();
                            }
                        } catch (Exception $exc) {
                            
                        }
                    }
                    $this->success("站点数据缓存更新成功！", U('Index/public_cache'));
                    break;
                case "template":
                    $Dir->delDir($Cachepath . "Cache/");
                    $this->success("模板缓存清理成功！", U('Index/public_cache'));
                    break;
                case "logs":
                    $Dir->del($Cachepath . "Logs/");
                    $this->success("站点日志清理成功！", U('Index/public_cache'));
                    break;
                default:
                    $this->error("请选择清楚缓存类型！");
                    break;
            }
        } else {
            $this->display("Index:cache");
        }
    }

    //后台框架首页菜单搜索
    public function public_find() {
        $keyword = trim($this->_get("keyword"));
        if (!$keyword) {
            $this->error("请输入需要搜索的关键词！");
        }
        $where = array();
        $where['name'] = array("LIKE", "%$keyword%");
        $where['status'] = array("EQ", 1);
        $where['type'] = array("EQ", 1);
        $data = M("Menu")->where($where)->select();
        $menuData = $menuName = array();
        $Module = F("Module");
        foreach ($data as $k => $v) {
            $menuData[ucwords($v['app'])][] = $v;
            $menuName[ucwords($v['app'])] = $Module[ucwords($v['app'])]['name'];
        }
        $this->assign("menuData", $menuData);
        $this->assign("menuName", $menuName);
        $this->assign("keyword", $keyword);
        $this->display();
    }

}

?>
