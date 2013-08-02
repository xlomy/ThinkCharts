<?php

function datetime($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    if ($setting['fieldtype'] == 'int') {
        $value = strtotime($value);
    }
    return $value;
}

?>