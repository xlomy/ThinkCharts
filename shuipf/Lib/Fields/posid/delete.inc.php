<?php

//删除推荐位数据处理
function posid($field, $value) {
    //删除推荐位
    return M("Position_data")->where(array("id" => $this->id, "catid" => $this->catid, "module" => "content"))->delete();
}

?>