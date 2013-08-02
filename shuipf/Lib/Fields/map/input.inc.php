<?php

function map($field, $value) {
    foreach ($value as $r) {
        $data.='|' . $r;
    }
    return substr($data, 1);
}

?>