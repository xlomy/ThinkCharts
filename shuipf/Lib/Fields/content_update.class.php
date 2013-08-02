<?php

/* * 
 * 数据更新，也就是类似回调吧！
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class content_update {

    public $modelid, $fields, $data, $id, $catid;
    //错误提示
    public $error;

    function __construct($modelid, $id) {
        $this->modelid = $modelid;
        $this->fields = F("Model_field_" . $modelid);
        $this->id = $id;
        load("@.treatfun");
    }

    /**
     * 执行更新操作
     * @param type $data
     */
    function update($data) {
        $info = array();
        $this->data = $data;
        $catid = $this->catid = (int) $data['catid'];
        foreach ($data as $field => $value) {
            if (!isset($this->fields[$field])) {
                continue;
            }
            $func = $this->fields[$field]['formtype'];
            //配置
            $setting = unserialize($this->fields[$field]['setting']);

            $value = method_exists($this, $func) ? $this->$func($field, $value) : $value;

            //字段扩展，可以对字段内容进行再次处理，类似ECMS字段处理函数
            if ($setting['backstagefun'] || $setting['frontfun']) {
                $backstagefun = explode("###", $setting['backstagefun']);
                $usfun = $backstagefun[0];
                $usparam = $backstagefun[1];
                //前后台
                if (defined("IN_ADMIN") && IN_ADMIN) {
                    //检查方法是否存在
                    if (function_exists($usfun)) {
                        //判断是入库执行类型
                        if ((int) $setting['backstagefun_type'] >= 2) {
                            //调用自定义函数，参数传入：模型id，栏目ID，信息ID，字段内容，字段名，操作类型，附加参数
                            // 例子 demo($modelid ,$value , $catid , $id, $field ,$action ,$param){}
                            try {
                                $value = call_user_func($usfun, $this->modelid, $this->id, $value, $field, ACTION_NAME, $usparam);
                            } catch (Exception $exc) {
                                //记录日志
                                Log::write("模型id:" . $this->modelid . ",错误信息：调用自定义函数" . $usfun . "出现错误！");
                            }
                        }
                    }
                } else {
                    //前台投稿处理自定义函数处理
                    //判断当前用户组是否拥有使用字段处理函数的权限，该功能暂时木有，以后加上
                    if (true) {
                        $backstagefun = explode("###", $setting['frontfun']);
                        $usfun = $backstagefun[0];
                        $usparam = $backstagefun[1];
                        //检查方法是否存在
                        if (function_exists($usfun)) {
                            //判断是入库执行类型
                            if ((int) $setting['backstagefun_type'] >= 2) {
                                //调用自定义函数，参数传入：模型id，栏目ID，信息ID，字段内容，字段名，操作类型，附加参数
                                // 例子 demo($modelid ,$value , $catid , $id, $field ,$action ,$param){}
                                try {
                                    $value = call_user_func($usfun, $this->modelid, $this->id, $value, $field, ACTION_NAME, $usparam);
                                } catch (Exception $exc) {
                                    //记录日志
                                    Log::write("模型id:" . $this->modelid . ",错误信息：调用自定义函数" . $usfun . "出现错误！");
                                }
                            }
                        }
                    }
                }
            }

            $info[$field] = $value;
        }
        
        return $info;
    }

    /**
     * 错误信息
     * @param type $message 错误信息
     * @param type $fields 字段
     */
    public function error($message, $fields = false) {
        $this->error = $message;
    }

    /**
     * 获取错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }

}?>