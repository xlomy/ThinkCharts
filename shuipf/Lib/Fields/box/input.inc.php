<?php

function box($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    if ($setting['boxtype'] == 'checkbox') {
        if (!is_array($value) || empty($value))
            return false;
        array_shift($value);
        $value = ',' . implode(',', $value) . ',';
        return $value;
    } elseif ($setting['boxtype'] == 'multiple') {
        if (is_array($value) && count($value) > 0) {
            $value = ',' . implode(',', $value) . ',';
            return $value;
        }
    } else {
        return $value;
    }
}

?>