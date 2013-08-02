<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class DomainsModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('module', 'require', '必须选择模块！'),
        array('module', '', '该模块绑定已经存在！', 0, 'unique', 1),
        array('domain', 'require', '必须填写需要绑定的域名！'),
    );

    /**
     * 添加
     * @param array $data 
     * @return boolean
     */
    public function AddDomains($data) {
        if (!$data) {
            return false;
        }
        if (in_array($data['module'], array("Domains", "Attachment", "Contents", "Models", "Template"))) {
            return false;
        }
        return $this->add($data);
    }

    /**
     * 编辑
     * @param array $data 
     * @return boolean
     */
    public function editDomains($data) {
        if (!$data) {
            return false;
        }
        if (in_array($data['module'], array("Domains", "Attachment", "Contents", "Models", "Template"))) {
            return false;
        }
        return $this->save($data);
    }

    /**
     * 更新缓存
     */
    public function domains_cache() {
        $Domains_data = $this->where(array("status" => 1))->field(array("module", "domain"))->select();
        foreach ($Domains_data as $r) {
            $r['domain'] = explode("|", $r['domain']);
            $Domains_list[$r['module']] = $r['domain'][0];
            foreach ($r['domain'] as $dom) {
                $Domains_cache[$dom] = $r['module'];
            }
        }
        //缓存 域名->模块
        F("Domains_list", $Domains_cache);
        //缓存 模块->绑定的域名
        F("Module_Domains_list", $Domains_list);
    }
    
    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        $this->domains_cache();
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->_before_write($data);
    }

}

?>
