<?php
//推荐位
function posid($field, $value, $fieldinfo) {
    $setting = unserialize($fieldinfo['setting']);
    //推荐位缓存
    $position = F("Position");
    if (empty($position))
        return '';
    $array = array();
    foreach ($position as $_key => $_value) {
        if ($_value['modelid'] && ($_value['modelid'] != $this->modelid) || ($_value['catid'] && strpos(',' . $this->categorys[$_value['catid']]['arrchildid'] . ',', ',' . $this->catid . ',') === false))
            continue;
        $array[$_key] = $_value['name'];
    }
    $posids = array();
    if (ACTION_NAME == 'edit') {
        $this->position_data_db = M('Position_data');
        $result = $this->position_data_db->where(array('id' => $this->id, 'modelid' => $this->modelid))->getField("posid,id,catid,posid,module,modelid,thumb,data,listorder,expiration,extention,synedit");
        $posids = implode(',', array_keys($result));
    } else {
        $posids = $setting['defaultvalue'];
    }
    return "<input type='hidden' name='info[$field][]' value='-1'>" . Form::checkbox($array, $posids, "name='info[$field][]'", '', $setting['width']);
}

?>