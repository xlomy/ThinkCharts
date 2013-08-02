<?php 
/**
 * 模块安装，菜单/权限配置
 * Some rights reserved：omyweb.cn
 * Contact email:admin@omyweb.cn
 */
defined('UNINSTALL') or exit('Access Denied');
//删除菜单/权限数据
M("Menu")->where(array("app"=>"Vote"))->delete();
M("Access")->where(array("g" => "Vote"))->delete();
?>