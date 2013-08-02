<?php

/* * 
 * 搜索
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class IndexAction extends BaseAction {

    public $config;

    function _initialize() {
        parent::_initialize();
        $this->config = F("Search_config");
        if (!$this->config) {
            D("Search")->search_cache();
            $this->config = F("Search_config");
        }
    }

    public function index() {
        C('TOKEN_ON', false);
        $seo = seo();
        $this->assign("seo", $seo);
        if (I("request.q")) {
            G('search');
            //关键字
            $q = Input::forSearch(safe_replace(I("request.q")));
            $q = htmlspecialchars(strip_tags($q));
            if (trim($q) == '') {
                header('Location: ' . U("Search/Index/index"));
                exit;
            }
            if (IS_POST) {
                header('Location: ' . U("Search/Index/index", array("q" => $q)));
                exit;
            }
            //时间范围
            $time = $this->_get("time");
            //模型
            $mid = (int) $this->_get("modelid");
            //栏目
            $catid = (int) $this->_get("catid");
            //排序
            $order = array("adddate" => "DESC", "searchid" => "DESC");
            //搜索历史记录
            $shistory = cookie("shistory");
            if (!$shistory) {
                $shistory = array();
            }
            $model = F("Model");
            $category = F("Category");
            array_unshift($shistory, $q);
            $shistory = array_slice(array_unique($shistory), 0, 10);
            //加入搜索历史
            cookie("shistory", $shistory);

            $where = array();
            //每页显示条数
            $pagesize = $this->config['pagesize'] ? $this->config['pagesize'] : 10;
            //缓存时间
            $cachetime = (int) $this->config['cachetime'];

            //按时间搜索
            if ($time == 'day') {//一天
                $search_time = time() - 86400;
                $where['adddate'] = array("GT", $search_time);
            } elseif ($time == 'week') {//一周
                $search_time = time() - 604800;
                $where['adddate'] = array("GT", $search_time);
            } elseif ($time == 'month') {//一月
                $search_time = time() - 2592000;
                $where['adddate'] = array("GT", $search_time);
            } elseif ($time == 'year') {//一年
                $search_time = time() - 31536000;
                $where['adddate'] = array("GT", $search_time);
            } else {
                $search_time = 0;
            }

            //可用数据源
            $this->config['modelid'] = $this->config['modelid'] ? $this->config['modelid'] : array();

            //按模型搜索
            if ($mid && in_array($mid, $this->config['modelid'])) {
                $where['modelid'] = array("EQ", (int) $mid);
            }

            //按栏目搜索
            if ($catid) {
                //不支持多栏目搜索，和父栏目搜索。
                $where['catid'] = array("EQ", (int) $catid);
            }

            //分页模板
            $TP = '共有{recordcount}条信息&nbsp;{pageindex}/{pagecount}&nbsp;{first}{prev}{liststart}{list}{listend}{next}{last}';

            //如果开启sphinx
            if ($this->config['sphinxenable']) {
                import("Sphinxapi", APP_PATH . C("APP_GROUP_PATH") . '/Search/Class/');
                $sphinxhost = $this->config['sphinxhost'];
                $sphinxport = $this->config['sphinxport'];
                $cl = new SphinxClient ();
                //设置searchd的主机名和TCP端口
                $cl->SetServer($sphinxhost, $sphinxport);
                //设置连接超时
                $cl->SetConnectTimeout(1);
                //控制搜索结果集的返回格式
                $cl->SetArrayResult(true);
                //设置全文查询的匹配模式 api http://docs.php.net/manual/zh/sphinxclient.setmatchmode.php
                $cl->SetMatchMode(SPH_MATCH_EXTENDED2);
                //设置排名模式 api http://docs.php.net/manual/zh/sphinxclient.setrankingmode.php
                $cl->SetRankingMode(SPH_RANK_PROXIMITY_BM25);
                //按一种类似SQL的方式将列组合起来，升序或降序排列。用weight是权重排序
                $cl->SetSortMode(SPH_SORT_EXTENDED, "@weight desc");
                //设置返回结果集偏移量和数目
                $page = (int) $this->_get(C("VAR_PAGE"));
                $page = $page < 1 ? 1 : $page;
                $offset = $pagesize * ($page - 1);
                $cl->SetLimits($offset, $pagesize, ($pagesize > 1000) ? $pagesize : 1000);
                if (in_array($time, array("day", "week", "month", "year"))) {
                    //过滤时间
                    $cl->SetFilterRange('adddate', $search_time, time(), false);
                }
                if ($mid && in_array($mid, $this->config['modelid'])) {
                    //过滤模型
                    $cl->SetFilter('modelid', array((int)$mid));
                }
                if ($catid) {
                    //过滤栏目
                    $cl->SetFilter('catid', array((int)$catid));
                }
                //执行搜索 api http://docs.php.net/manual/zh/sphinxclient.query.php
                $res = $cl->Query($q, "*");
                //信息总数
                $count = $res['total'];
                //如果结果不为空
                if (!empty($res['matches'])) {
                    $result_sphinx = $res['matches'];
                }
                $result = array();
                //数组重新组合
                foreach ($result_sphinx as $k => $v) {
                    $result[$k] = array(
                        "searchid" => $v['id'],
                        "adddate" => $v['attrs']['adddate'],
                        "catid" => $v['attrs']['catid'],
                        "id" => $v['attrs']['id'],
                        "modelid" => $v['attrs']['modelid'],
                    );
                }
                $words = array();
                //搜索关键字
                foreach ($res['words'] as $k => $v) {
                    $words[] = $k;
                }
                $page = page($count, $pagesize);
                $page->SetPager('default', $TP, array("listlong" => "6", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
                $this->assign("Page", $page->show('default'));
            } else {
                import("Segment", APP_PATH . C("APP_GROUP_PATH") . '/Search/Class/');
                $Segment = new Segment();
                //分词结果
                $segment_q = $Segment->get_keyword($Segment->split_result($q));
                if (!empty($segment_q) && $this->config['segment']) {
                    $words = explode(" ", $segment_q);
                    $where['_string'] = " MATCH (`data`) AGAINST ('$segment_q' IN BOOLEAN MODE) ";
                } else {
                    //这种搜索最不行
                    $where['data'] = array('like', "%{$q}%");
                    $words = array($q);
                }
                //查询结果缓存
                if ($cachetime) {
                    //统计
                    $count = M("Search")->cache(true, $cachetime)->where($where)->count();
                    $page = page($count, $pagesize);
                    $result = M("Search")->cache(true, $cachetime)->where($where)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
                } else {
                    $count = M("Search")->where($where)->count();
                    $page = $this->page($count, $pagesize);
                    $result = M("Search")->where($where)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
                }
                $page->SetPager('default', $TP, array("listlong" => "6", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
                $this->assign("Page", $page->show('default'));
            }

            //搜索结果处理
            if ($result && is_array($result)) {
                foreach ($result as $k => $r) {
                    $modelid = $r['modelid'];
                    $id = $r['id'];
                    $tablename = ucwords($model[$modelid]['tablename']);
                    if ($tablename) {
                        $result[$k] = M($tablename)->where(array("id" => $id))->find();
                    }
                }
            }
            //搜索记录
            if (strlen($q) < 17 && strlen($q) > 1 && $result) {
                $res = M("SearchKeyword")->where(array('keyword' => $q))->find();
                if ($res) {
                    //关键词搜索数+1
                    M("SearchKeyword")->where(array('keyword' => $q))->setInc("searchnums");
                } else {
                    //关键词转换为拼音
                    load("@.iconvfunc");
                    $pinyin = gbk_to_pinyin(iconv('utf-8', 'gbk//IGNORE', $q));
                    if (is_array($pinyin)) {
                        $pinyin = implode('', $pinyin);
                    }
                    M("SearchKeyword")->add(array('keyword' => $q, 'searchnums' => 1, 'data' => $segment_q, 'pinyin' => $pinyin));
                }
            }
            //相关搜索功能
            if ($this->config['relationenble']) {
                $map = array();
                //相关搜索
                if (!empty($segment_q)) {
                    $relation_q = str_replace(' ', '%', $segment_q);
                } else {
                    $relation_q = $q;
                }
                $map['_string'] = " MATCH (`data`) AGAINST ('%$relation_q%' IN BOOLEAN MODE) ";
                $relation = M("SearchKeyword")->where($map)->select();

                $this->assign("relation", $relation);
            }

            foreach ($this->config['modelid'] as $modelid) {
                $source[$modelid] = array(
                    "name" => $model[$modelid]['name'],
                    "modelid" => $modelid
                );
            }
            //搜索结果
            $this->assign("result", $result);
            //运行时间
            $search_time = G('search', 'end', 6);
            $this->assign("count", $count ? $count : 0);
            $this->assign("search_time", $search_time);
            $this->assign("keyword", $q);
            $this->assign("category", $category);
            $this->assign("source", $source);
            $this->assign("time", $time);
            $this->assign("modelid", $mid);
            $this->assign("shistory", $shistory);
            //分词后的搜索关键字
            $this->assign("words", $words);
            $this->display("search");
        } else {
            $this->display();
        }
    }

}

?>
