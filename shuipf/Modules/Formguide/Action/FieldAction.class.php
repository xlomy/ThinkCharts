<?php

/**
 * 字段管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class FieldAction extends AdminbaseAction {

    public $formid, $fields, $banfie;

    function _initialize() {
        parent::_initialize();
        $this->assign("show_header", true);
        $this->formid = (int) $this->_get("formid");
        //字段类型存放目录
        $this->fields = C("SHUIPF_FIELDS_PATH");
        //允许使用的字段列表
        $this->banfie = array("text","textarea", "box", "number", "editor", "datetime", "downfiles", "image", "images", "omnipotent");
        $this->modelfield = D("Model_field");
    }

    /**
     * 管理字段
     */
    public function index() {
        if (IS_POST) {
            foreach ($_POST['listorders'] as $id => $listorder) {
                $this->modelfield->where(array('fieldid' => $id))->save(array('listorder' => $listorder));
            }
            $this->success("排序更新成功！");
        } else {
            if (!$this->formid) {
                $this->error("缺少参数！");
            }
            $model = M("Model")->where(array("modelid" => $this->formid))->find();
            if (!$model) {
                $this->error("该表单不存在！");
            }
            //载入字段配置文件
            require $this->fields . "fields.inc.php";
            $data = D("Model_field")->Modelfield($this->formid);

            //不允许删除的字段，这些字段讲不会在字段添加处显示
            $this->assign("not_allow_fields", $not_allow_fields);
            //允许添加但必须唯一的字段
            $this->assign("unique_fields", $unique_fields);
            //禁止被禁用的字段列表
            $this->assign("forbid_fields", $forbid_fields);
            //禁止被删除的字段列表
            $this->assign("forbid_delete", $forbid_delete);
            //可以追加 JS和CSS 的字段
            $this->assign("att_css_js", $att_css_js);
            $this->assign("modelinfo", $model);
            $this->assign("data", $data);
            $this->assign("modelinfo", $model);
            $this->assign("formid", $this->formid);
            $this->display();
        }
    }

    /**
     * 添加字段
     */
    public function add() {
        if (IS_POST) {
            //模型ID
            $modelid = (int) $this->_post("modelid");
            //表名获取
            $model_table = M("Model")->where(array("modelid" => $modelid, "type" => 3))->getField("tablename");
            if (!$model_table) {
                $this->error("表单模型不存在！");
            }
            //完整表名获取
            $tablename = C("DB_PREFIX") . $model_table;
            //所增加的字段名
            $field = $_POST['field'];
            //字符长度取值范围
            $minlength = $_POST['minlength'] ? $_POST['minlength'] : 0;
            $maxlength = $_POST['maxlength'] ? $_POST['maxlength'] : 0;
            //字段类型
            $fieldtype = $_POST['formtype'];
            if (empty($fieldtype)) {
                $this->error("请选择字段类型！");
            }
            //载入对应字段配置信息
            $fiepath = $this->fields . $fieldtype . "/";
            require($fiepath . "config.inc.php");
            //根据字段设置临时更改字段类型，否则使用字段配置文件配置的类型
            if (isset($_POST['setting']['fieldtype'])) {
                $field_type = $_POST['setting']['fieldtype'];
            }
            //增加字段所需配置信息
            $fieldarray = array(
                "tablename" => $tablename,
                "fieldname" => $field,
                "maxlength" => $maxlength,
                "minlength" => $minlength
            );

            $data['formtype'] = $fieldtype; //字段类型
            $data['issystem'] = 1; //作为主表字段
            $data['field'] = $field; //字段名
            $data['name'] = $_POST['name']; //字段别名
            $data['tips'] = $_POST['tips']; //字段提示
            $data['formattribute'] = $_POST['formattribute']; //表单附加属性
            $data['css'] = $_POST['css']; //表单样式名
            $data['minlength'] = $minlength; //最小值
            $data['maxlength'] = $maxlength; //最大值
            $data['pattern'] = $_POST['pattern']; //数据校验正则
            $data['errortips'] = $_POST['errortips']; //数据校验未通过的提示信息
            $data['isunique'] = $_POST['isunique']?1:0; //值唯一
            $data['isbase'] = 1; //作为基本信息
            $data['issearch'] = 0; //作为搜索条件
            $data['isadd'] = $_POST['isadd']?1:0; //在前台投稿中显示
            $data['isfulltext'] = 0; //作为全站搜索信息
            $data['isomnipotent'] = $_POST['isomnipotent']?1:0; //作为万能字段的附属字段 1 是
            $data['isposition'] = 0; //是否入库到推荐位 1 是
            $data['modelid'] = $modelid; //模型id
            //附加属性值
            $data['setting'] = serialize($_POST['setting']);
            $data['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',', $_POST['unsetgroupids']) : '';
            $data['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',', $_POST['unsetroleids']) : '';
            //表单令牌
            $data[C("TOKEN_NAME")] = $_POST[C("TOKEN_NAME")];
            $cda = $this->modelfield->create($data);
            if ($cda) {
                $cda['pattern'] = $_POST['pattern']; //数据校验正则
                $addfieldstatus = $this->modelfield->addfield($field_type, $fieldarray);
                if ($addfieldstatus) {
                    if (!$this->modelfield->add($cda)) {
                        $this->error("字段增加失败！");
                    }
                    $this->assign("jumpUrl", U("Field/index", array("formid" => $modelid)));
                    $this->success("添加字段成功！");
                } else {
                    $this->error("添加字段失败！");
                }
            } else {
                $this->error($this->modelfield->getError());
            }
        } else {
            if (!$this->formid) {
                $this->error("缺少参数！");
            }
            $model = M("Model")->where(array("modelid" => $this->formid))->find();
            if (!$model) {
                $this->error("该表单不存在！");
            }
            require $this->fields . "fields.inc.php";
            $modelid = $this->formid;
            if (!$modelid) {
                $this->error("请选择需要添加字段的模型！");
            }
            $f_datas = D("Model_field")->where(array("modelid" => $modelid))->getField("field,field,name");
            foreach ($f_datas as $_k => $_v) {
                $exists_field[] = $_v['field'];
            }
            //字段类型过滤
            foreach ($fields as $_k => $_v) {
                if (in_array($_k, $not_allow_fields) || in_array($_k, $exists_field) && in_array($_k, $unique_fields) || !in_array($_k, $this->banfie))
                    continue;
                $all_field[$_k] = $_v;
            }
            $this->assign("all_field", $all_field);
            $this->assign("modelinfo", $model);
            $this->assign("formid", $this->formid);
            $this->display();
        }
    }

    /**
     * 编辑字段
     */
    public function edit() {
        if(IS_POST){
            //模型ID
            $modelid = (int) $this->_post("modelid");
            //字段ID
            $fieldid = (int) $this->_post("fieldid");
            //表名获取
            $model_table = M("Model")->where(array("modelid" => $modelid, "type" => 3))->getField("tablename");
            //完整表名获取 判断主表 还是副表
            $tablename = C("DB_PREFIX") . $model_table;
            if (!$this->modelfield->table_exists($model_table)) {
                $this->error("数据表不存在！");
            }
            //所编辑的字段名
            $field = $_POST['field'];
            //字符长度取值范围
            $minlength = $_POST['minlength'] ? $_POST['minlength'] : 0;
            $maxlength = $_POST['maxlength'] ? $_POST['maxlength'] : 0;

            //旧字段名
            $oldfield = $_POST['oldfield'];
            $field_type = $_POST['formtype'];

            //载入对应字段配置信息
            $fiepath = $this->fields . $field_type . DIRECTORY_SEPARATOR;
            require($fiepath . "config.inc.php");
            //字段类型
            if (isset($_POST['setting']['fieldtype'])) {
                $field_type = $_POST['setting']['fieldtype'];
            }

            //更改字段
            require $this->fields . 'edit.sql.php';

            $data['issystem'] = 1;
            $data['fieldid'] = $fieldid;
            $data['field'] = $field; //字段名
            $data['name'] = $_POST['name']; //字段别名
            $data['tips'] = $_POST['tips']; //字段提示
            $data['formattribute'] = $_POST['formattribute']; //表单附加属性
            $data['css'] = $_POST['css']; //表单样式名
            $data['minlength'] = $minlength; //最小值
            $data['maxlength'] = $maxlength; //最大值
            $data['pattern'] = $_POST['pattern']; //数据校验正则
            $data['errortips'] = $_POST['errortips']; //数据校验未通过的提示信息
            $data['isbase'] = 1; //作为基本信息
            $data['isadd'] = $_POST['isadd']?1:0; //在前台投稿中显示
            $data['isunique'] = $_POST['isunique']?1:0; //值唯一
            //附加属性值
            $data['setting'] = serialize($_POST['setting']);
            $data['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',', $_POST['unsetgroupids']) : '';
            $data['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',', $_POST['unsetroleids']) : '';
            //表单令牌
            $data[C("TOKEN_NAME")] = $_POST[C("TOKEN_NAME")];
            //var_dump($data);exit;
            $editdata = $this->modelfield->create($data);
            if ($editdata) {
                $editdata['pattern'] = $_POST['pattern']; //数据校验正则
                if ($this->modelfield->save($editdata) !== false) {
                    $this->assign("jumpUrl", U("Field/index", array("formid" => $modelid)));
                    $this->success("更新成功！");
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($this->modelfield->getError());
            }
        }else{
             //模型ID
            $modelid = (int) $_GET['formid'];
            //字段ID
            $fieldid = (int) $_GET['fieldid'];
            //模型信息
            $modedata = D("Model")->where(array("modelid" => $modelid))->find();
            if(!$modedata){
                $this->error("该表单不存在！");
            }
            //字段信息
            $fielddata = $this->modelfield->where(array("fieldid" => $fieldid, "modelid" => $modelid))->find();

            extract($fielddata);
            //载入对应字段配置
            $fiepath = $this->fields . $formtype . "/";
            require($fiepath . "config.inc.php");
            //将序列化的转换为数组
            $setting = unserialize($setting);
            //打开缓冲区
            ob_start();
            include $fiepath . 'field_edit_form.inc.php';
            $form_data = ob_get_contents();
            //关闭缓冲
            ob_end_clean();
            //载入字段配置
            require $this->fields . "fields.inc.php";
            //字段类型过滤
            foreach ($fields as $_k => $_v) {
                $all_field[$_k] = $_v;
            }
            //var_dump($all_field);exit;
            $this->assign("all_field", $all_field);
            //附加属性
            $this->assign("form_data", $form_data);
            $this->assign("modelid", $modelid);
            $this->assign("formid", $modelid);
            $this->assign("fieldid", $fieldid);
            $this->assign("setting", $setting);
            //字段信息分配到模板
            $this->assign("data", $fielddata);
            $this->assign("modelinfo", $modedata);
            $this->display();
        }
    }

    /**
     * 删除字段
     */
    public function delete() {
         //载入字段配置文件
        require $this->fields . "fields.inc.php";
        $fieldid = $this->_get("fieldid");
        $modelid = $this->_get("formid");
        $field = $this->modelfield->where(array("fieldid" => $fieldid, "modelid" => $modelid))->find();
        if (empty($field)) {
            $this->error("需要删除的字段不存在！");
        }
        //检查是否允许被删除
        if (in_array($field['field'], $forbid_delete)) {
            $this->error("该字段不允许被删除！");
        }

        $tbname = M("Model")->where(array("modelid" => $modelid, "type" => 3))->getField("tablename");;
        if(!$tbname){
            $this->error("出现错误！请尝试更新缓存！");
        }
        //删除字段信息
        $status = $this->modelfield->where(array("fieldid" => $fieldid, "modelid" => $modelid))->delete();
        if ($status) {
            //删除表中字段
            $this->modelfield->drop_field($tbname, $field['field']);
            $this->success("字段删除成功！");
        } else {
            $this->error("删除字段失败！");
        }
    }

    /**
     * 验证字段是否重复 AJAX
     */
    public function public_checkfield() {
        //新字段名称
        $field = $this->_get("field");
        //原来字段名
        $oldfield = $this->_get("oldfield");
        if ($field == $oldfield) {
            $this->ajaxReturn($field, "字段没有重复！", true);
        }
        //模型ID
        $modelid = $this->_get("modelid");

        $status = $this->modelfield->where(array("field" => $field, "modelid" => $modelid))->count();
        if ($status == 0) {
            $this->ajaxReturn($field, "字段没有重复！", true);
        } else {
            $this->ajaxReturn($field, "字段有重复！", false);
        }
    }

    /**
     * 字段属性配置
     */
    public function public_field_setting() {
        //字段类型
        $fieldtype = $this->_get("fieldtype");
        $fiepath = $this->fields . $fieldtype . "/";
        //载入对应字段配置文件 config.inc.php 
        require($fiepath . "config.inc.php");
        ob_start();
        include $fiepath . "field_add_form.inc.php";
        $data_setting = ob_get_contents();
        ob_end_clean();
        $settings = array('field_basic_table' => $field_basic_table, 'field_minlength' => $field_minlength, 'field_maxlength' => $field_maxlength, 'field_allow_search' => $field_allow_search, 'field_allow_fulltext' => $field_allow_fulltext, 'field_allow_isunique' => $field_allow_isunique, 'setting' => $data_setting);
        echo json_encode($settings);
        return true;
    }

    /**
     * 字段的启用与禁用 
     */
    public function disabled() {
        $fieldid = intval($_GET['fieldid']);
        $disabled = $_GET['disabled'] ? 0 : 1;
        $status = $this->modelfield->where(array('fieldid' => $fieldid))->save(array('disabled' => $disabled));
        if ($status !== false) {
            $this->success("操作成功！");
        } else {
            $this->error("操作失败！");
        }
    }
}

?>
