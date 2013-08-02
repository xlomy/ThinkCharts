<?php

/**
 * 下载相关
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class DownloadAction extends BaseAction {

    /**
     * 显示下载页面 
     */
    public function index() {
        //栏目ID
        $catid = (int) $this->_get('catid');
        //信息ID
        $id = (int) $this->_get('id');
        //编号
        $k = (int) $this->_get('k');
        //字段名称
        $f = $this->_get('f');
        if (!$catid || !$id || !$f) {
            $this->error("参数有误！");
        }
        $Category = F('Category');
        $Model = F('Model');
        //模型ID
        $modelid = $Category[$catid]['modelid'];
        $Model_field = F("Model_field_" . $modelid);
        //主表名称
        if ((int) $Model_field[$f]['issystem'] == 1) {
            $tablename = ucwords($Model[$modelid]['tablename']);
        } else {
            $tablename = ucwords($Model[$modelid]['tablename']) . "_data";
        }

        //字段配置
        $setting = unserialize($Model_field[$f]['setting']);
        if ($setting) {
            //字段信息
            $downfiles = M($tablename)->where(array("id" => $id))->getField($f);
            $downfiles = unserialize($downfiles);
            $info = $downfiles[$k];

            if (!$info) {
                $this->error("该下载地址已经失效！");
            }
            //加验证码
            $aut = md5(get_client_ip() . substr($_SERVER['HTTP_USER_AGENT'], 0, 254));
            //加密
            //格式：aut|栏目ID|信息id|下载编号|字段
            $key = authcode($aut . "|" . $catid . "|" . $id . "|" . $k . "|" . $f, "", '', 3600);
            $this->assign("fileurl", U("Download/d", "key=".  str_replace("%2F","%*2F",urlencode($key))));
            $this->assign("filename", $info['filename']);
            $this->assign("point", $info['point']);
            $this->assign("groupid", $info['groupid']);
            $this->assign("Member_group", F("Member_group"));
            $this->assign("SEO", seo($catid, urldecode($info['filename']), '', ''));
            $this->display("Public:download");
        } else {
            $this->error("出现错误，请联系管理员更新缓存！");
        }
    }

    /**
     * 文件下载 
     */
    public function d() {
        $aut = md5(get_client_ip() . substr($_SERVER['HTTP_USER_AGENT'], 0, 254));
        $_GET['key'] = str_replace(array("%*2F"," "),array("/","+"),$_GET['key']);
        //格式：aut|栏目ID|信息id|下载编号|字段
        $key = explode("|", authcode($_GET['key'], "DECODE"));
        //栏目ID
        $catid = $key[1];
        //信息ID
        $id = $key[2];
        //编号
        $k = $key[3];
        //字段名称
        $f = $key[4];
        $Category = F('Category');
        $Model = F('Model');
        //模型ID
        $modelid = $Category[$catid]['modelid'];
        $Model_field = F("Model_field_" . $modelid);
        //主表名称
        if ((int) $Model_field[$f]['issystem'] == 1) {
            $tablename = ucwords($Model[$modelid]['tablename']);
        } else {
            $tablename = ucwords($Model[$modelid]['tablename']) . "_data";
        }

        if ($aut == $key[0]) {
            $downfiles = M($tablename)->where(array("id" => $id))->getField($f);
            $downfiles = unserialize($downfiles);
            $info = $downfiles[$k];
            //判断会有组
            if ((int) $info['groupid'] > 0 || (int) $info['point'] > 0) {
                if (!AppframeAction::$Cache['uid']) {
                    $this->error("请登陆后再下载！", U("Member/Index/login", "forward=" . urlencode(get_url())));
                }
                if ((int) $info['groupid'] > 0 && (int) AppframeAction::$Cache['User']['groupid'] != (int) $info['groupid']) {
                    $this->error("您所在的会有组不能下载该附件！");
                }
                if ((int) $info['point'] > 0) {
                    $point = 0 - $info['point'];
                    $status = service("Passport")->user_integral(AppframeAction::$Cache['uid'], $point);
                    if ($status == -1) {
                        $this->error("您当前的积分不足，无法下载！");
                    } else if ($status == false) {
                        $this->error("系统出现错误，请联系管理员！");
                    }
                    //下载记录----暂时木有这功能，后期增加
                }
            }
            $fileurl = $info['fileurl'];
            if( !urlDomain(CONFIG_SITEURL) ){
                $urlDomain = urlDomain(get_url());//当前页面地址域名
            }else{
                $urlDomain = urlDomain(CONFIG_SITEURL);
            }
            //不管附件地址是远程地址，还是不带域名的地址，都进行替换
            $fileurl = str_replace($urlDomain,"",$fileurl);
           
            //远程文件
            if (strpos($fileurl, ':/') ) {
                header("Location: $fileurl");
                exit;
            }
            
            //取得文件后缀
            $houz = "." . fileext(basename($fileurl));
            $fileurl = SITE_PATH.'/'.$fileurl;
            if(file_exists($fileurl)){
                $this->downfiles($fileurl, urldecode($info['filename'] . $houz));
            }else{
                $this->error("需要下载的文件不存在！");
            }
        } else {
            $this->error("下载地址不正确！");
        }
    }

    //开始下载
    protected function downfiles($file, $basename) {
        //处理中文文件名
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            $filename = basename(iconv("UTF-8", "GB2312//IGNORE", $basename));
        } else {
            $filename = basename($basename);
        }
        header("Content-type: application/octet-stream");
        $encoded_filename = urlencode($filename);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header("Content-Disposition: attachment; filename*=\"utf8''" . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Content-Length: " . filesize($file));
        readfile($file);
    }

}

?>
