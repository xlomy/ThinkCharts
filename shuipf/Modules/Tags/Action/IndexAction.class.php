<?php

/**
 * TAGS
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class IndexAction extends BaseAction {

    function _initialize() {
        //手动指定模块
        define("GROUP_MODULE", "Contents");
        parent::_initialize();
    }

    public function index() {
        //URL规则
        $urlrules = F('urlrules');
        $where = array();
        if (isset($_GET['tagid'])) {
            $where['tagid'] = array("EQ", (int) $this->_get("tagid"));
            $r = M("Tags")->where($where)->find();
            $where['tag'] = $r['tag'];
            unset($where['tagid']);
        } else {
            $where['tag'] = array("EQ", $this->_get("tag"));
        }
        
        $data = M("Tags")->where($where)->find();
        if (!$data) {
            $this->error("该Tag不存在！");
        }
        $this->assign($data);
        M("Tags")->where($where)->setInc("hits");
        M("Tags")->where($where)->save(array("lasthittime" => time()));
        $page = $this->_get(C("VAR_PAGE"));
        $this->assign("page", $page);
        $urlrules = $urlrules[CONFIG_TAGURL];
        define('URLRULE', str_replace('|', '~', str_replace(array('{$tag}', '{$tagid}'), array($this->_get('tag'), (int) $this->_get("tagid")), $urlrules)));
        $SEO = seo();
        //seo分配到模板
        $this->assign("SEO", $SEO);
        //把分页分配到模板
        $this->assign(C("VAR_PAGE"), $page);
        $this->display("Tags:tag");
    }

}

?>
