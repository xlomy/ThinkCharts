<?php

//删除对应tags记录
function tags($field, $value) {
    return D("Tags")->deleteAll($this->id,$this->catid,$this->modelid);
}

?>
