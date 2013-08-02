<?php

/**
 * 前台附件上传
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class UploadAction extends BaseAction {

    const isadmin = 0; //是否后台

    function _initialize() {
        if (ACTION_NAME == 'swfupload') {
            //初始化站点配置信息
            $this->initSite();
        } else {
            parent::_initialize();
        }
    }

    /**
     * swfupload 上传 
     * 通过swf上传成功以后回调处理时会调用swfupload_json方法增加cookies！
     */
    public function swfupload() {
        if (IS_POST) {
            $sess_id = I("post.sessid", 0);
            $key = md5(C("AUTHCODE") . $sess_id . self::isadmin);
            //密钥验证
            if (I("post.swf_auth_key") != $key) {
                exit("0,权限认证失败！" . I("post.swf_auth_key") . "|" . C("AUTHCODE"));
            }
            //回调函数
            $Callback = false;
            //用户ID
            $upuserid = I('post.uid', 0, 'intval');
            //用户组
            $groupid = I("post.groupid", 8, "intval");
            $Member_group = F("Member_group");
            if ((int) $Member_group[$groupid]['allowattachment'] < 1) {
                exit("0,所在的用户组没有附件上传权限！");
            }
            //取得栏目ID
            $catid = I('post.catid', 0, 'intval');
            //取得模块名称
            $module = I('post.module', '', 'trim,strtolower');

            //上传处理类
            $upload = new UploadFile();
            //获取附件服务
            $Attachment = service("Attachment", array("module" => $module, "catid" => $catid, "isadmin" => self::isadmin));
            //设置上传用户
            $Attachment->set_userid($upuserid);
            //允许上传的文件类型，直接使用网站配置的
            $allowExts = CONFIG_QTUPLOADALLOWEXT;
            //设置上传类型
            $upload->allowExts = explode("|", $allowExts);
            //设置上传大小
            $upload->maxSize = (int) CONFIG_QTUPLOADMAXSIZE * 1024; //单位字节
            //缩略图宽度
            $thumb_width = I('post.thumb_width', 0, 'intval');
            $thumb_height = I('post.thumb_height', 0, 'intval');
            //图片裁减相关设置，如果开启，将不保留原图
            if ($thumb_width && $thumb_height) {
                $upload->thumb = true;
                $upload->thumbRemoveOrigin = true;
                //设置缩略图最大宽度
                $upload->thumbMaxWidth = $thumb_width;
                //设置缩略图最大高度
                $upload->thumbMaxHeight = $thumb_height;
            }
            //是否添加水印  post:watermark_enable 等于1也需要加水印
            if (I('post.watermark_enable', 0, 'intval')) {
                $Callback = array(
                    array("UploadAction", "water"),
                );
            }

            //上传目录 可以单独写个方法，根据栏目ID生成相对于栏目目录附件
            $this->filepath = $upload->savePath = $Attachment->FilePath();

            //开始上传
            if ($upload->upload($Callback)) {
                //上传成功
                $info = $upload->getUploadFileInfo();
                //写入附件数据库信息
                $status = $Attachment->FileData($info[0]);
                if ($status) {
                    if (in_array($info[0]['extension'], array("jpg", "png", "jpeg", "gif"))) {
                        // 附件ID 附件网站地址 图标(图片时为1) 文件名
                        echo "$status," . $Attachment->filehttp . ",1," . str_replace(array("\\", "/"), "", $info[0]['name']);
                        exit;
                    } else {
                        $fileext = $info[0]['extension'];
                        if ($fileext == 'zip' || $fileext == 'rar')
                            $fileext = 'rar';
                        elseif ($fileext == 'doc' || $fileext == 'docx')
                            $fileext = 'doc';
                        elseif ($fileext == 'xls' || $fileext == 'xlsx')
                            $fileext = 'xls';
                        elseif ($fileext == 'ppt' || $fileext == 'pptx')
                            $fileext = 'ppt';
                        elseif ($fileext == 'flv' || $fileext == 'swf' || $fileext == 'rm' || $fileext == 'rmvb')
                            $fileext = 'flv';
                        else
                            $fileext = 'do';

                        echo "$status," . $Attachment->filehttp . "," . $fileext . "," . str_replace(array("\\", "/"), "", $info[0]['name']);
                        exit;
                    }
                } else {
                    //删除已经上传的图片，这里逻辑还要优化
                    @unlink($info[0]['savepath'] . $info[0]['savename']);
                    exit("0,上传成功，但写库失败！");
                }
            } else {
                //上传失败，返回错误
                exit("0," . $upload->getErrorMsg());
            }
        } else {
            exit("0,上传失败！");
        }
    }

}

?>
