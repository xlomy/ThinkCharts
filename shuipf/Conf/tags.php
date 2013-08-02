<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
//行为配置定义
/*
 * app_init                    应用初始化标签位
 * path_info                 PATHINFO 解析标签位
 * route_check             路由检测标签位
 * app_begin               应用开始标签位
 * action_begin           控制器开始标签位
 * view_begin              视图输出开始标签位
 * view_template         视图模板解析标签位
 * view_parse               视图解析标签位
 * view_filter                视图输出过滤标签位
 * view_end                 视图输出结束标签位
 * action_end              控制器结束标签位
 * app_end                  应用结束标签位
 */
return array(
    "app_init" => array("Appframe"),
    "app_begin" => array("Appcheck"),
    //AdminbaseAction.class _initialize()
    "appframe_rbac_init" => array("Rbac"),
);
?>
