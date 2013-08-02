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
    "parentid" => 52,//内容发布管理
    //模块目录名称，也是项目名称
    "app" => "Collection",
    //文件名称，比如LinksAction.class.php就填写 Links
    "model" => "Node",
    //方法名称
    "action" => "index",
    //附加参数 例如：a=12&id=777
    "data" => "",
    //类型，后台是1。
    "type" => 1,
    //状态，1是显示，2是不显示
    "status" => 1,
    //名称
    "name" => "采集管理",
    //备注
    "remark" => "采集模块是可以批量采集目标网站内容入库！",
    //排序
    "listorder" => 0
));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"add","data"=>"","type"=>1,"status"=>1,"name"=>"添加采集点","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"node_import","data"=>"","type"=>1,"status"=>1,"name"=>"导入采集点","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"edit","data"=>"","type"=>1,"status"=>2,"name"=>"编辑采集点","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"delete","data"=>"","type"=>1,"status"=>2,"name"=>"删除采集点","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"copy","data"=>"","type"=>1,"status"=>2,"name"=>"复制采集点","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"export","data"=>"","type"=>1,"status"=>2,"name"=>"导出采集点","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"col_url_list","data"=>"","type"=>1,"status"=>2,"name"=>"采集网址入库","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"col_content","data"=>"","type"=>1,"status"=>2,"name"=>"采集内容入库","remark"=>"","listorder"=>0));
$neirfb = M("Menu")->add(array("parentid"=>$parentid,"app"=>"Collection","model"=>"Node","action"=>"publist","data"=>"","type"=>1,"status"=>2,"name"=>"内容发布","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$neirfb,"app"=>"Collection","model"=>"Node","action"=>"content_del","data"=>"","type"=>1,"status"=>2,"name"=>"删除已采集文章","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$neirfb,"app"=>"Collection","model"=>"Node","action"=>"import","data"=>"","type"=>1,"status"=>2,"name"=>"导入文章","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$neirfb,"app"=>"Collection","model"=>"Node","action"=>"import_content","data"=>"","type"=>1,"status"=>2,"name"=>"导入文章到模型入库","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$neirfb,"app"=>"Collection","model"=>"Node","action"=>"import_program_add","data"=>"","type"=>1,"status"=>2,"name"=>"添加导入方案","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$neirfb,"app"=>"Collection","model"=>"Node","action"=>"import_program_del","data"=>"","type"=>1,"status"=>2,"name"=>"删除导入方案","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$neirfb,"app"=>"Collection","model"=>"Node","action"=>"import_program_edit","data"=>"","type"=>1,"status"=>2,"name"=>"编辑导入方案","remark"=>"","listorder"=>0));