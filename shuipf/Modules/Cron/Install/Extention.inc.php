<?php 
/**
 * 模块安装，菜单/权限配置
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
defined('INSTALL') or exit('Access Denied');
//添加一个菜单到后台“模块->模块列表”ID等于常量 MENUID
$parentid = M("Menu")->add(array(
    //父ID
    "parentid" => MENUID,
    //模块目录名称，也是项目名称
    "app" => "Cron",
    //文件名称，比如LinksAction.class.php就填写 Links
    "model" => "Cron",
    //方法名称
    "action" => "index",
    //附加参数 例如：a=12&id=777
    "data" => "",
    //类型，后台是1。
    "type" => 1,
    //状态，1是显示，2是不显示
    "status" => 1,
    //名称
    "name" => "计划任务",
    //备注
    "remark" => "计划任务是一项使系统在规定时间自动执行某些特定任务的功能。",
    //排序
    "listorder" => 0
));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Cron","model"=>"Cron","action"=>"add","data"=>"","type"=>1,"status"=>1,"name"=>"添加计划任务","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Cron","model"=>"Cron","action"=>"edit","data"=>"","type"=>1,"status"=>2,"name"=>"编辑计划任务","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Cron","model"=>"Cron","action"=>"delete","data"=>"","type"=>1,"status"=>2,"name"=>"删除计划任务","remark"=>"","listorder"=>0));