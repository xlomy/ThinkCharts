<?php

/* * 
 * Search搜索模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class SearchModel extends CommonModel {

    /**
     * 生成缓存
     * @return boolean
     */
    public function search_cache() {
        $Search = M("Module")->where(array("module" => "Search"))->find();
        if (!$Search) {
            return false;
        }
        $Search['setting'] = unserialize($Search['setting']);
        $Search['setting']['relationenble'] = isset($Search['setting']['relationenble'])?$Search['setting']['relationenble']:1;
        $Search['setting']['segment'] = isset($Search['setting']['segment'])?$Search['setting']['segment']:1;
        $Search['setting']['pagesize'] = isset($Search['setting']['pagesize'])?$Search['setting']['pagesize']:10;
        $Search['setting']['cachetime'] = isset($Search['setting']['cachetime'])?$Search['setting']['cachetime']:0;
        $Search['setting']['sphinxenable'] = isset($Search['setting']['sphinxenable'])?$Search['setting']['sphinxenable']:0;
        F("Search_config", $Search['setting']);
        return false;
    }

    /**
     * 更新搜索配置
     * @param type $config 配置数据
     * @return boolean 成功返回true
     */
    public function search_config($config) {
        if (!$config || !is_array($config)) {
            return false;
        }
        $status = M("Module")->where(array("module" => "Search"))->save(array("setting" => serialize($config)));
        if ($status !== false) {
            $this->search_cache();
            return true;
        }
        return false;
    }

    //清空表
    public function emptyTable() {
        //删除旧的搜索数据
        $DB_PREFIX = C("DB_PREFIX");
        $this->execute("DELETE FROM `{$DB_PREFIX}search`");
        $this->execute("ALTER TABLE `{$DB_PREFIX}search` AUTO_INCREMENT=1");
    }

    /**
     *  数据处理
     * @param type $data 数据
     * @return type
     */
    private function dataHandle($data, $text = '') {
        if (!$data) {
            return $data;
        }
        import("Input");
        $data = addslashes($data);
        $data = strip_tags($data);
        $data = str_replace(array(" ", "\r\t"), array(""), $data);
        $data = Input::forSearch($data);
        $data = Input::deleteHtmlTags($data);
        $config = F("Search_config");
        if (!$config) {
            $this->search_cache();
            $config = F("Search_config");
        }
        //判断是否启用sphinx全文索引，如果不是，则进行php简易分词处理
        if ((int) $config['sphinxenable'] == 0 && $config['segment']) {
            static $Segment;
            if (!isset($Segment)) {
                import("Segment", APP_PATH . C("APP_GROUP_PATH") . '/Search/Class/');
                $Segment = new Segment();
            }
            $fulltext_data = $Segment->get_keyword($Segment->split_result($data));
            $data = $text. " ". $fulltext_data;
        }
        return $data;
    }

    /**
     * 添加搜索数据
     * @param type $id 信息id
     * @param type $catid 栏目id
     * @param type $modelid 模型id
     * @param type $inputtime 发布时间
     * @param type $data 数据
     * @return boolean
     */
    public function searchAdd($id, $catid, $modelid, $inputtime, $data, $text = '') {
        if (!$id || !$catid || !$modelid || !$data) {
            return false;
        }
        //发布时间
        $inputtime = $inputtime ? (int) $inputtime : time();
        $data = $this->dataHandle($data, $text);
        $searchid = $this->add(array(
            "id" => $id,
            "catid" => $catid,
            "modelid" => $modelid,
            "adddate" => $inputtime,
            "data" => $data,
                ));
        if ($searchid !== false) {
            return $searchid;
        }
        return false;
    }

    /**
     * 更新搜索数据
     * @param type $id 信息id
     * @param type $catid 栏目id
     * @param type $modelid 模型id
     * @param type $inputtime 发布时间
     * @param type $data 数据
     * @return boolean
     */
    public function searchSave($id, $catid, $modelid, $inputtime, $data, $text = '') {
        if (!$id || !$catid || !$modelid || !$data) {
            return false;
        }
        //发布时间
        $inputtime = $inputtime ? (int) $inputtime : time();
        $data = $this->dataHandle($data, $text);
        $searchid = $this->where(array(
                    "id" => $id,
                    "catid" => $catid,
                    "modelid" => $modelid,
                ))->save(array(
            "adddate" => $inputtime,
            "data" => $data,
                ));
        if ($searchid !== false) {
            return $searchid;
        }
        return false;
    }

    /**
     * 删除搜索数据
     * @param type $id 信息id
     * @param type $catid 栏目id 
     * @param type $modelid 模型id
     * @return boolean
     */
    public function searchDelete($id, $catid, $modelid) {
        if (!$id || !$catid || !$modelid) {
            return false;
        }
        return $this->where(array(
                    "id" => $id,
                    "catid" => $catid,
                    "modelid" => $modelid,
                ))->delete();
    }

    /**
     * 更新搜索数据 api 接口
     * @param type $id 信息id
     * @param type $data 数据 数据分为 system，和model
     * @param type $modelid 模型id
     * @param type $action 动作
     */
    public function search_api($id = 0, $data = array(), $modelid, $action = 'add') {
        $fulltextcontent = "";
        //更新动作
        if ($action == 'add' || $action == 'updata') {
            //取得模型字段
            $fulltext_array = F('Model_field_' . $modelid);
            if (!$fulltext_array) {
                $fulltext_array = array();
            }
            foreach ($fulltext_array AS $key => $value) {
                //作为全站搜索信息
                if ((int) $value['isfulltext']) {
                    $fulltextcontent .= $data['system'][$key] ? $data['system'][$key] : $data['model'][$key];
                }
            }
            $fulltextcontent .= $data['system']['title'] . $data['system']['keywords'];
            //添加到搜索数据表
            $inputtime = (int) $data['system']['inputtime'];
            $catid = (int) $data['system']['catid'];
            if ($action == 'add') {
                $this->searchAdd($id, $catid, $modelid, $inputtime, $fulltextcontent, $data['system']['title'] . $data['system']['keywords']);
            } elseif ($action == 'updata') {
                //判断是否有数据，如果没有，变成add
                if ($this->where(array("id" => $id, "catid" => $catid, "modelid" => $modelid))->count()) {
                    $this->searchSave($id, $catid, $modelid, $inputtime, $fulltextcontent, $data['system']['title'] . $data['system']['keywords']);
                } else {
                    $this->searchAdd($id, $catid, $modelid, $inputtime, $fulltextcontent, $data['system']['title'] . $data['system']['keywords']);
                }
            }
        } elseif ($action == 'delete') {//删除动作
            $catid = $data['system']['catid'] ? $data['system']['catid'] : $data['catid'];
            $this->searchDelete($id, $catid, $modelid);
        }
    }

}

?>
