<form method="post" action="{:U('Index/post')}" id="myform" name="myform">
    <input type="hidden" name="formid" value="{$formid}"/>
    <table width="925" cellspacing="0" class="table_form">
        <?php
        if (is_array($forminfos)) {
            foreach ($forminfos as $field => $info) {
                if ($info['isomnipotent'])
                    continue;
                if ($info['formtype'] == 'omnipotent') {
                    foreach ($forminfos as $_fm => $_fm_value) {
                        if ($_fm_value['isomnipotent']) {
                            $info['form'] = str_replace('{' . $_fm . '}', $_fm_value['form'], $info['form']);
                        }
                    }
                }
                ?>
                <tr>
                    <th width="100"><if condition=" $info['star'] "><font color="red">*</font></if> {$info['name']}：</th> 
                <td width="800">{$info['form']}<if condition=" $info['tips'] ">{$info['tips']}</if></td>
                </tr>
                <?php
            }
        }
        ?>
        <tr>
            <th></th>
            <td>
                <!--提交成功返回地址-->
                <input name="forward" type="hidden" value="{$forward}">
                <input name="dosubmit" type="submit" id="dosubmit" value="提交" class="button"></td>
        </tr>
    </table>
</form>