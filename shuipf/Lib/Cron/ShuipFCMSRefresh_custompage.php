<?php

/**
 * 计划任务 - 刷新自定义页面
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
//指定内容模块生成，没有指定默认使用GROUP_NAME
define("GROUP_MODULE", "Contents");

class ShuipFCMSRefresh_custompage extends BaseAction {

    //任务主体
    public function run($cronId) {
        import('Html');
        $html = new Html();
        $r = M("Cron")->where(array("cron_id" => $cronId))->find();
        if ($r) {
            $catid = explode(",", $r['data']);
            if (is_array($catid)) {
                $CDb = D("Customtemp");
                foreach ($catid as $cid) {
                    $tempid = $cid;
                    $rs = $CDb->where(array("tempid" => $tempid))->find();
                    if ($rs) {
                        $html->createhtml($rs['temptext'], $rs);
                    }
                }
            }
        }
    }

}