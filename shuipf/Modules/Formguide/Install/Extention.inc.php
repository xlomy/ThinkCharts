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
    "app" => "Formguide",
    //文件名称，比如LinksAction.class.php就填写 Links
    "model" => "Formguide",
    //方法名称
    "action" => "index",
    //附加参数 例如：a=12&id=777
    "data" => "",
    //类型，后台是1。
    "type" => 1,
    //状态，1是显示，2是不显示
    "status" => 1,
    //名称
    "name" => "表单",
    //备注
    "remark" => "表单",
    //排序
    "listorder" => 0
));

//添加其他需要加入权限认证的方法，后台进行权限认证时不通过。
//提示：比如一些删除，修改这类方法需要配合参数使用，该类不适合直接显示出来，可以把status设置为0
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Formguide","model"=>"Formguide","action"=>"add","data"=>"","type"=>1,"status"=>1,"name"=>"添加表单","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Formguide","model"=>"Formguide","action"=>"edit","data"=>"","type"=>1,"status"=>0,"name"=>"编辑","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Formguide","model"=>"Formguide","action"=>"delete","data"=>"","type"=>1,"status"=>0,"name"=>"删除","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Formguide","model"=>"Formguide","action"=>"status","data"=>"","type"=>1,"status"=>0,"name"=>"禁用","remark"=>"","listorder"=>0));

$InfoID = M("Menu")->add(array("parentid"=>$parentid,"app"=>"Formguide","model"=>"Info","action"=>"index","data"=>"","type"=>1,"status"=>0,"name"=>"信息列表","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$InfoID,"app"=>"Formguide","model"=>"Info","action"=>"delete","data"=>"","type"=>1,"status"=>0,"name"=>"删除","remark"=>"","listorder"=>0));

$FieldID = M("Menu")->add(array("parentid"=>$parentid,"app"=>"Formguide","model"=>"Field","action"=>"index","data"=>"","type"=>1,"status"=>0,"name"=>"管理字段","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$FieldID,"app"=>"Formguide","model"=>"Field","action"=>"add","data"=>"","type"=>1,"status"=>0,"name"=>"添加字段","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$FieldID,"app"=>"Formguide","model"=>"Field","action"=>"edit","data"=>"","type"=>1,"status"=>0,"name"=>"编辑字段","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$FieldID,"app"=>"Formguide","model"=>"Field","action"=>"delete","data"=>"","type"=>1,"status"=>0,"name"=>"删除字段","remark"=>"","listorder"=>0));
?>