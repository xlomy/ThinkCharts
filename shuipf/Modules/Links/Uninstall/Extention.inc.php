<?php 
/**
 * 模块安装，菜单/权限配置
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
defined('UNINSTALL') or exit('Access Denied');
//删除菜单/权限数据
M("Menu")->where(array("app"=>"Links"))->delete();
M("Access")->where(array("g" => "Links"))->delete();
//删除分类数据
M("Terms")->where(array("module"=>"links"))->delete();
?>