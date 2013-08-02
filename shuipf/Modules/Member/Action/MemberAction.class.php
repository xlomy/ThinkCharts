<?php

/**
 * 会员中心后台管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class MemberAction extends AdminbaseAction {

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

    public function error($message, $jumpUrl = '', $ajax = false) {
        parent::error($message, $jumpUrl, $ajax);
    }

    /**
     * 会员管理 
     */
    public function index() {
        $Member = M("Member");
        $where = array();
        if ($_GET['search']) {
            //注册时间段
            $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
            $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', time());
            //开始时间
            $where_start_time = strtotime($start_time) ? strtotime($start_time) : 0;
            //结束时间
            $where_end_time = strtotime($end_time)?(strtotime($end_time) + 86400):0;
            //开始时间大于结束时间，置换变量
            if ($where_start_time > $where_end_time) {
                $tmp = $where_start_time;
                $where_start_time = $where_end_time;
                $where_end_time = $tmp;
                $tmptime = $start_time;

                $start_time = $end_time;
                $end_time = $tmptime;
                unset($tmp, $tmptime);
            }
            //时间范围
            if($where_start_time){
                $where['regdate'] = array('between', array($where_start_time, $where_end_time));
            }
            
            //状态
            $status = (int) $this->_get("status");
            if ($status > 0) {
                $islock = $status == 1 ? 1 : 0;
                $where['islock'] = array("EQ", $islock);
            }
            //会员模型
            $modelid = (int) $this->_get("modelid");
            if ($modelid > 0) {
                $where['modelid'] = array("EQ", $modelid);
            }
            //会员组
            $groupid = (int) $this->_get("groupid");
            if ($groupid > 0) {
                $where['groupid'] = array("EQ", $groupid);
            }
            //关键字
            $keyword = $this->_get('keyword');
            if ($keyword) {
                $type = (int) $this->_get('type');
                switch ($type) {
                    case 1:
                        $where['username'] = array("LIKE", '%' . $keyword . '%');
                        break;
                    case 2:
                        $where['userid'] = array("EQ", $keyword);
                        break;
                    case 3:
                        $where['email'] = array("LIKE", '%' . $keyword . '%');
                        break;
                    case 4:
                        $where['regip'] = array("EQ", $keyword);
                        break;
                    case 5:
                        $where['nickname'] = array("LIKE", '%' . $keyword . '%');
                        break;
                    default:
                        $where['username'] = array("LIKE", '%' . $keyword . '%');
                        break;
                }
            }
        }

        $count = $Member->where($where)->count();
        $page = $this->page($count, 20);
        $data = $Member->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("userid" => "DESC"))->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign("data", $data);

        $this->display();
    }

    /**
     * 添加会员 
     */
    public function add() {
        if (IS_POST) {
            $Member = D("Member");
            $data = $Member->create();
            if ($data) {
                $data = array_merge($_POST, $data);
                $data['groupid'] = $Member->get_usergroup_bypoint($data['point']);
                $userid = service("Passport")->user_register($data['username'], $data['password'], $data['email'], $data);
                if ($userid > 0) {
                    //添加到相应模型
                    $Model_Member = F("Model_Member");
                    $tablename = $Model_Member[$data['modelid']]['tablename'];
                    M($tablename)->add(array("userid" => $userid));
                    $this->success("添加会员成功！", U("Member/index"));
                } else {
                    $this->error("会员添加失败！");
                }
            } else {
                $this->error($Member->getError());
            }
        } else {
            $this->display();
        }
    }

    /**
     * 修改会员 
     */
    public function edit() {
        $Member = D("Member");
        if (IS_POST) {
            $data = $Member->create();
            if ($data) {
                //根据积分，算出用户组
                if (isset($data['point'])) {
                    $data['groupid'] = $_POST['groupid'] = $Member->get_usergroup_bypoint($data['point']);
                }
                //检测帐号基本信息是否需要做修改：密码，邮箱
                //========================用户名，旧密码，新密码，邮箱，是否忽略旧密码1
                $edit = service("Passport")->user_edit($data['username'], $data['password'], $data['password'], $data['email'], 1, $data);
                if ($edit < 0) {
                    switch ($edit) {
                        case -4:
                            $this->error("Email 格式有误！");
                            break;
                        case -5:
                            $this->error("Email 不允许注册！");
                            break;
                        case -6:
                            $this->error("该 Email 已经被注册！");
                            break;
                        default :
                            $this->error("修改失败！");
                            break;
                    }
                }

                //判断是否需要删除头像
                if ($this->_post("delavatar")) {
                    service("Passport")->user_deleteavatar($data['userid']);
                }
                $modelid = $this->_post('modelid');
                require_cache(RUNTIME_PATH . 'content_input.class.php');
                $content_input = new content_input($modelid, $this);
                $inputinfo = $content_input->get($_POST['info']);
                //取得模型内容
                $modedata = $inputinfo['model'];
                $Model_Member = F("Model_Member");
                $tablename = ucwords($Model_Member[$modelid]['tablename']);
                M($tablename)->where(array("userid" => $data['userid']))->save($modedata);
                $this->success("更新成功！");
            } else {
                $this->error($Member->getError());
            }
        } else {
            $userid = (int) $this->_get("userid");
            //主表
            $data = $Member->where(array("userid" => $userid))->find();
            if ($this->_get("modelid")) {
                $modelid = (int) $this->_get("modelid");
                if (!$this->Model_Member[$modelid]) {
                    $this->error("该模型不存在！");
                }
            } else {
                $modelid = $data['modelid'];
            }
            if (!$data) {
                $this->error("该会员不存在！");
            }
            $Model_Member = F("Model_Member");
            $tablename = $Model_Member[$modelid]['tablename'];
            //相应会员模型数据
            $modeldata = M(ucwords($tablename))->where(array("userid" => $userid))->find();
            if (!is_array($modeldata)) {
                $modeldata = array();
            }
            $data = array_merge($data, $modeldata);
            //引入输入表单处理类
            require RUNTIME_PATH . 'content_form.class.php';
            $content_form = new content_form($modelid);
            $data['modelid'] = $modelid;
            //字段内容
            $forminfos = $content_form->get($data);
            //js提示
            $formValidator = $content_form->formValidator;

            $this->assign("forminfos", $forminfos);
            $this->assign("formValidator", $formValidator);
            $this->assign("data", $data);
            $this->assign("show_header", true);
            $this->display();
        }
    }

    /**
     * 删除会员 
     */
    public function delete() {
        if (IS_POST) {
            $userid = $_POST['userid'];
            if (!$userid) {
                $this->error("请选择需要删除的会员！");
            }
            $Member = M("Member");
            $connect = M("Connect");
            foreach ($userid as $uid) {
                $modelid = $Member->where(array("userid" => $uid))->getField("modelid");
                if ($modelid) {
                    //删除授权登陆信息
                    if (service("Passport")->user_delete($uid)) {
                        $connect->where(array("uid" => $uid))->delete();
                    }
                }
            }
            $this->success("删除成功！");
        }
    }

    /**
     * 锁定会员 
     */
    public function lock() {
        if (IS_POST) {
            $userid = $_POST['userid'];
            if (!$userid) {
                $this->error("请选择需要锁定的会员！");
            }
            $Member = M("Member");
            foreach ($userid as $uid) {
                $modelid = $Member->where(array("userid" => $uid))->getField("modelid");
                if ($modelid) {
                    $Member->where(array("userid" => $uid))->save(array("islock" => 1));
                }
            }
            $this->success("锁定成功！");
        }
    }

    /**
     * 解除锁定会员 
     */
    public function unlock() {
        if (IS_POST) {
            $userid = $_POST['userid'];
            if (!$userid) {
                $this->error("请选择需要解锁的会员！");
            }
            $Member = M("Member");
            foreach ($userid as $uid) {
                $modelid = $Member->where(array("userid" => $uid))->getField("modelid");
                if ($modelid) {
                    $Member->where(array("userid" => $uid))->save(array("islock" => 0));
                }
            }
            $this->success("解锁成功！");
        }
    }

    /**
     * 会员资料查看 
     */
    public function memberinfo() {
        $this->assign("show_header", true);
        $userid = (int) $this->_get("userid");
        $Member = M("Member");
        //主表
        $data = $Member->where(array("userid" => $userid))->find();
        if (!$data) {
            $this->error("该会员不存在！");
        }
        $modelid = $data['modelid'];
        $Model_Member = F("Model_Member");
        $tablename = $Model_Member[$modelid]['tablename'];
        //相应会员模型数据
        $modeldata = M(ucwords($tablename))->where(array("userid" => $userid))->find();
        //去除敏感信息
        unset($data['password']);
        unset($data['encrypt']);
        require_cache(RUNTIME_PATH . 'content_output.class.php');
        $content_output = new content_output($modelid);
        $output_data = $content_output->get($modeldata);
        $Model_field = F("Model_field_" . $modelid);


        $this->assign("output_data", $output_data);
        $this->assign("Model_field", $Model_field);
        $this->assign($data);
        $this->display();
    }

    /**
     * 审核会员 
     */
    public function userverify() {
        $Member = M("Member");
        if (IS_POST) {
            $userid = $_POST['userid'];
            if (!$userid) {
                $this->error("请选择需要审核的会员！");
            }
            $Member = M("Member");
            foreach ($userid as $uid) {
                $modelid = $Member->where(array("userid" => $uid))->getField("modelid");
                if ($modelid) {
                    $Member->where(array("userid" => $uid))->save(array("checked" => 1));
                }
            }
            $this->success("审核成功！");
        } else {
            $where = array();
            $where['checked'] = array("EQ", 0);
            if ($_GET['search']) {
                //注册时间段
                $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
                $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', time());
                //开始时间
                $where_start_time = strtotime($start_time) ? strtotime($start_time) : 0;
                //结束时间
                $where_end_time = strtotime($end_time) + 86400;
                //开始时间大于结束时间，置换变量
                if ($where_start_time > $where_end_time) {
                    $tmp = $where_start_time;
                    $where_start_time = $where_end_time;
                    $where_end_time = $tmp;
                    $tmptime = $start_time;

                    $start_time = $end_time;
                    $end_time = $tmptime;
                    unset($tmp, $tmptime);
                }
                $where['regdate'] = array('between', array($where_start_time, $where_end_time));
                //会员模型
                $modelid = (int) $this->_get("modelid");
                if ($modelid > 0) {
                    $where['modelid'] = array("EQ", $modelid);
                }
                //会员组
                $groupid = (int) $this->_get("groupid");
                if ($groupid > 0) {
                    $where['groupid'] = array("EQ", $groupid);
                }
                //关键字
                $keyword = $this->_get('keyword');
                if ($keyword) {
                    $type = (int) $this->_get('type');
                    switch ($type) {
                        case 1:
                            $where['username'] = array("LIKE", '%' . $keyword . '%');
                            break;
                        case 2:
                            $where['userid'] = array("EQ", $keyword);
                            break;
                        case 3:
                            $where['email'] = array("LIKE", '%' . $keyword . '%');
                            break;
                        case 4:
                            $where['regip'] = array("EQ", $keyword);
                            break;
                        case 5:
                            $where['nickname'] = array("LIKE", '%' . $keyword . '%');
                            break;
                        default:
                            $where['username'] = array("LIKE", '%' . $keyword . '%');
                            break;
                    }
                }
            }

            $count = $Member->where($where)->count();
            $page = $this->page($count, 20);
            $data = $Member->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("userid" => "DESC"))->select();

            $this->assign("Page", $page->show('Admin'));
            $this->assign("data", $data);

            $this->display();
        }
    }

    //用户授权管理
    public function connect() {
        $db = M("Connect");
        $member = M("Member");
        if (IS_POST) {
            //批量删除
            $connectid = $this->_post("connectid");
            if (is_array($connectid)) {
                foreach ($connectid as $connectid) {
                    $db->where(array("connectid" => $connectid))->delete();
                }
                $this->success("操作成功！");
            } else {
                $this->error("操作失败！");
            }
        } else {
            $connectid = (int) $this->_get("connectid");
            if ($connectid) {
                //单个删除
                if ($db->where(array("connectid" => $connectid))->delete()) {
                    $this->success("取消绑定成功！");
                } else {
                    $this->error("操作失败！");
                }
            } else {
                $count = $db->count();
                $page = $this->page($count, 20);
                $data = $db->limit($page->firstRow . ',' . $page->listRows)->select();
                foreach ($data as $k => $r) {
                    $data[$k]['username'] = $member->where(array("userid" => $r['uid']))->getField("username");
                    $data[$k]['userid'] = $r['uid'];
                }
                $this->assign("show_header", 1);
                $this->assign("Page", $page->show('Admin'));
                $this->assign("data", $data);
                $this->display();
            }
        }
    }

}

?>
