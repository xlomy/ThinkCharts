<?php

/**
 * 内容模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Content_cacheModel extends CommonModel {

    Protected $autoCheckFields = false;

    // 更新模型缓存方法
    public function model_content_cache() {
        $fields_path = C("SHUIPF_FIELDS_PATH");
        require $fields_path . 'fields.inc.php';
        //更新内容模型数据处理相关类
        $classtypes = array('form', 'input', 'output', 'update', 'delete');
        //缓存生成路径
        $cachemodepath = RUNTIME_PATH;
        foreach ($classtypes as $classtype) {
            $cache_data = file_get_contents($fields_path . "content_$classtype.class.php");
            $cache_data = str_replace('}?>', '', $cache_data);
            //循环字段列表，把各个字段的 form.inc.php 文件合并到 缓存 content_form.class.php 文件
            foreach ($fields as $field => $fieldvalue) {
                //检查文件是否存在
                if (file_exists($fields_path . $field . DIRECTORY_SEPARATOR . $classtype . '.inc.php')) {
                    //读取文件，$classtype.inc.php 
                    $ca = file_get_contents($fields_path . $field . DIRECTORY_SEPARATOR . $classtype . '.inc.php');
                    $cache_data .= str_replace(array("<?php", "?>"), "", $ca);
                }
            }
            $cache_data .= "\r\n } \r\n?>";
            //写入缓存
            file_put_contents($cachemodepath . 'content_' . $classtype . '.class.php', $cache_data);
            //设置权限
            chmod($cachemodepath . 'content_' . $classtype . '.class.php', 0777);
            unset($cache_data);
        }
    }

}

?>
