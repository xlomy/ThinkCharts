<?php

function datetime($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    extract($setting);
    if ($fieldtype == 'date' || $fieldtype == 'datetime') {
        return $value;
    } else {
        $format_txt = $format;
    }
    if (strlen($format_txt) < 6) {
        $isdatetime = 0;
    } else {
        $isdatetime = 1;
    }
    if (!$value)
        $value = time();
    $value = date($format_txt, $value);
    return $value;
}

?>