<?php

/* * 
 * 搜索管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class SearchAction extends AdminbaseAction {

    public $config;
    private $db;

    function _initialize() {
        parent::_initialize();
        $this->db = D("Search");
        $this->config = F("Search_config");
        if (!$this->config) {
            $this->db->search_cache();
            $this->config = F("Search_config");
        }
    }

    //搜索相关配置
    public function index() {
        if (IS_POST) {
            $setting = $_POST['setting'];
            if ($this->db->search_config($setting) !== false) {
                $this->success("配置修改成功！");
            } else {
                $this->error("配置修改失败！");
            }
        } else {
            $model = F("Model");
            $this->assign("model_list", $model);
            $this->assign("config", $this->config);
            $this->display();
        }
    }
    
    //搜索关键词记录
    public function searchot(){
        $db = M("SearchKeyword");
        if(IS_POST){
            $keyword = $this->_post("keyword");
            if($keyword && is_array($keyword)){
                foreach($keyword as $k){
                    $db->where(array("keyword"=>$k))->delete();
                }
            }
            $this->success("操作成功！");
        }else{
            $count = $db->count();
            $page = $this->page($count, 20);
            $data = $db->limit($page->firstRow . ',' . $page->listRows)->order(array("searchnums"=>"DESC"))->select();
            $this->assign("data",$data);
            $this->assign("Page",$page->show('Admin'));
            $this->display();
        }
    }

    //重建索引
    public function create() {
        unset($_GET['_URL_']);
        if (isset($_GET['start'])) {
            //每轮更新数
            $pagesize = (int) $this->_get("pagesize");
            $_GET['pagesize'] = $pagesize = $pagesize > 1 ? $pagesize : 100;
            //模型
            $_GET['modelid'] = $modelid = (int) $this->_get("modelid");
            //第几轮更新
            $page = $_GET['start'] = (int) $this->_get("start");
            //总共几轮
            $pages = (int) $this->_get("pages");
            //信息总数
            $total = (int) $this->_get("total");

            $model = F("Model");
            //如果是重建所有模型
            if ($modelid) {
                $table_name = ucwords($model[$modelid]['tablename']);
                if (!$table_name) {
                    $this->error("该模型不存在！");
                }
                $ContentDb = new ContentModel($table_name);

                if (!in_array($modelid, $this->config['modelid'])) {
                    $this->error("该模型无需重建！");
                }
                //取得总数
                if (!isset($_GET['total'])) {
                    $count = $ContentDb->where(array("status" => 99))->count();
                    //信息总数
                    $total = $_GET['total'] = $count;
                    //总共几轮
                    $pages = $_GET['pages'] = ceil($_GET['total'] / $pagesize);
                    //初始第一轮更新
                    $page = $_GET['start'] = 1;
                }

                $page = max(intval($page), 1);
                $offset = $pagesize * ($page - 1);

                $data = $ContentDb->relation(true)->where(array("status" => 99))->order(array("id" => "ASC"))->limit($offset . "," . $pagesize)->select();
                if (!$data) {
                    $data = array();
                }
                //数据处理
                foreach ($data as $r) {
                    //组合数据
                    $inputinfo = array();
                    $inputinfo['system'] = $r;
                    $inputinfo['model'] = $r[$table_name . "_data"];
                    $id = $r['id'];
                    $this->db->search_api($id, $inputinfo, $modelid);
                }

                if ($pages == $page || $page > $pages) {
                    $this->success("更新完成！ ...", U("Search/create"));
                    exit;
                }

                if ($pages > $page) {
                    $page++;
                    $_GET['start'] = $page;
                    $creatednum = $offset + count($data);
                    $percent = round($creatednum / $total, 2) * 100;
                    $message = "有 <font color=\"red\">{$total}</font> 条信息 - 已完成 <font color=\"red\">{$creatednum}</font> 条（<font color=\"red\">{$percent}%</font>）";
                    $forward = U("Search/create", $_GET);
                    $this->assign("waitSecond", 200);
                    $this->success($message, $forward);
                    exit;
                } 
            } else {
                //当没有选择模型更新时，进行全部可用模型数据更新
                $modelArr = $this->config['modelid'];
                $autoid = $this->_get("autoid");
                $autoid = $_GET['autoid'] ? intval($_GET['autoid']) : 0;
                if (!isset($modelArr[$autoid])) {
                    $this->success("更新完成！ ...", U("Search/create"));
                    exit;
                }
                $modelid = $modelArr[$autoid];
                $table_name = ucwords($model[$modelid]['tablename']);
                if (!$table_name) {
                    $this->error("该模型不存在！");
                }
                $ContentDb = new ContentModel($table_name);

                //取得总数
                if (!isset($_GET['total'])) {
                    $count = $ContentDb->where(array("status" => 99))->count();
                    //信息总数
                    $total = $_GET['total'] = $count;
                    //总共几轮
                    $pages = $_GET['pages'] = ceil($_GET['total'] / $pagesize);
                    //初始第一轮更新
                    $page = $_GET['start'] = 1;
                }
                $page = max(intval($page), 1);
                $offset = $pagesize * ($page - 1);

                $data = $ContentDb->relation(true)->where(array("status" => 99))->order(array("id" => "ASC"))->limit($offset . "," . $pagesize)->select();
                if (!$data) {
                    $data = array();
                }
                //数据处理
                foreach ($data as $r) {
                    //组合数据
                    $inputinfo = array();
                    $inputinfo['system'] = $r;
                    $inputinfo['model'] = $r[$table_name . "_data"];
                    $id = $r['id'];
                    $this->db->search_api($id, $inputinfo, $modelid);
                }

                if ($pages == $page || $page > $pages) {
                    $autoid++;
                    $_GET['autoid'] = $autoid;
                    unset($_GET['total']);
                    $this->assign("waitSecond", 200);
                    $this->success("模型【" . $model[$modelid]['name'] . "】更新完成 ...", U("Search/create", $_GET));
                    exit;
                }

                if ($pages > $page) {
                    $page++;
                    $_GET['start'] = $page;
                    $creatednum = $offset + count($data);
                    $percent = round($creatednum / $total, 2) * 100;
                    $message = "【" . $model[$modelid]['name'] . "】有 <font color=\"red\">{$total}</font> 条信息 - 已完成 <font color=\"red\">{$creatednum}</font> 条（<font color=\"red\">{$percent}%</font>）";
                    $forward = U("Search/create", $_GET);
                    $this->assign("waitSecond", 200);
                    $this->success($message, $forward);
                    exit;
                } 
            }
        } else {
            if (IS_POST) {
                //每轮更新数
                $pagesize = (int) $this->_post("pagesize");
                $pagesize = $pagesize > 1 ? $pagesize : 100;
                //模型
                $modelid = (int) $this->_post("modelid");
                if ($modelid) {
                    //删除旧的搜索数据
                    $this->db->where(array("modelid" => $modelid))->delete();
                } else {
                    //删除旧的搜索数据
                    $this->db->emptyTable();
                }
                $this->success("开始进行索引重建...", U("Search/create", array("start" => 1, "pagesize" => $pagesize, "modelid" => $modelid)));
            } else {
                $model = F("Model");
                $this->assign("models", $model);
                $this->assign("config", $this->config);
                $this->display();
            }
        }
    }

}

?>
