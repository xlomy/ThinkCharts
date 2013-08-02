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
    "app" => "Domains",
    //文件名称，比如LinksAction.class.php就填写 Links
    "model" => "Domains",
    //方法名称
    "action" => "index",
    //附加参数 例如：a=12&id=777
    "data" => "",
    //类型，后台是1。
    "type" => 1,
    //状态，1是显示，2是不显示
    "status" => 1,
    //名称
    "name" => "域名绑定",
    //备注
    "remark" => "域名绑定管理",
    //排序
    "listorder" => 0
));

//添加其他需要加入权限认证的方法，后台进行权限认证时不通过。
//提示：比如一些删除，修改这类方法需要配合参数使用，该类不适合直接显示出来，可以把status设置为0
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Domains","model"=>"Domains","action"=>"add","data"=>"","type"=>1,"status"=>1,"name"=>"添加域名绑定","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Domains","model"=>"Domains","action"=>"delete","data"=>"","type"=>1,"status"=>0,"name"=>"删除","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Domains","model"=>"Domains","action"=>"edit","data"=>"","type"=>1,"status"=>0,"name"=>"编辑","remark"=>"","listorder"=>0));
?>