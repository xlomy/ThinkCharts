<?php

/**
 * TAGS整理，增加到TAG表
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
function tags($field, $value) {
    if (!empty($value)) {
        $db = M("Tags");
        $time = time();
        if (strpos($value, ',') === false) {
            $keyword = explode(' ', $value);
        } else {
            $keyword = explode(',', $value);
        }
        $keyword = array_unique($keyword);
        //新增
        if (ACTION_NAME == 'add') {
            D("Tags")->addTag($keyword, $this->id, $this->catid, $this->modelid, array(
                "url" => $this->data['url'],
                "title" => $this->data['title'],
            ));
        } else {
            D("Tags")->updata($keyword, $this->id, $this->catid, $this->modelid, array(
                "url" => $this->data['url'],
                "title" => $this->data['title'],
            ));
        }
    } else {
        //删除全部tags信息
        D("Tags")->deleteAll($this->id, $this->catid, $this->modelid);
    }
}

?>
