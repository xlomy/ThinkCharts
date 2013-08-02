<?php

/**
 * 计划任务 - 刷新静态栏目页
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
//指定内容模块生成，没有指定默认使用GROUP_NAME
define("GROUP_MODULE", "Contents");

class ShuipFCMSRefresh_category extends BaseAction {

    //任务主体
    public function run($cronId) {
        import('Html');
        $html = new Html();
        $r = M("Cron")->where(array("cron_id" => $cronId))->find();
        if ($r) {
            $catid = explode(",", $r['data']);
            if (is_array($catid)) {
                foreach ($catid as $cid) {
                    $page = 1;
                    $j = 1;
                    //开始生成列表
                    do {
                        $html->category($cid, $page);
                        $page++;
                        $j++;
                        $total_number = isset($_GET['total_number']) ? (int)$_GET['total_number'] : $GLOBALS["Total_Pages"];
                    } while ($j <= $total_number);
                }
            }
        }
    }

}