<?php

/* * 
 * 网站配置模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class ConfigModel extends CommonModel {
    
    public function config_cache(){
        $data = M("Config")->getField("varname,value");
        F("Config", $data);
        return $data;
    }

    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("Config", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->config_cache();
    }
    
    //更新数据后更新缓存
    public function _after_update($data, $options) {
        parent::_after_update($data, $options);
        $this->config_cache();
    }

    //插入数据后更新缓存
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        $this->config_cache();
    }
}

?>
