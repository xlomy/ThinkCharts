<?php

/**
 * Shuipf模式
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
// Shuipf模式定义文件
$version = include SITE_PATH . '/shuipf/Conf/version.php';
$version = $version?$version:array();
$dataconfig = include SITE_PATH . '/shuipf/Conf/dataconfig.php';
$dataconfig = $dataconfig?$dataconfig:array();
$addition = include SITE_PATH . '/shuipf/Conf/addition.php';
$addition = $addition?$addition:array();
$Shuipf = array(
    'core' => array(
        LIB_PATH . 'Mode/shuipf/functions.php', // 标准模式函数库
        CORE_PATH . 'Core/Log.class.php', // 日志处理类
        LIB_PATH . 'Mode/shuipf/Dispatcher.class.php', // URL调度类
        CORE_PATH . 'Core/App.class.php', // 应用程序类
        CORE_PATH . 'Core/Action.class.php', // 控制器类
        CORE_PATH . 'Core/View.class.php', // 视图类
    ),
    'config' => array_merge($version, $addition, $dataconfig),
);
return $Shuipf;
