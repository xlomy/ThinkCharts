<?php

/**
 * 模块安装，菜单/权限配置
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
defined('UNINSTALL') or exit('Access Denied');
//删除菜单/权限数据
M("Menu")->where(array("app" => "Formguide"))->delete();
M("Access")->where(array("g" => "Formguide"))->delete();
//取得模型
$model = D("Model")->where(array("type" => 3))->select();
if ($model) {
    foreach ($model as $r) {
        if ($r['modelid'] && $r['type'] == 3) {
            //删除模型数据
            D("Model")->where(array("modelid" => $r['modelid']))->delete();
            //删除数据表
            D("Model")->DeleteTable($r['tablename']);
        }
    }
}
?>