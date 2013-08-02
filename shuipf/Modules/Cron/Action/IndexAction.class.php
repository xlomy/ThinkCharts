<?php

/**
 * 计划任务
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class IndexAction extends AppframeAction {

    function _initialize() {
        parent::_initialize();
        //单个任务最大执行时间
        $CRON_MAX_TIME = C('CRON_MAX_TIME');
        if (!$CRON_MAX_TIME) {
            C('CRON_MAX_TIME', 3000);
        }
    }

    //执行计划任务
    public function index() {
        // 锁定自动执行
        $lockfile = RUNTIME_PATH . 'cron.lock';
        if (is_writable($lockfile) && filemtime($lockfile) > $_SERVER['REQUEST_TIME'] - C('CRON_MAX_TIME')) {
            return;
        } else {
            //设置指定文件的访问和修改时间
            touch($lockfile);
        }
        set_time_limit(0);
        ignore_user_abort(true);

        //执行计划任务
        $this->runCron();

        // 解除锁定
        unlink($lockfile);
    }

    /**
     * 递归执行计划任务
     * Enter description here ...
     */
    private function runCron() {
        $_time = time();
        $cron = D("Cron")->where(array("isopen" => array("EGT", 1)))->order(array("next_time" => "ASC"))->find();
        //检测是否还有需要执行的任务
        if (!$cron || $cron['next_time'] > $_time)
            return false;
        list($day, $hour, $minute) = explode('-', $cron['loop_daytime']);
        //获取下一次执行时间
        $nexttime = D("Cron")->getNextTime($cron['loop_type'], $day, $hour, $minute);
        //更新计划任务的下次执行时间
        D("Cron")->where(array("cron_id" => $cron['cron_id']))->save(array(
            "modified_time" => $_time,
            "next_time" => $nexttime,
        ));

        if (!$this->_runAction($cron['cron_file'], $cron['cron_id'])) {
            return false;
        }

        $this->runCron();

        return true;
    }

    //运行计划
    private function _runAction($filename = '', $cronId = 0) {
        $dir = LIB_PATH . "Cron/";
        if (!$filename || strpos($filename, "ShuipFCMS") != 0)
            return false;
        //载入文件
        $require = require_cache($dir . $filename . ".php");
        if ($require) {
            try {
                $cron = new $filename();
                $cron->run($cronId);
            } catch (Exception $exc) {
                Log::write("计划任务:$filename，执行出错！");
            }
        } else {
            Log::write("计划任务:$filename，文件载入出错！");
        }
        return true;
    }

}

?>
