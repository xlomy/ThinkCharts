<?php

function editor($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    $isadmin = 0;
    //是否保存远程图片
    $enablesaveimage = (int)$setting['enablesaveimage'];
    if (defined("IN_ADMIN") && IN_ADMIN) {
        $isadmin = 1;
    }
    if($enablesaveimage){
        $Attachment = service("Attachment", array(
            "module" => "Contents",
            "catid" => $this->catid,
            "isadmin" => $isadmin,
        ));
        $value = $Attachment->download($value);
    }
    return $value;
}

?>