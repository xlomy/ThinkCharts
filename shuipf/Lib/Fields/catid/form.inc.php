<?php
/*
 * 栏目字段类型
 */
function catid($field, $value, $fieldinfo) {
    if (!$value)
        $value = $this->catid;
    $publish_str = '';
    if (ACTION_NAME == 'add' && defined("IN_ADMIN") && IN_ADMIN){
        $publish_str = " <a href='javascript:;' onclick=\"omnipotent('selectid','".U("Contents/Content/add_othors",array("catid"=>$this->catid))."','同时发布到其他栏目',1);return false;\" style='color:#B5BFBB'>[同时发布到其他栏目]</a>
            <ul class='three_list cc' id='add_othors_text'></ul>";
    }
    return '<input type="hidden" name="info[' . $field . ']" value="' . $value . '">' . $this->categorys[$value]['catname'] . $publish_str;
}

?>