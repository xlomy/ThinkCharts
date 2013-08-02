<?php

/* * 
 * 网站配置模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class ModuleModel extends CommonModel {

    /**
     * 更新缓存
     * @return type
     */
    public function module_cache() {
        $data = M("Module")->where(array("disabled" => 1))->select();
        $App = array();
        $Module = array();
        foreach ($data as $v) {
            $Module[$v['module']] = $v;
            $App[$v['module']] = $v['module'];
        }
        F("Module", $Module);
        F("App", $App);
        return $data;
    }

    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("Module", NULL);
        F("App", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->module_cache();
    }

    //更新数据后更新缓存
    public function _after_update($data, $options) {
        parent::_after_update($data, $options);
        $this->module_cache();
    }

    //插入数据后更新缓存
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        $this->module_cache();
    }

}

?>
