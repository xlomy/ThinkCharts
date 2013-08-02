<?php

/**
 * 内容:添加/修改/删除
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Content {

    public $categorys, $Content, $table_name, $fbtable_name, $Model, $url;
    //模型ID
    public $modelid, $Error;

    function Content() {
        $this->categorys = F("Category");
        $this->Model = F("Model");
        import('Url');
        $this->url = new Url();
    }

    /**
     * 获取错误提示
     * @return type
     */
    public function getError() {
        return $this->Error;
    }

    /**
     * 添加内容
     * @param type $data 需要添加的数据
     * @return type 成功返回新增的信息ID
     */
    public function add($data) {
        import('Html');
        $html = new Html();
        $this->catid = (int) $data['catid'];
        $this->modelid = $this->categorys[$this->catid]['modelid'];
        require_array(array(
            RUNTIME_PATH . 'content_input.class.php',
            RUNTIME_PATH . 'content_update.class.php'
        ));
        $content_input = new content_input($this->modelid);
        $inputinfo = $content_input->get($data);

        if (is_bool($inputinfo) && $inputinfo == false) {
            //提示错误
            $this->Error = $content_input->getError();
            return false;
        }

        //标签
        tag("content_add_begin", $inputinfo);

        //主表字段内容
        $systeminfo = $inputinfo['system'];
        //副表字段内容
        $modelinfo = $inputinfo['model'];

        //栏目数据
        $catidinfo = $this->categorys[$systeminfo['catid']];
        //setting配置
        $catidsetting = unserialize($catidinfo['setting']);

        //前台投稿状态判断
        if (defined('IN_ADMIN') && IN_ADMIN == false) {
            //前台投稿，根据栏目配置和用户配置
            $Member_group = F("Member_group");
            $groupid = AppframeAction::$Cache['User']['groupid'];
            //如果会员组设置中设置，投稿不需要审核，直接无视栏目设置
            if ($Member_group[$groupid]['allowpostverify']) {
                $systeminfo['status'] = 99;
            } else {
                //前台投稿是否需要审核
                if ($catidsetting['member_check']) {
                    $systeminfo['status'] = 1;
                } else {
                    $systeminfo['status'] = 99;
                }
            }
        }

        //检查发表时间，如果有时间转换为时间戳
        if ($data['inputtime'] && !is_numeric($data['inputtime'])) {
            $systeminfo['inputtime'] = strtotime($data['inputtime']);
        } elseif (!$data['inputtime']) {
            $systeminfo['inputtime'] = time();
        } else {
            $systeminfo['inputtime'] = $data['inputtime'];
        }
        //读取模型字段配置中，关于日期配置格式，来组合日期数据
        $this->fields = F("Model_field_" . $this->modelid);
        $setting = unserialize($this->fields['inputtime']['setting']);
        //从数组中把变量导入到当前的符号表中
        extract($setting);
        if ($fieldtype == 'date') {
            $systeminfo['inputtime'] = date('Y-m-d');
        } elseif ($fieldtype == 'datetime') {
            $systeminfo['inputtime'] = date('Y-m-d H:i:s');
        }

        //更新时间处理
        if ($data['updatetime'] && !is_numeric($data['updatetime'])) {
            $systeminfo['updatetime'] = strtotime($data['updatetime']);
        } elseif (!$data['updatetime']) {
            $systeminfo['updatetime'] = time();
        } else {
            $systeminfo['updatetime'] = $data['updatetime'];
        }

        //添加用户名
        $systeminfo['username'] = $data['username'] ? $data['username'] : AppframeAction::$Cache['username'];
        //标识是否后台发布
        $systeminfo['sysadd'] = defined('IN_ADMIN') && IN_ADMIN ? 1 : 0;

        //自动提取摘要，如果有设置自动提取，且description为空，且有内容字段才执行
        if (isset($_POST['add_introduce']) && $systeminfo['description'] == '' && isset($modelinfo['content'])) {
            $content = stripslashes($modelinfo['content']);
            $introcude_length = intval($_POST['introcude_length']);
            $systeminfo['description'] = str_cut(str_replace(array("\r\n", "\t", '[page]', '[/page]', '&ldquo;', '&rdquo;', '&nbsp;'), '', strip_tags($content)), $introcude_length);
            $inputinfo['system']['description'] = $systeminfo['description'] = Input::getVar($systeminfo['description']);
        }

        //自动提取缩略图，从content 中提取
        if (isset($_POST['auto_thumb']) && $systeminfo['thumb'] == '' && isset($modelinfo['content'])) {
            $content = $content ? $content : stripslashes($modelinfo['content']);
            $auto_thumb_no = intval($_POST['auto_thumb_no']) - 1;
            if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
                $systeminfo['thumb'] = $matches[3][$auto_thumb_no];
            }
        }

        //主表操作开始
        $this->table_name = ucwords($this->Model[$this->modelid]['tablename']);
        $this->fbtable_name = $this->table_name . "_data";
        $this->Content = new ContentModel($this->table_name);
        //使用TP的自动验证，所以要把令牌合并
        $systeminfo = array_merge($systeminfo, array(C("TOKEN_NAME") => $_POST[C("TOKEN_NAME")]));
        //自动验证后的数据
        $newsdata = $this->Content->create($systeminfo);
        if (!$newsdata) {
            $this->Error = $this->Content->getError();
            return false;
        }
        //把副表的数据合并，按关联模型的需求合并
        $fdata = array(
            $this->fbtable_name => $modelinfo
        );
        $newsdata = array_merge($newsdata, $fdata);
        //插入成功返回ID
        $id = $modelinfo['id'] = $this->Content->relation(true)->data($newsdata)->add();
        $newsdata['id'] = $id;

        if (is_bool($id) && $id == false) {
            $this->Error = "信息录入失败！";
            return false;
        }

        //转向地址
        if ($data['islink'] == 1) {
            $urls['url'] = $_POST['linkurl'];
        } else {
            //生成该篇地址
            $urls = $this->url->show($newsdata);
        }
        //更新url
        $this->Content->where(array('id' => $id))->data(array('url' => $urls['url']))->save();
        //添加统计
        $this->hits_db = M("Hits");
        $hitsid = 'c-' . $systeminfo['catid'] . '-' . $id;
        $this->hits_db->add(array('hitsid' => $hitsid, 'modelid' => $this->modelid, 'catid' => $systeminfo['catid'], 'updatetime' => time()), array(), true);

        //更新到全站搜索
        if ($systeminfo['status'] == 99) {
            $this->search_api($id, $inputinfo);
        }

        //调用 update
        $content_update = new content_update($this->modelid, $id);
        //合并后，调用update
        $merge_data = array_merge($systeminfo, $modelinfo);
        $merge_data['url'] = $urls['url'];
        $merge_data['posid'] = $data['posid'];
        $content_update->update($merge_data);

        //发布到其他栏目,只能后台发布才可以使用该功能
        if (defined('IN_ADMIN') && IN_ADMIN) {
            if (is_array($_POST['othor_catid'])) {
                foreach ($_POST['othor_catid'] as $classid => $v) {
                    if ($this->catid == $classid) {
                        continue;
                    }
                    $othor_catid[] = $classid;
                }
                //去除重复
                $othor_catid = array_unique($othor_catid);
                $newsdata['url'] = $urls['url'];
                $this->othor_catid($othor_catid, $urls['url'], $newsdata, $this->modelid);
            }
        }

        //更新附件状态，把相关附件和文章进行管理
        $this->attachment_db = service("Attachment");
        $this->attachment_db->api_update('', 'c-' . $systeminfo['catid'] . '-' . $id, 2);

        //标签
        tag("content_add_end", $merge_data);

        //生成相关
        $generatelish = 0;
        if (defined('IN_ADMIN') && IN_ADMIN) {
            //是否生成内容页
            if ($catidsetting['generatehtml']) {
                //生成静态
                if ($catidsetting['content_ishtml'] && $newsdata['status'] == 99) {
                    $html->show($newsdata,0,'add');
                }
            }
            //生成列表
            if ((int) $catidsetting['generatelish'] > 0) {
                $generatelish = (int) $catidsetting['generatelish'];
            }
        } else {
            //投稿内容页生成，直接审核通过的直接生成内容页
            if ($newsdata['status'] == 99) {
                //生成静态
                if ($catidsetting['content_ishtml']) {
                    $html->show($newsdata,0,'add');
                }
            }
            //列表生成
            if ((int) $catidsetting['member_generatelish'] > 0) {
                $generatelish = (int) $catidsetting['member_generatelish'];
            }
        }
        //列表生成
        switch ($generatelish) {
            //生成当前栏目
            case 1:
                $html->create_relation_html($newsdata['catid']);
                break;
            //生成首页
            case 2:
                $html->index();
                break;
            //生成父栏目
            case 3:
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                break;
            //生成当前栏目与父栏目
            case 4:
                $html->category($newsdata['catid']);
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                break;
            //生成父栏目与首页
            case 5:
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                $html->index();
                break;
            //生成当前栏目、父栏目与首页
            case 6:
                $html->create_relation_html($newsdata['catid']);
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                $html->index();
                break;
        }
        //生成上一篇下一篇
        if ($newsdata['status'] == 99) {
            $this->related_content($newsdata['catid'], $id, "add");
        }
        return $id;
    }

    /**
     * 修改内容 
     * @param array $data 数据
     * @param type $id 信息ID
     * @return boolean 
     */
    public function edit($data, $id) {
        $data['id'] = $id;
        require_array(array(
            RUNTIME_PATH . 'content_input.class.php',
            RUNTIME_PATH . 'content_update.class.php'
        ));
        $this->catid = (int) $data['catid'];
        $this->modelid = $this->categorys[$this->catid]['modelid'];
        //主表操作开始
        $this->table_name = ucwords($this->Model[$this->modelid]['tablename']);
        $this->fbtable_name = $this->table_name . "_data";
        $this->Content = new ContentModel($this->table_name);

        $content_input = new content_input($this->modelid, $this);
        $inputinfo = $content_input->get($data);

        if (is_bool($inputinfo) && $inputinfo == false) {
            //显示错误
            $this->Error = $content_input->getError();
            return false;
        }

        //标签
        tag("content_edit_begin", $inputinfo);

        //主表字段内容
        $systeminfo = $inputinfo['system'];
        //副表字段内容
        $modelinfo = $inputinfo['model'];

        //栏目数据
        $catidinfo = $this->categorys[$systeminfo['catid']];
        //setting配置
        $catidsetting = unserialize($catidinfo['setting']);

        //前台投稿状态判断
        if (defined('IN_ADMIN') && IN_ADMIN == false) {
            //前台投稿编辑是否需要审核
            if ($catidsetting['member_editcheck']) {
                $systeminfo['status'] = 1;
            }
        }

        //取得inputtime的真实发布时间
        //inputtime为真实发表时间，不允许修改
        $data['inputtime'] = $systeminfo['inputtime'] = $inputtime = $this->Content->where(array("id" => $id))->getField("inputtime");

        //更新时间处理
        if ($data['updatetime'] && !is_numeric($data['updatetime'])) {
            $systeminfo['updatetime'] = strtotime($data['updatetime']);
        } elseif (!$data['updatetime']) {
            $systeminfo['updatetime'] = time();
        } else {
            $systeminfo['updatetime'] = $data['updatetime'];
        }

        //自动提取摘要，如果有设置自动提取，且description为空，且有内容字段才执行
        if (isset($_POST['add_introduce']) && $systeminfo['description'] == '' && isset($modelinfo['content'])) {
            $content = stripslashes($modelinfo['content']);
            $introcude_length = intval($_POST['introcude_length']);
            $systeminfo['description'] = str_cut(str_replace(array("\r\n", "\t", '[page]', '[/page]', '&ldquo;', '&rdquo;', '&nbsp;'), '', strip_tags($content)), $introcude_length);
            $inputinfo['system']['description'] = $systeminfo['description'] = Input::getVar($systeminfo['description']);
        }

        //自动提取缩略图，从content 中提取
        if (isset($_POST['auto_thumb']) && $systeminfo['thumb'] == '' && isset($modelinfo['content'])) {
            $content = $content ? $content : stripslashes($modelinfo['content']);
            $auto_thumb_no = intval($_POST['auto_thumb_no']) - 1;
            if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
                $systeminfo['thumb'] = $matches[3][$auto_thumb_no];
            }
        }

        //转向地址
        if ($data['islink'] == 1) {
            $systeminfo['url'] = $_POST['linkurl'];
        } else {
            //生成该篇地址
            $urls = $this->url->show($data);
            $systeminfo['url'] = $urls["url"];
        }

        //使用TP的自动验证，所以要把令牌合并
        $systeminfo = array_merge($systeminfo, array(C("TOKEN_NAME") => $_POST[C("TOKEN_NAME")]));

        $status = $this->Content->create($systeminfo);
        if (!$status) {
            $this->Error = $this->Content->getError();
            return false;
        }

        //把副表的数据合并，按关联模型的需求合并
        $status['id'] = $id;
        $fdata = array(
            $this->fbtable_name => $modelinfo
        );
        $status = array_merge($status, $fdata);
        $editdata = $status;
        $editdata['id'] = $id;
        //删除真实时间的信息，避免被更新
        unset($status['inputtime']);
        //数据修改，这里使用关联操作
        $this->Content->relation(true)->where(array('id' => $id))->save($status);

        //调用 update
        $content_update = new content_update($this->modelid, $id);
        //合并后，调用update
        $merge_data = array_merge($systeminfo, $modelinfo);
        $merge_data['url'] = $urls["url"];
        $merge_data['posid'] = $data['posid'];
        $content_update->update($merge_data);

        //更新附件状态，把相关附件和文章进行管理
        $this->attachment_db = service("Attachment");
        $this->attachment_db->api_update('', 'c-' . $systeminfo['catid'] . '-' . $id, 2);

        //更新到全站搜索
        if ($systeminfo['status'] == 99) {
            $this->search_api($id, $inputinfo, "updata");
        } else {
            $this->search_api($id, $inputinfo, "delete");
        }

        //标签
        tag("content_edit_end", $merge_data);

        //生成相关
        $generatelish = 0;
        import('Html');
        $html = new Html();
        if (defined('IN_ADMIN') && IN_ADMIN) {
            //是否生成内容页
            if ($catidsetting['generatehtml']) {
                //生成静态
                if ($catidsetting['content_ishtml'] && $status['status'] == 99) {
                    $html->show($editdata, 0,'edit');
                }
            }
            //如果是未审核，删除已经生成
            if ($catidsetting['content_ishtml'] && !$data['islink'] && $status['status'] == 1) {
                $this->deleteHtml($data['catid'], $id, $systeminfo['inputtime'], $systeminfo['prefix'], $data);
            }
            //生成列表
            if ((int) $catidsetting['generatelish'] > 0) {
                $generatelish = (int) $catidsetting['generatelish'];
            }
        } else {
            //投稿内容页生成，直接审核通过的直接生成内容页
            if ($status['status'] == 99) {
                //生成静态
                if ($catidsetting['content_ishtml']) {
                    $html->show($editdata, 0,'edit');
                }
            } else {
                if ($catidsetting['content_ishtml'] && !$data['islink']) {
                    $this->deleteHtml($data['catid'], $id, $systeminfo['inputtime'], $systeminfo['prefix'], $data);
                }
            }
            //列表生成
            if ((int) $catidsetting['member_generatelish'] > 0) {
                $generatelish = (int) $catidsetting['member_generatelish'];
            }
        }
        //列表生成
        switch ($generatelish) {
            //生成当前栏目
            case 1:
                $html->create_relation_html($status['catid']);
                break;
            //生成首页
            case 2:
                $html->index();
                break;
            //生成父栏目
            case 3:
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                break;
            //生成当前栏目与父栏目
            case 4:
                $html->create_relation_html($status['catid']);
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                break;
            //生成父栏目与首页
            case 5:
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                $html->index();
                break;
            //生成当前栏目、父栏目与首页
            case 6:
                $html->create_relation_html($status['catid']);
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                $html->index();
                break;
        }
        //生成上一篇下一篇
        $this->related_content($status['catid'], $id, "edit");
        return true;
    }

    /**
     * 删除内容
     * @param $id 内容id
     * @param $catid 栏目id
     * @param $file 文件路径
     */
    public function delete($id, $catid, $file = '') {
        require_cache(RUNTIME_PATH . 'content_delete.class.php');
        //模型ID
        $this->modelid = $modelid = $this->categorys[$catid]['modelid'];
        //是否生成HTML
        $sethtml = $this->categorys[$catid]['sethtml'];
        //栏目配置信息
        $setting = unserialize($this->categorys[$catid]['setting']);
        $content_ishtml = $setting['content_ishtml'];
        $this->table_name = ucwords($this->Model[$this->modelid]['tablename']);
        $this->Content = new ContentModel($this->table_name);
        //删除附件
        $Attachment = service("Attachment");

        $r = $this->Content->relation(true)->where(array("id" => $id))->find();
        if ($content_ishtml && !$r['islink']) {
            $this->deleteHtml($catid, $id, $r['inputtime'], $r['prefix'], $r);
        }
        //删除内容
        $this->Content->relation(true)->where(array('id' => $id))->delete();
        //调用 content_delete
        $content_update = new content_delete($id, $catid, $modelid);
        $data = array_merge($r, $r[$this->table_name . '_data']);
        unset($data[$this->table_name . '_data']);
        $content_update->get($data);
        //删除统计
        M("Hits")->where(array("hitsid" => "c-" . $catid . "-" . $id))->delete();
        //删除评论
        $comment_id = "c-$catid-$id";
        D('Comments')->deleteCommentsMark($comment_id);
        $Attachment->api_delete('c-' . $catid . '-' . $id);
        //删除对应的会员投稿记录信息
        M("MemberContent")->where(array("content_id" => $id, "catid" => $catid))->delete();
        //删除全站搜索数据
        $this->search_api($id, $r, "delete");
        //标签
        tag("content_delete_end", $r);

        return true;
    }

    /**
     * 同步发布
     * @param type $othor_catid 需要同步发布到的栏目ID
     * @param type $linkurl 原信息地址
     * @param type $data 原数据，以关联表的数据格式
     * @param type $modelid 原信息模型ID
     * @return boolean
     */
    public function othor_catid($othor_catid, $linkurl, $data, $modelid) {
        //数据检测
        if (!$linkurl || !$othor_catid || !$data || !$modelid) {
            return false;
        }
        //去除ID
        unset($data['id']);
        import('Html');
        $html = new Html();
        if (!is_object($this->url)) {
            import('Url');
            $this->url = new Url();
        }
        if (!is_object($this->hits_db)) {
            $this->hits_db = M("Hits");
        }
        if (!is_object($this->Content)) {
            $table_name = ucwords($this->Model[$modelid]['tablename']);
            $this->Content = new ContentModel($table_name);
        }

        //循环需要同步发布的栏目
        foreach ($othor_catid as $cid) {
            //获取需要同步栏目所属模型ID
            $mid = $this->categorys[$cid]['modelid'];
            //判断模型是否相同
            if ($modelid == $mid) {//相同
                $data['catid'] = $cid;
                $_categorys = $this->categorys[$data['catid']];
                $_categorys['setting'] = unserialize($_categorys['setting']);
                //修复当被推送的文章是推荐位的文章时，推送后会把相应属性也推送过去
                $data['posid'] = 0;
                $newid = $this->Content->relation(true)->data($data)->add();
                if (!$newid) {
                    continue;
                }
                $othordata = $data;
                $othordata['id'] = $newid;
                //更新URL地址
                if ((int) $othordata['islink'] == 1) {
                    $nurls = $othordata['url'];
                    //更新地址
                    $this->Content->where(array('id' => $newid))->data(array('url' => $nurls))->save();
                } else {
                    $nurls = $this->url->show($othordata);
                    //更新地址
                    $this->Content->where(array('id' => $newid))->data(array('url' => $nurls['url']))->save();
                }
                if (is_array($nurls) && $_categorys['setting']['content_ishtml'] && $othordata['status'] == 99) {
                    //生成静态
                    $html->show($othordata, 0, "add");
                }
            } else {//不相同
                //不同模型，则以链接的形式添加，也就是转向地址
                $dataarray = array('title' => $data['title'],
                    'style' => $data['style'],
                    'thumb' => $data['thumb'],
                    'keywords' => $data['keywords'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'catid' => $cid,
                    'url' => $linkurl,
                    'sysadd' => 1,
                    'username' => $data['username'],
                    'inputtime' => $data['inputtime'],
                    'updatetime' => $data['updatetime'],
                    'islink' => 1
                );
                $table_name = ucwords($this->Model[$mid]['tablename']);
                $newid = M($table_name)->data($dataarray)->add();
                //副表
                if ($newid) {
                    M($table_name . "_data")->data(array("id" => $newid))->add();
                }
            }
            //添加统计
            $hitsid = 'c-' . $cid . '-' . $newid;
            $this->hits_db->add(array('hitsid' => $hitsid, 'modelid' => $mid, 'catid' => $cid, 'updatetime' => time()), array(), true);
        }
        return true;
    }

    /**
     * 信息审核
     * @param type $catid 栏目ID
     * @param type $id 信息ID
     * @param type $status 1为未审核，99为审核通过
     * @return boolean 
     */
    public function check($catid, $id, $status = 99) {
        //模型ID
        $this->modelid = $modelid = $this->categorys[$catid]['modelid'];
        //是否生成HTML
        $sethtml = $this->categorys[$catid]['sethtml'];
        //栏目配置信息
        $setting = unserialize($this->categorys[$catid]['setting']);
        $content_ishtml = $setting['content_ishtml'];
        $this->table_name = ucwords($this->Model[$this->modelid]['tablename']);
        $this->Content = new ContentModel($this->table_name);
        //组合数据
        $inputinfo = array();
        $inputinfo['system'] = $r = $this->Content->relation(true)->where(array('id' => $id, 'catid' => $catid))->find();
        $inputinfo['model'] = $r[$this->table_name . "_data"];
        if ($r) {
            if ($this->Content->where(array('id' => $id, 'catid' => $catid))->save(array("status" => $status))) {
                //判断是否前台投稿
                if ($r['sysadd'] == 0 && $status == 99) {
                    //检查是否已经赠送过积分
                    $integral = M("MemberContent")->where(array("content_id" => $id, "catid" => $catid))->getField("integral");
                    if (!$integral) {
                        if (service("Passport")->user_integral($r['username'], $setting['member_addpoint'])) {
                            M("MemberContent")->where(array("content_id" => $id, "catid" => $catid))->save(array("integral" => 1));
                        }
                    }
                }
                //生成该篇地址
                $urls = $this->url->show($r);
                //生成内容页
                if ($content_ishtml && !$r['islink'] && $status == 99) {
                    import('Html');
                    $html = new Html();
                    $html->show($r, 0, 'edit');
                    //生成上下篇
                    $this->related_content($catid, $id);
                }
                //如果是取消审核，则删除生成静态的文件
                if ($content_ishtml && $status == 1) {
                    $this->deleteHtml($catid, $id, $r['inputtime'], $r['prefix'], $r);
                    //删除全站搜索数据
                    $this->search_api($id, $r, "delete");
                } elseif ($status == 99) {
                    $this->search_api($id, $inputinfo);
                }
            }
        }
        return false;
    }

    /**
     * 删除静态生成的文章文件 
     * @param type $catid 栏目ID
     * @param type $id 信息ID
     * @param type $inputtime 真实发布时间
     * @param type $prefix 自定义文件名
     * @return type 
     */
    public function deleteHtml($catid, $id, $inputtime, $prefix = '', $data = false) {
        if ($data == false) {
            //模型ID
            $this->modelid = $this->categorys[$catid]['modelid'];
            $this->table_name = ucwords($this->Model[$this->modelid]['tablename']);
            $this->Content = new ContentModel($this->table_name);
            $data = $this->Content->relation(true)->where(array('id' => $id, 'catid' => $catid))->find();
        }
        //获取信息生成地址和url
        $urls = $this->url->show($data);
        $fileurl = $urls['path'];
        //删除静态文件
        $lasttext = strrchr($fileurl, '.');
        $len = -strlen($lasttext);
        $path = substr($fileurl, 0, $len);
        $path = ltrim($path, '/');
        $filelist = glob(SITE_PATH . "/" . $path . '*');
        foreach ($filelist as $delfile) {
            $lasttext = strrchr($delfile, '.');
            if (!in_array($lasttext, array('.htm', '.html', '.shtml')))
                continue;
            @unlink($delfile);
        }
        return true;
    }

    /**
     * 上下篇生成
     * @param type $catid
     * @param type $id 
     */
    public function related_content($catid, $id, $action = "edit") {
        if (!$catid || !$id) {
            return;
        }
        $categorys = $this->categorys;
        $Model = $this->Model;
        $modelid = $categorys[$catid]['modelid'];

        $tabname = $Model[$modelid]['tablename'];
        $db = M(ucwords($tabname));
        $where = array();
        $where['catid'] = $catid;
        $where['status'] = array("EQ", "99");
        $where['id'] = array("LT", $id);
        $data[] = $db->where($where)->order(array("id" => "DESC"))->find();
        if ($action == "edit") {
            $where['id'] = array("GT", $id);
            $data[] = $db->where($where)->find();
        }
        $db = NULL;
        import('Html');
        $html = new Html();

        foreach ($data as $r) {
            if ($r['islink'] || empty($r['id']))
                continue;
            $setting = unserialize($categorys[$r['catid']]['setting']);
            $content_ishtml = $setting['content_ishtml'];
            if (!$content_ishtml) {
                continue;
            }
            $fdb = M(ucwords($tabname) . "_data");
            $r2 = $fdb->where(array('id' => $r['id']))->find();
            if ($r2)
                $r = array_merge($r, $r2);
            $urls = $this->url->show($r);
            $html->show($r, 1, 'edit');
            $fdb = NULL;
        }

        return true;
    }

    /**
     * 更新搜索数据
     * @param type $id 信息id
     * @param type $data 数据
     * @param type $action 动作
     */
    private function search_api($id = 0, $data = array(), $action = 'add') {
        $App = F("App");
        if (!in_array("Search", $App)) {
            return false;
        }
        //检查当前模型是否有在搜索数据源中
        $searchConfig = F("Search_config");
        if(!in_array($this->modelid,$searchConfig['modelid'])){
            return false;
        }
        $db = D("Search/Search");
        return $db->search_api($id, $data, $this->modelid, $action);
    }

}

?>
