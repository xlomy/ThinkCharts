<?php

/**
 * 友情链接标签
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class LinksTagLib {

    /**
     * 获取友情链接列表（type_list）
     * 参数名	 是否必须	 默认值	 说明
     * order	 是	 id DESC	 排序方式
     * termsid	 否	 null	 分类ID
     * id	 否	 null	 链接ID 
     */
    public function type_list($data) {
        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = to_guid_string($data);
        if ($cache && $return = S($cacheID)) {
            return $return;
        }
        $termsid = (int) $data['termsid'];
        $id = (int) $data['id'];
        $num = empty($data['num']) ? 10 : (int) $data['num'];
        $order = empty($data['order']) ? "id DESC" : $data['order'];
        $db = M("Links");
        $where = array();
        if ($id > 0) {
            $where['id'] = array("EQ", $id);
            $data = $db->where($where)->find();
        } else {
            if (isset($data['termsid'])) {
                $where['termsid'] = array("EQ", $termsid);
                $data = $db->where($where)->order($order)->limit($num)->select();
            }
        }
        //结果进行缓存
        if ($cache) {
            S($cacheID, $data, $cache);
        }
        return $data;
    }

}

?>
