<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<!doctype html>
<!--[if lt IE 8 ]> <html class="no-js ie6-7"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<title>
<if condition=" isset($SEO['title']) && !empty($SEO['title']) ">{$SEO['title']}</if>
{$SEO['site_title']}</title>
<link rel="stylesheet" href="{$Config.siteurl}statics/blog/css/style.css" type="text/css" media="screen" />
<link rel='stylesheet' id='wp-recentcomments-css'  href='{$Config.siteurl}statics/blog/css/wp-recentcomments.css?ver=2.0.6' type='text/css' media='screen' />
<link rel="alternate" type="application/rss+xml" title="{$SEO['site_title']} - Rss" href="{$Config.siteurl}index.php?m=Rss&rssid={$catid}" />
<meta name="generator" content="ThinkPHP Shuipf" />
<meta name="description" content="{$SEO['description']}" />
<meta name="keywords" content="{$SEO['keyword']}" />
<link rel="canonical" href="{$Config.siteurl}" />
<!--[if IE 7]>
<style type="text/css">
#sidebar {
    padding-top:40px;
}
.cm #commentform p {
	float:none;
	clear:none;
}
</style>
<![endif]-->
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "{$config_siteurl}",
    JS_ROOT: "statics/js/"
};
</script>
<script type='text/javascript' src='{$Config.siteurl}statics/js/jquery.js'></script>
<script type='text/javascript' src='{$Config.siteurl}statics/blog/js/ls.js'></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/ajaxForm.js"></script>
<!--html5 SHIV的调用-->
<script type='text/javascript' src='{$Config.siteurl}statics/blog/js/html5.js'></script>
</head>
<body  class="single single-post postid-111 single-format-standard">
<!--header START-->
<template file="Contents/header.php"/>
<!--header END-->
<div id="main" class="grid"> 
  <!--主体内容开始-->
  <div id="content" class="g-u" role="主内容">
    <div class="block">
      <section class="profile">
        <h1 class="author-page-h1" id=""> 水平凡的档案 </h1>
        <dl>
          <dt>真名</dt>
          <dd>GongJingPing<i>（自己用输入法打...）</i></dd>
          <dt>网名</dt>
          <dd>水平凡<i>（水的平凡，和不可缺少...）</i></dd>
          <dt>邮箱</dt>
          <dd>admin@abc3210.com<i>（非诚勿扰...）</i></dd>
          <dt>蜗居</dt>
          <dd>厦门<i>（美丽的鹭岛城市）</i></dd>
          <dt>职业</dt>
          <dd id="">挨踢程序员<i>（工龄1年，混江湖中，求包养...）</i></dd>
          <dt>就职公司</dt>
          <dd>厦门小鱼网<i>（不是你想象中的那么好，也不是你想象中的那么糟）</i></dd>
          <dt>毕业学院</dt>
          <dd>软件学院<i>（有山、有水、有人家...当然也有妹子-__,-!）</i></dd>
          <dt>爱好</dt>
          <dd>看书 | 数码 | 乒乓球 | 羽毛球 | Girl<i>（其实就是个彻头彻尾的宅男...）</i></dd>
          <dt>自恋宣言</dt>
          <dd>下辈子我一定要投胎做女人，然后嫁个象我这样的男人....<i>（无语ing...）</i></dd>
        </dl>
      </section>
      <h1 class="author-page-h1">水平凡的微博 </h1>
      <div class="wei-bo">
        <iframe frameborder="0" scrolling="no" src="http://v.t.qq.com/show/show.php?n=shuipf&w=0&h=498&fl=2&l=30&o=27&c=0&si=14649fa8cd3e130cc5e28df4dc385966e8a4f55f" width="100%" height="498"></iframe>
      </div>
    </div>
    <div class="cm block">
      <div id="ds-reset"></div>
    </div>
  </div>
  <!--主体内容结束-->
  <template file="Contents/sidebar.php"/> 
</div>
<template file="Contents/footer.php"/> 
<!--[if lte IE 6]>
<script src="http://letskillie6.googlecode.com/svn/trunk/2/zh_CN.js"></script>
<![endif]--> 
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F6a7ac600fcf5ef3f164732dcea2e2ba5' type='text/javascript'%3E%3C/script%3E"));
</script> 
<script language="javascript" type="text/javascript" src="{$Config.siteurl}statics/js/artDialog/artDialog.js?skin=blue"></script> 
<script type="text/javascript" charset="utf-8" src="{$Config.siteurl}statics/js/lazyload.js"></script> 
<!--代码高亮--> 
<script type="text/javascript" charset="utf-8" src="{$Config.siteurl}statics/js/ueditor/third-party/SyntaxHighlighter/shCore.js"></script>
<link rel="stylesheet" type="text/css" href="{$Config.siteurl}statics/js/ueditor/third-party/SyntaxHighlighter/shCoreDefault.css"/>
<script type="text/javascript">
// jquery.autoIMG.js - 2010-04-02 - Tang Bin - http://planeArt.cn/ - MIT Licensed
(function ($) {
    // 检测是否支持css2.1 max-width属性
    var isMaxWidth = 'maxWidth' in document.documentElement.style,
        // 检测是否IE7浏览器
        isIE7 = !-[1, ] && !('prototype' in Image) && isMaxWidth;

    $.fn.autoIMG = function () {
        var maxWidth = this.width();
		if(maxWidth>666){
			maxWidth = 666;
		}
        return this.find('img').each(function (i, img) {
            // 如果支持max-width属性则使用此，否则使用下面方式
            if (isMaxWidth) return img.style.maxWidth = maxWidth + 'px';
            var src = img.src;

            // 隐藏原图
            img.style.display = 'none';
            img.removeAttribute('src');

            // 获取图片头尺寸数据后立即调整图片
            imgReady(src, function (width, height) {
                // 等比例缩小
                if (width > maxWidth) {
                    height = maxWidth / width * height, width = maxWidth;
                    img.style.width = width + 'px';
                    img.style.height = height + 'px';
                };
                // 显示原图
                img.style.display = '';
                img.setAttribute('src', src);
            });

        });
    };

    // IE7缩放图片会失真，采用私有属性通过三次插值解决
    isIE7 && (function (c, d, s) {
        s = d.createElement('style');
        d.getElementsByTagName('head')[0].appendChild(s);
        s.styleSheet && (s.styleSheet.cssText += c) || s.appendChild(d.createTextNode(c))
    })('img { -ms-interpolation-mode:bicubic }', document);

    /**
     * 图片头数据加载就绪事件
     * @see		http://www.planeart.cn/?p=1121
     * @param	{String}	图片路径
     * @param	{Function}	尺寸就绪 (参数1接收width; 参数2接收height)
     * @param	{Function}	加载完毕 (可选. 参数1接收width; 参数2接收height)
     * @param	{Function}	加载错误 (可选)
     */
    var imgReady = (function () {
        var list = [],
            intervalId = null,

            // 用来执行队列
            tick = function () {
                var i = 0;
                for (; i < list.length; i++) {
                    list[i].end ? list.splice(i--, 1) : list[i]();
                };
                !list.length && stop();
            },

            // 停止所有定时器队列
            stop = function () {
                clearInterval(intervalId);
                intervalId = null;
            };

        return function (url, ready, load, error) {
            var check, width, height, newWidth, newHeight, img = new Image();

            img.src = url;

            // 如果图片被缓存，则直接返回缓存数据
            if (img.complete) {
                ready(img.width, img.height);
                load && load(img.width, img.height);
                return;
            };
            // 检测图片大小的改变
            width = img.width;
            height = img.height;
            check = function () {
                newWidth = img.width;
                newHeight = img.height;
                if (newWidth !== width || newHeight !== height ||
                // 如果图片已经在其他地方加载可使用面积检测
                newWidth * newHeight > 1024) {
                    ready(newWidth, newHeight);
                    check.end = true;
                };
            };
            check();
            // 加载错误后的事件
            img.onerror = function () {
                error && error();
                check.end = true;
                img = img.onload = img.onerror = null;
            };
            // 完全加载完毕的事件
            img.onload = function () {
                load && load(img.width, img.height);
                !check.end && check();
                // IE gif动画会循环执行onload，置空onload即可
                img = img.onload = img.onerror = null;
            };
            // 加入队列中定期执行
            if (!check.end) {
                list.push(check);
                // 无论何时只允许出现一个定时器，减少浏览器性能损耗
                if (intervalId === null) intervalId = setInterval(tick, 40);
            };
        };
    })();

})(jQuery);
$(document).ready(function (){
	$("#content article p").autoIMG();
	var val;
	$('.J_CmFormField').bind('blur',function(){
		LS.item($(this).attr('id'),$(this).val());
	}).each(function(){
		val = LS.item($(this).attr('id'));
		if(val != null) $(this).val(val);
	});
	$("img").lazyload({
		placeholder:"{$Config.siteurl}statics/images/image-pending.gif",
		effect:"fadeIn"
	});
	//点击
	$.get("{$Config.siteurl}api.php?m=Hits&catid={$catid}&id={$id}",function(data){
		$("#hits").html(data.views);
	},"json");
	var histories = new Histories();
	histories.appendTo('.J_Histories');
	var data = {
		'id' : '{$id}',
		'title' : "{$title}",
		'url' : '{$url}'
	};
	histories.save(data);
});
//代码高亮
SyntaxHighlighter.highlight();    
//评论
var commentsQuery = {
    'catid': '{$catid}',
    'id': '{$id}',
    'size': 10
};
(function () {
    var ds = document.createElement('script');
    ds.type = 'text/javascript';
    ds.async = true;
    ds.src = GV.DIMAUB+'statics/js/comment/embed.js';
    ds.charset = 'UTF-8';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ds);
})();
//评论结束
</script> 
</body>
</html>