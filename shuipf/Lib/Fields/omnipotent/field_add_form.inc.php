<table cellpadding="2" cellspacing="1" width="98%">
    <tr> 
        <td width="100">表单</td>
        <td><textarea name="setting[formtext]" rows="2" cols="20" id="options" style="height:100px;width:400px;"></textarea><BR>
            例如：&lt;input type='text' name='info[voteid]' id='voteid' value='{FIELD_VALUE}' style='50' &gt;</td>
    </tr>
    <tr> 
        <td>字段类型</td>
        <td>
            <select name="setting[fieldtype]">
                <option value="varchar">字符型0-255字节(VARCHAR)</option>
                <option value="char">定长字符型0-255字节(CHAR)</option>
                <option value="text">小型字符型(TEXT)</option>
                <option value="mediumtext">中型字符型(MEDIUMTEXT)</option>
                <option value="longtext">大型字符型(LONGTEXT)</option>
                <option value="tinyint">整数 TINYINT(3)</option>
                <option value="smallint">整数 SMALLINT(5)</option>
                <option value="mediumint">整数 MEDIUMINT(8)</option>
                <option value="int">整数 INT(10)</option>
                <option value="bigint">超大数值型(BIGINT)</option>
                <option value="float">数值浮点型(FLOAT)</option>
                <option value="double">数值双精度型(DOUBLE)</option>
                <option value="date">日期型(DATE)</option>
                <option value="datetime">日期时间型(DATETIME)</option>
            </select> <span id="minnumber" style="display:none"><input type="radio" name="setting[minnumber]" value="1" checked/> <font color='red'>正整数</font> <input type="radio" name="setting[minnumber]" value="-1" /> 整数</span>
        </td>
    </tr>
</table>