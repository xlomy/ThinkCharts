<?php

/**
 * 点击数
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class HitsAction extends Action {

    protected $db;

    public function index() {
        $catid = (int) $this->_get("catid");
        $id = (int) $this->_get("id");
        $hitsid = 'c-' . $catid . '-' . $_GET['id'];
        $r = $this->get_count($hitsid);
        if (!$r)
            exit;
        extract($r);
        $this->hits($hitsid);
        echo json_encode($r);
    }

    /**
     * 获取点击数
     * @param type $hitsid 
     */
    public function get_count($hitsid) {
        $this->db = M("Hits");
        $r = $this->db->where(array('hitsid' => $hitsid))->find();
        if (!$r) {
            return false;
        }
        return $r;
    }

    /**
     * 增加点击数 
     */
    public function hits($hitsid) {
        if (empty($hitsid)) {
            return false;
        }
        $this->db = M("Hits");
        $r = $this->db->where(array('hitsid' => $hitsid))->find();
        if (!$r) {
            return false;
        }
        $time = time();
        $views = $r['views'] + 1;
        $yesterdayviews = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayviews'] : $r['yesterdayviews'];
        $dayviews = (date('Ymd', $r['updatetime']) == date('Ymd', $time)) ? ($r['dayviews'] + 1) : 1;
        $weekviews = (date('YW', $r['updatetime']) == date('YW', $time)) ? ($r['weekviews'] + 1) : 1;
        $monthviews = (date('Ym', $r['updatetime']) == date('Ym', $time)) ? ($r['monthviews'] + 1) : 1;
        $data = array(
            'views' => $views,
            'yesterdayviews' => $yesterdayviews,
            'dayviews' => $dayviews,
            'weekviews' => $weekviews,
            'monthviews' => $monthviews,
            'updatetime' => $time
        );
        $status = $this->db->where(array('hitsid' => $hitsid))->save($data);
        return $status;
    }

}

?>
