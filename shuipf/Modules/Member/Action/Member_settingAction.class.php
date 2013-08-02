<?php

/**
 * 会员中心后台管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Member_settingAction extends AdminbaseAction {
    
    public $Member_group, $Model_Member;
    
    function _initialize() {
        parent::_initialize();
        import('Form');
        $group = F("Member_group");
        foreach ($group as $k => $v) {
            $this->Member_group[$v['groupid']] = $v['name'];
        }
        $Model_Member = F("Model_Member");
        foreach ($Model_Member as $k => $v) {
            $this->Model_Member[$v['modelid']] = $v['name'];
        }

        $this->assign("Member_group", $this->Member_group);
        $this->assign("Model_Member", $this->Model_Member);
    }

    /**
     * 会员模块设置 
     */
    public function setting() {
        if (IS_POST) {
            $setting = $_POST['setting'];
            $data['setting'] = serialize($setting);
            $Module = M("Module");
            if ($Module->create()) {
                if ($Module->where(array("module" => "Member"))->save($data) !== false) {
                    import('Cacheapi');
                    $Cache = new Cacheapi();
                    $Cache->member_cache();
                    $this->success("更新成功！", U("Member_setting/setting"));
                } else {
                    $this->error("更新失败！", U("Member_setting/setting"));
                }
            } else {
                $this->error($Module->getError());
            }
        } else {
            $setting = M("Module")->where(array("module" => "Member"))->getField("setting");
            $this->assign("setting", unserialize($setting));
            $this->assign("show_header",false);
            $this->display();
        }
    }

    /**
     * Ucenter 测试数据库链接 
     */
    public function myqsl_test() {
        $host = isset($_GET['host']) && trim($_GET['host']) ? trim($_GET['host']) : exit('0');
        $password = isset($_GET['password']) && trim($_GET['password']) ? trim($_GET['password']) : exit('0');
        $username = isset($_GET['username']) && trim($_GET['username']) ? trim($_GET['username']) : exit('0');
        if (@mysql_connect($host, $username, $password)) {
            exit('1');
        } else {
            exit('0');
        }
    }

}

?>
