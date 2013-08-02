<?php

//分页选择字段
function pages($field, $value, $fieldinfo) {
    extract($fieldinfo);
    if ($value) {
        $v = explode('|', $value);
        $data = "<select name=\"info[paginationtype]\" id=\"paginationtype\" onchange=\"if(this.value==1)\$('#paginationtype1').css('display','');else \$('#paginationtype1').css('display','none');\">";
        $type = array("不分页", "自动分页", "手动分页");
        if ($v[0] == 1)
            $con = 'style="display:"';
        else
            $con = 'style="display:none"';
        foreach ($type as $i => $val) {
            if ($i == $v[0])
                $tag = 'selected';
            else
                $tag = '';
            $data .= "<option value=\"$i\" $tag>$val</option>";
        }
        $data .= "</select><span id=\"paginationtype1\" $con> <input name=\"info[maxcharperpage]\" type=\"text\" id=\"maxcharperpage\" value=\"$v[1]\" size=\"8\" maxlength=\"8\" class='input'>字符数（包含HTML标记）</span>";
        return $data;
    } else {
        return "<select name=\"info[paginationtype]\" id=\"paginationtype\" onchange=\"if(this.value==1)\$('#paginationtype1').css('display','');else \$('#paginationtype1').css('display','none');\">
                <option value=\"0\">不分页</option>
                <option value=\"1\" selected>自动分页</option>
                <option value=\"2\">手动分页</option>
            </select>
			<span id=\"paginationtype1\" style=\"\"><input name=\"info[maxcharperpage]\" type=\"text\" id=\"maxcharperpage\" value=\"10000\" size=\"8\" maxlength=\"8\" class='input'>字符数（包含HTML标记）</span>";
    }
}

?>