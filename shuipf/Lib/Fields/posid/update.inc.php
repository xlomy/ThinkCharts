<?php

//推荐位数据处理
function posid($field, $value) {
    if (!empty($value) && is_array($value)) {
        //新增
        if (ACTION_NAME == 'add') {
            $position_data_db = M('Position_data');
            $textcontent = array();
            foreach ($this->fields AS $_key => $_value) {
                //判断字段是否入库到推荐位字段
                if ($_value['isposition']) {
                    $textcontent[$_key] = $this->data[$_key];
                }
            }
            //颜色选择为隐藏域 在这里进行取值
            $textcontent['style'] = $_POST['style_color'] ? strip_tags($_POST['style_color']) : '';
            $textcontent = serialize($textcontent);
            foreach ($value as $r) {
                if ($r != '-1') {
                    $data = array('id' => $this->id, 'catid' => $this->data['catid'], 'posid' => $r, 'module' => 'content', 'modelid' => $this->modelid, 'data' => $textcontent, 'listorder' => $this->id);
                    //增加
                    $position_data_db->data($data)->add();
                }
            }
        } else {
            $posids = array();
            $catid = $this->data['catid'];
            $position_data_db = D('Position');
            foreach ($value as $r) {
                if ($r != '-1')
                    $posids[] = $r;
            }
            $textcontent = array();
            foreach ($this->fields AS $_key => $_value) {
                if ($_value['isposition']) {
                    $textcontent[$_key] = $this->data[$_key];
                }
            }
            //颜色选择为隐藏域 在这里进行取值
            $textcontent['style'] = $_POST['style_color'] ? strip_tags($_POST['style_color']) : '';

            $position_data_db->position_update($this->id, $this->modelid, $catid, $posids, $textcontent);
        }
    }
}

?>