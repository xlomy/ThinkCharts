<?php

/**
 * 附件接口
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class AttachmentService {

    private $db, $path, $Config, $Ftpstatus, $Ftp;
    //时间
    public $time;
    //上传用户ID
    public $userid;
    //是否后台上传
    public $isadmin;
    //上传模块
    public $module;
    //上传栏目ID
    public $catid = 0;
    // 上传日期目录
    public $dateFormat = "Y/m";
    //附件临时编号
    public $filepass;
    //上传成功以后，完整附件物理地址
    public $filepath;
    //上传成功以后，完整的网络地址
    public $filehttp;

    function __construct($variable) {
        extract($variable);
        $this->module = $module ? strtolower($module) : "contents";
        $this->userid = AppframeAction::$Cache['uid'];
        $this->isadmin = (int)$isadmin;
        $this->catid = (int) $catid;
        $this->time = time();
        //附件数据库模型
        $this->db = D("Attachment");
        //附件上传存放目录
        $this->path = C("UPLOADFILEPATH");
        //网站配置
        $this->Config = F("Config");
        if ((int) $this->Config['ftpstatus']) {
            import('Ftp');
            $this->Ftp = new Ftp();
            $this->ftpsta = $this->Ftp->connect($this->Config['ftphost'], $this->Config['ftpuser'], $this->Config['ftppassword'], $this->Config['ftpport'], $this->Config['ftppasv'], $this->Config['ftpssl'], $this->Config['ftptimeout']);
            $this->Ftpstatus = true;
        }
    }

    //设置附件路径
    public function path($path) {
        $this->path = $path;
    }

    //设置用户ID
    public function set_userid($uid = false) {
        $this->userid = (int) $uid;
    }

    /**
     * 图片加水印 
     * @param type $source 原图文件名。
     * @param type $water 水印图片文件名
     * @param type $savename 要保存的图片名，如果留空则用source
     * @param type $alpha  水印图片的alpha值，默认为80，范围为0~100
     * @param type $waterPos 水印位置。
     * @param type $quality jpg图片质量
     */
    public function water($source, $water = null, $savename = null, $alpha = null, $waterPos = null, $quality = null) {
        import("Image");
        //设置默认水印
        if($water == ''){
            $water = SITE_PATH.$this->Config['watermarkimg'];
        }
        //图像信息
        $sInfo = Image::getImageInfo($source);
        //如果图片小于系统设置，不进行水印添加
        if ($sInfo["width"] < (int)$this->Config['watermarkminwidth'] || $sInfo['height'] < (int)$this->Config['watermarkminheight']){
            return false;
        }
        //水印位置
        if(empty($waterPos)){
            $waterPos = (int)$this->Config['watermarkpos'];
        }
        //水印透明度
        if(empty($alpha)){
            $alpha = (int)$this->Config['watermarkpct'];
        }
        //jpg图片质量
        if(empty($quality)){
            $quality = (int)$this->Config['watermarkquality'];
        }
        return Image::water($source, $water, $savename, $alpha, $waterPos, $quality);
    }

    /**
     * 远程保存
     * @param $value 传入下载内容
     * @param $watermark 是否加入水印
     * @param $ext 下载扩展名
     */
    public function download($value, $watermark= '' , $ext = 'gif|jpg|jpeg|bmp|png') {
        //检查是否有开启CURL
        if (!function_exists('curl_init')) {
            return $value;
        }
        if($watermark == ''){
            if($this->Config['watermarkenable']){
                $watermark = true;
            }
        }
        
        $curl = curl_init();
        //正则匹配是否有符合数据
        if (!preg_match_all("/(href|src)=([\"|']?)([^ \"'>]+\.($ext))\\2/i", $value, $matches)) {
            return $value;
        }
        $remotefileurls = array();
        //过滤域名 
        $NoDomain = explode("|", CONFIG_FILEEXCLUDE);
        //当前程序所在域名地址
        $NoDomain[] = urlDomain(get_url());
        //附件地址
        $upload_url = urlDomain(CONFIG_SITEFILEURL);
        foreach ($matches[3] as $matche) {
            //过滤远程地址
            if (strpos($matche, '://') === false) {
                continue;
            }
            //过滤后台设置的域名，和本站域名
            if (in_array(urlDomain($matche), $NoDomain)) {
                continue;
            }
            $remotefileurls[] = $matche;
        }
        //保存目录
        $FilePath = $this->FilePath();
        $oldpath = $newpath = array();
        import('UploadFile');
        $upload = new UploadFile();
        foreach ($remotefileurls as $k => $file) {
            if (strpos($file, '://') === false || strpos($file, $upload_url) !== false) {
                continue;
            }
            //取得文件扩展
            $file_fileext = fileext($file);
            //取得文件名
            $file_name = basename($file);
            //保存文件名
            $filename = uniqid() . "." . $file_fileext;
            // 设置你需要抓取的URL
            curl_setopt($curl, CURLOPT_URL, cn_urlencode($file));
            // 设置header
            curl_setopt($curl, CURLOPT_HEADER, 0);
            // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            //超时设置
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            // 运行cURL，请求网页
            $filedata = curl_exec($curl);
            //保存图片
            $status = $this->build_file($filedata, $FilePath . "/" . $filename);
            if ($status) {
                //加水印
                if($watermark){
                    $this->water($FilePath . "/" . $filename, "", $FilePath . "/" . $filename);
                }
                $oldpath[] = $file;
                $newpath[] = str_replace($this->path, CONFIG_SITEFILEURL, $FilePath . "/" . $filename);
                $info = array(
                    "name" => $file_name,
                    "type" => "",
                    "size" => filesize($FilePath . "/" . $filename),
                    "key" => "",
                    "extension" => $file_fileext,
                    "savepath" => $FilePath,
                    "savename" => "/" . $filename,
                    "hash" => md5(str_replace($this->path, "", $FilePath . "/" . $filename)),
                );
                $aid = $this->FileData($info);
                //设置标识
                $this->upload_json($aid, $this->filehttp, $filename);
                //FTP
                if($this->Ftpstatus){
                    // 远程存放地址
                    $remote = $this->Config['ftpuppat'] . str_replace(SITE_PATH . "/", "", $FilePath . "/" . $filename);
                    if($upload->FTPuplode($FilePath . "/" . $filename, $remote)){
                        //删除
                        unlink($FilePath . "/" . $filename);
                    }
                }
            }
        }
        // 关闭URL请求
        curl_close($curl);
        $value = str_replace($oldpath, $newpath, $value);
        return $value;
    }

    /**
     * 保存上传成功后的信息到数据库
     * @param type $info 
     */
    public function FileData($info, $isthumb = '0') {
        /**
         * $info
         * array (
          'name' => '6.jpg',
          'type' => 'application/octet-stream',
          'size' => 112102,
          'key' => 0,
          'extension' => 'jpg',
          'savepath' => '/home/wwwroot/lvyou.abc3210.com/d/file/contents/2012/07',
          'savename' => '/5002ba343fc9d.jpg',
          'hash' => '77b5118c1722da672b0ddce3c4388e64',
          )
         */
        if (!is_array($info)) {
            return false;
        }
        //保存上传后的物理地址
        $this->filepath = $info['savepath'] . $info['savename'];
        $this->filehttp = CONFIG_SITEFILEURL . str_replace($this->path, "", $info['savepath'] . $info['savename']);
        
        $db = M("Attachment");
        $aid = $db->add(array(
                    //模块名称
                    "module" => $this->module,
                    //栏目ID
                    "catid" => $this->catid,
                    //附件名称
                    "filename" => $info['name'],
                    //附件路径，不包含附件目录 例如 contents/2012/07/5002ba343fc9d.jpg
                    "filepath" => str_replace($this->path, "", $info['savepath'] . $info['savename']),
                    //附件大小
                    "filesize" => $info['size'],
                    //附件扩展名
                    "fileext" => $info['extension'],
                    //是否为图片附件
                    "isimage" => in_array($info['extension'], array("jpg", "png", "jpeg", "gif")) ? 1 : 0,
                    //是否为缩略图
                    "isthumb" => $isthumb,
                    //上传用户ID
                    "userid" => $this->userid,
                    //是否后台上传
                    'isadmin' => $this->isadmin,
                    //上传时间
                    "uploadtime" => $this->time,
                    //上传IP
                    "uploadip" => get_client_ip(),
                    //附件状态
                    "status" => 0,
                    //附件hash
                    "authcode" => $info['hash'],
                ));
        return $aid;
    }

    /**
     * 取得文件保存路径
     * @param type $catid 栏目ID，当为0的时候，直接使用模块目录
     */
    public function FilePath() {
        $this->FilePath = $this->path . $this->module . "/" . date($this->dateFormat, $this->time);
        //检测目录是否存在，不存在创建
        if (!is_dir($this->FilePath)) {
            if (!mkdir($this->FilePath, 0777,true)) {
                return false;
            }
        }
        return $this->FilePath;
    }

    /**
     * 删除附件。
     * @param type $aid 附件id
     */
    public function del($aid) {
        if ($this->Ftpstatus) {
            if ($this->ftpsta == false) {
                $this->error = 'FTP链接失败' . $this->Config['ftphost'];
                return false;
            }
        }
        $data = $this->db->where(array("aid" => $aid))->find();
        if ($data) {
            $filepath = $data['filepath'];
            $status = $this->db->where(array("aid" => $aid))->delete();
            if ($status) {
                //FTP删除
                if ($this->Ftpstatus) {
                    // 远程存放地址
                    $remote = $this->Config['ftpuppat'] . str_replace(SITE_PATH . "/", "", $this->path . $filepath);
                    $status = $this->FTPdel($remote);
                    return $status;
                } else {
                    return unlink($this->path . $filepath);
                }
            }
        } else {
            return false;
        }
    }

    /**
     * FTP文件删除
     * @param type $path FTP上的文件路径
     */
    public function FTPdel($path) {
        if ($this->Ftpstatus) {
            return $this->Ftp->f_delete($path);
        }
        return false;
    }

    /**
     * FTP文件夹删除
     * @param type $path FTP上的文件夹路径
     * @param boolean $enforce 强制删除
     */
    public function FTPrmdir($path, $enforce = false) {
        if ($this->Ftpstatus) {
            return $this->Ftp->rmdir($path, $enforce);
        }
        return false;
    }

    /**
     * 生成文件
     * @param type $file 需要写入的文件或者二进制流
     * @param type $filename 需要生成的文件名的绝对路径
     * @return boolean 
     */
    protected function build_file($file, $filename) {
        $write = @fopen($filename, "w");
        if ($write == false) {
            return false;
        }
        if (fwrite($write, $file) == false) {
            return false;
        }
        if (fclose($write) == false) {
            return false;
        }
        return true;
    }

    /**
     * 通过附件关系删除附件 
     * @param type $keyid 关联ID
     * @return boolean 布尔值
     */
    public function api_delete($keyid) {
        if (empty($keyid)) {
            return false;
        }
        $db = M("AttachmentIndex");
        $data = $db->where(array("keyid" => $keyid))->select();
        if ($data) {
            foreach ($data as $aid) {
                //统计使用同一个附件的次数，如果大于一，表示还有其他地方使用，将不删除附件
                $count = $db->where(array("aid" => $aid['aid']))->count();
                if ($count > 1) {
                    //只删除附件关系，不删除真实附件
                    continue;
                } else {
                    if ($this->del($aid['aid'])) {
                        
                    } else {
                        return false;
                    }
                }
            }
        }
        $db->where(array("keyid" => $keyid))->delete();
        return true;
    }

    /**
     * 附件更新接口.
     * @param string $content 可传入空，html，数组形式url，url地址，传入空时，以cookie方式记录。
     * @param string 传入附件关系表中的组装id
     * @isurl intval 为本地地址时设为1,以cookie形式管理时设置为2
     */
    public function api_update($content, $keyid, $isurl = 0) {
        $keyid = trim($keyid);
        $isurl = intval($isurl);
        if ($isurl == 2 || empty($content)) {
            $this->api_update_cookie($keyid);
        } else {
            $att_index_db = M("Attachment_index");
            //http附件地址 http://file.abc3210.com/d/file/
            $upload_url = CONFIG_SITEFILEURL;
            if (strpos($upload_url, '://') !== false) {
                $pos = strpos($upload_url, "/", 8);
                //附件域名 http://file.abc3210.com/
                $domain = substr($upload_url, 0, $pos) . '/';
                //附件目录 d/file/
                $dir_name = substr($upload_url, $pos + 1);
            }else{
                //如果附件地址是类似“/d/file/”这样的
                $dir_name = $upload_url;
            }
            if ($isurl == 0) {//分析$content中的附件地址
                $pattern = '/(href|src)=\"(.*)\"/isU';
                preg_match_all($pattern, $content, $matches);
                if (is_array($matches) && !empty($matches)) {
                    //移除数组中的重复的值
                    $att_arr = array_unique($matches[2]);
                    //开始计算md5
                    foreach ($att_arr as $_k => $_v){
                        $att_arrs[$_k] = md5(str_replace(array($domain, $dir_name), '', $_v));
                    }
                }
                
            } elseif ($isurl == 1) {//不用分析$content中的地址，$content本身就是一个地址，或者是一个一个数组的情况
                //如果传入的是数组
                if (is_array($content)) {
                    $att_arr = array_unique($content);
                    foreach ($att_arr as $_k => $_v){
                        $att_arrs[$_k] = md5(str_replace(array($domain, $dir_name), '', $_v));
                    }
                } else {
                    $att_arrs[] = md5(str_replace(array($domain, $dir_name), '', $content));
                }
            }
            //删除旧的关联关系，从新绑定附件和信息关系
            $att_index_db->where(array('keyid' => $keyid))->delete();
            if (is_array($att_arrs) && !empty($att_arrs)) {
                foreach ($att_arrs as $r) {
                    $aid = M("Attachment")->where(array('authcode' => $r))->getField("aid");
                    if ($aid) {
                        M("Attachment")->where(array('aid' => $aid))->save(array('status' => 1));
                        $att_index_db->add(array('keyid' => $keyid, 'aid' => $aid),array(),true);
                    }
                }
            }
        }
        //删除附件cookie
        cookie('att_json', NULL);
        return true;
    }

    /**
     * cookie 方式关联附件
     * @param type $keyid 关联ID
     * @return boolean 失败返回false
     */
    private function api_update_cookie($keyid) {
        $att_index_db = M("Attachment_index");
        $att_json = cookie('att_json');
        if ($att_json) {
            $att_cookie_arr = explode('||', $att_json);
            $att_cookie_arr = array_unique($att_cookie_arr);
        } else {
            return false;
        }
        foreach ($att_cookie_arr as $_att_c){
            $att[] = json_decode($_att_c, true);
        }
        foreach ($att as $_v) {
            M("Attachment")->where(array('aid' => $_v['aid']))->save(array('status' => 1));
            $att_index_db->add(array('keyid' => $keyid, 'aid' => $_v['aid']));
        }
    }
    
    /**
     * 设置upload上传的json格式cookie 
     * @param type $aid 附件ID
     * @param type $src 附件地址
     * @param type $filename 附件名称
     * @return boolean 返回布尔值
     */
    public function upload_json($aid, $src, $filename) {
        $arr['aid'] = $aid;
        $arr['src'] = trim($src);
        $arr['filename'] = urlencode($filename);
        $json_str = json_encode($arr);
        $att_arr_exist = cookie('att_json');
        $att_arr_exist_tmp = explode('||', $att_arr_exist);
        if (is_array($att_arr_exist_tmp) && in_array($json_str, $att_arr_exist_tmp)) {
            return true;
        } else {
            $json_str = $att_arr_exist ? $att_arr_exist . '||' . $json_str : $json_str;
            cookie('att_json', $json_str);
            return true;
        }
    }

}

?>
