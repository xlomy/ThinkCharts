<?php

function editor($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    
    return $value;
}

?>