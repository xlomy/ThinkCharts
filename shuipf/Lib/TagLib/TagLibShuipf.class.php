<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class TagLibShuipf extends TagLib {

    /**
     * @var type 
     * 标签定义： 
     *                  attr         属性列表 
     *                  close      标签是否为闭合方式 （0闭合 1不闭合），默认为不闭合 
     *                  alias       标签别名 
     *                  level       标签的嵌套层次（只有不闭合的标签才有嵌套层次）
     * 定义了标签属性后，就需要定义每个标签的解析方法了，
     * 每个标签的解析方法在定义的时候需要添加“_”前缀，
     * 可以传入两个参数，属性字符串和内容字符串（针对非闭合标签）。
     * 必须通过return 返回标签的字符串解析输出，在标签解析类中可以调用模板类的实例。
     */
    protected $tags = array(
        //内容标签
        'content' => array('attr' => 'action,cache,num,page,return,where,moreinfo,thumb,order,day', 'level' => 3),
        //Tags标签
        'tags' => array('attr' => 'action,cache,num,page,return,pagetp,pagefun', 'level' => 3),
        //评论标签
        'comment' => array('attr' => 'action,cache,num,return', 'level' => 3),
        //友情链接标签
        'links' => array('attr' => 'action,cache,num,return', 'level' => 3),
        //推荐位标签
        'position' => array('attr' => 'action,cache,num,return', 'level' => 3),
        //SQL标签
        'get' => array("attr" => 'sql,cache,page,dbsource,return,num,pagetp,pagefun', 'level' => 3),
        //模板标签
        'template' => array("attr" => "file", "close" => 0),
        //后台模板标签
        'admintemplate' => array("attr" => "file", "close" => 0),
        //Form标签
        'form' => array("attr" => "function,parameter", "close" => 0),
        //导航表情
        'navigate' => array('attr' => 'cache,catid,space', 'close' => 0),
        //上一篇
        'pre' => array('attr' => 'blank,msg', 'close' => 0),
        //下一篇
        'next' => array('attr' => 'blank,msg', 'close' => 0),
    );

    /**
     * 获取上一篇标签
     * 使用方法：
     *      用法示例：<pre catid="1" id="1" target="1" msg="已经没有了" />
     * 参数说明：
     *          @catid		栏目id，可以传入数字,在内容页可以不传
     *          @id		信息id，可以传入数字,在内容页可以不传
     *          @target		是否新窗口打开，1 是 0否
     *          @msg		当没有上一篇时的提示语
     * @param type $attr
     * @param type $content
     * @return type
     */
    public function _pre($attr, $content) {
        static $_preParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_preParseCache[$cacheIterateId])) {
            return $_preParseCache[$cacheIterateId];
        }
        $tag = $this->parseXmlAttr($attr, 'pre');
        //当没有内容时的提示语
        $msg = !empty($tag['msg']) ? $tag['msg'] : '已经没有了';
        //是否新窗口打开
        $target = !empty($tag['blank']) ? ' target=\"_blank\" ' : '';
        if(!$tag['catid']){
            $tag['catid'] = '$catid';
        }
        if(!$tag['id']){
            $tag['id'] = '$id';
        }
        
        $parsestr = '<?php ';
        $parsestr .= ' $_pre_r = M(ucwords($Model[$Categorys['.$tag['catid'].'][\'modelid\']][\'tablename\']))->where(array("catid"=>'.$tag['catid'].',"status"=>99,"id"=>array("LT",'.$tag['id'].')))->order(array("id" => "DESC"))->field("id,title,url")->find(); ';
        $parsestr .= ' echo $_pre_r?"<a class=\"pre_a\" href=\"".$_pre_r["url"]."\" '.$target.'>".$_pre_r["title"]."</a>":"'.str_replace('"','\"',$msg).'";';
        $parsestr .= ' ?> ';
        $_preParseCache[$cacheIterateId] = $parsestr;
        return $parsestr;
    }

    /**
     * 获取下一篇标签
     * 使用方法：
     *      用法示例：<next catid="1" id="1" target="1" msg="已经没有了" />
     * 参数说明：
     *          @catid		栏目id，可以传入数字,在内容页可以不传
     *          @id		信息id，可以传入数字,在内容页可以不传
     *          @target		是否新窗口打开，1 是 0否
     *          @msg		当没有上一篇时的提示语
     * @param type $attr
     * @param type $content
     * @return type
     */
    public function _next($attr, $content) {
         static $_nextParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_nextParseCache[$cacheIterateId])) {
            return $_nextParseCache[$cacheIterateId];
        }
        $tag = $this->parseXmlAttr($attr, 'pre');
        //当没有内容时的提示语
        $msg = !empty($tag['msg']) ? $tag['msg'] : '已经没有了';
        //是否新窗口打开
        $target = !empty($tag['blank']) ? ' target=\"_blank\" ' : '';
        if(!$tag['catid']){
            $tag['catid'] = '$catid';
        }
        if(!$tag['id']){
            $tag['id'] = '$id';
        }
        
        $parsestr = '<?php ';
        $parsestr .= ' $_pre_r = M(ucwords($Model[$Categorys['.$tag['catid'].'][\'modelid\']][\'tablename\']))->where(array("catid"=>'.$tag['catid'].',"status"=>99,"id"=>array("GT",'.$tag['id'].')))->order(array("id" => "DESC"))->field("id,title,url")->find(); ';
        $parsestr .= ' echo $_pre_r?"<a class=\"pre_a\" href=\"".$_pre_r["url"]."\" '.$target.'>".$_pre_r["title"]."</a>":"'.str_replace('"','\"',$msg).'";';
        $parsestr .= ' ?> ';
        $_nextParseCache[$cacheIterateId] = $parsestr;
        return $parsestr;
    }

    /**
     * 导航标签
     * 使用方法：
     *      用法示例：<navigate catid="$catid" space=" &gt; " />
     * 参数说明：
     *          @catid		栏目id，可以传入数字，也可以传递变量 $catid
     *          @space		分隔符，支持html代码
     *          @cache          缓存时间
     * @staticvar array $_navigateCache
     * @param type $attr 标签属性
     * @param type $content 表情内容
     * @return array|string
     */
    public function _navigate($attr, $content) {
        static $_navigateCache = array();
        $key = md5($attr.$content);
        if (isset($_navigateCache[$key])) {
            return $_navigateCache[$key];
        }
        $cache = (int) $tag['cache'];
        if ($cache) {
            $_navigateCache[$key] = $data = S($key);
            if ($data) {
                return $data;
            }
        }
        $tag = $this->parseXmlAttr($attr, 'navigate');
        //分隔符，支持html代码
        $space = !empty($tag['space']) ? $tag['space'] : '&gt;';
        $catid = $tag['catid'];
        $parsestr = '';
        //如果传入的是纯数字
        if (is_numeric($catid)) {
            $catid = (int) $catid;
            //获取模板中的Categorys变量
            $Categorys = $this->tpl->get('Categorys');
            if (!$Categorys) {
                $Categorys = F('Categorys');
            }
            if (!$Categorys[$catid]) {
                return '';
            }
            //获取当前栏目的 父栏目列表
            $arrparentid = array_filter(explode(',', $Categorys[$catid]['arrparentid'] . ',' . $catid));
            foreach ($arrparentid as $cid) {
                $parsestr[] = '<a href="' . $Categorys[$cid]['url'] . '">' . $Categorys[$cid]['catname'] . '</a>';
            }
            unset($Categorys);
            $parsestr = implode($space, $parsestr);
        } else {
            $parsestr = '';
            $parsestr .= '<?php';
            $parsestr .= '  $arrparentid = array_filter(explode(\',\', $Categorys[$catid][\'arrparentid\'] . \',\' . $catid)); ';
            $parsestr .= '  foreach ($arrparentid as $cid) {';
            $parsestr .= '      $parsestr[] = \'<a href="\' . $Categorys[$cid][\'url\'] . \'">\' . $Categorys[$cid][\'catname\'] . \'</a>\';';
            $parsestr .= '  }';
            $parsestr .= '  echo  implode("' . $space . '", $parsestr);';
            $parsestr .= '?>';
        }
        $_navigateCache[$key] = $parsestr;
        if ($cache) {
            S($key, $_navigateCache[$key], $cache);
        }
        return $_navigateCache[$key];
    }

    /**
     * 模板包含标签 
     * 格式
     * <Admintemplate file="APP/模块/模板"/>
     * @staticvar array $_admintemplateParseCache
     * @param type $attr 属性字符串
     * @param type $content 标签内容
     * @return array 
     */
    public function _Admintemplate($attr, $content) {
        static $_admintemplateParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_admintemplateParseCache[$cacheIterateId])) {
            return $_admintemplateParseCache[$cacheIterateId];
        }
        //分析Admintemplate标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'admintemplate');
        $file = explode("/", $tag['file']);
        $counts = count($file);
        if ($counts < 2) {
            return false;
        } else if ($counts < 3) {
            $file_path = DIRECTORY_SEPARATOR . "Admin" . DIRECTORY_SEPARATOR . "Tpl" . DIRECTORY_SEPARATOR . $tag['file'];
        } else {
            $file_path = DIRECTORY_SEPARATOR . $file[0] . DIRECTORY_SEPARATOR . "Tpl" . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR . $file[2];
        }
        //模板路径
        $TemplatePath = APP_PATH . C("APP_GROUP_PATH") . $file_path . C("TMPL_TEMPLATE_SUFFIX");
        //判断模板是否存在
        if (!file_exists_case($TemplatePath)) {
            return false;
        }
        //读取内容
        $tmplContent = file_get_contents($TemplatePath);
        //解析模板内容
        $parseStr = $this->tpl->parse($tmplContent);
        $_admintemplateParseCache[$cacheIterateId] = $parseStr;
        return $_admintemplateParseCache[$cacheIterateId];
    }

    /**
     * 标签：<Form/>
     * 作用：生成各种表单元素
     * 用法示例：<Form function="date" parameter="name,$valeu"/>
     * 参数说明：
     *          @function		表示所使用的方法名称，方法来源于Form.class.php这个类。
     *          @parameter		所需要传入的参数，支持变量！
     * 
     * @param type $attr
     * @param type $content
     */
    public function _Form($attr, $content) {
        static $_FormParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_FormParseCache[$cacheIterateId])) {
            return $_FormParseCache[$cacheIterateId];
        }

        $tag = $this->parseXmlAttr($attr, 'form');
        $function = $tag['function'];
        if (!$function) {
            return false;
        }

        $parameter = explode(",", $tag['parameter']);
        foreach ($parameter as $k => $v) {
            if ($v == "''" || $v == '""') {
                $v = "";
            }
            $parameter[$k] = trim($v);
        }
        $parameter = $this->arr_to_html($parameter);

        $parseStr = "<?php ";
        $parseStr .= " import(\"Form\");";
        $parseStr .= ' echo call_user_func_array(array("Form","' . $function . '"),' . $parameter . ')';
        //$parseStr .= " echo Form::$function(".$tag['parameter'].");\r\n";
        $parseStr .= " ?>";

        $_FormParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 标签：<template/>
     * 作用：引入其他模板
     * 用法示例：<template file="Member/footer.php"/>
     * 参数说明：
     *          @file	表示需要应用的模板路径。(这里需要说明的是，只能引入当前主题下的模板文件)
     * 
     * @staticvar array $_templateParseCache
     * @param type $attr 属性字符串
     * @param type $content 标签内容
     * @return array 
     */
    public function _template($attr, $content) {
        static $_templateParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_templateParseCache[$cacheIterateId])) {
            return $_templateParseCache[$cacheIterateId];
        }
        //检查CONFIG_THEME是否被定义
        if (!defined("CONFIG_THEME")) {
            return;
        }
        //分析template标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'template');
        $TemplatePath = TEMPLATE_PATH . CONFIG_THEME . DIRECTORY_SEPARATOR . $tag['file'];
        //判断模板是否存在
        if (!file_exists_case($TemplatePath)) {
            //启用默认模板
            $TemplatePath = TEMPLATE_PATH . "Default" . DIRECTORY_SEPARATOR . $tag['file'];
            if (!file_exists_case($TemplatePath)) {
                return;
            }
        }
        //读取内容
        $tmplContent = file_get_contents($TemplatePath);
        //解析模板
        $parseStr = $this->tpl->parse($tmplContent);
        $_templateParseCache[$cacheIterateId] = $parseStr;
        return $_templateParseCache[$cacheIterateId];
    }

    /**
     * 内容标签
     * 标签：<content></content>
     * 作用：内容模型相关标签，可调用栏目，列表等常用信息
     * 用法示例：<content action="lists" catid="$catid"  order="id DESC" num="4" page="$page"> .. HTML ..</content>
     * 参数说明：
     * 	基本参数
     * 		@action		调用方法（必填）
     * 		@page		当前分页号，默认$page，当传入该参数表示启用分页，一个页面只允许有一个page，多个标签使用多个page会造成不可预知的问题。
     * 		@num		每次返回数据量
     * 		@catid		栏目id（必填），列表页，内容页可以使用 $catid 获取当前栏目。
     * 	公用参数：
     * 		@cache		数据缓存时间，单位秒
     *              @pagefun            分页函数，默认page()
     * 		@pagetp		分页模板
     * 		@return		返回值变量名称，默认data
     * 	#当action为lists时，调用栏目列表标签
     * 	#用法示例：<content action="lists" catid="$catid"  order="id DESC" num="4" page="$page"> .. HTML ..</content>
     * 	独有参数：
     * 		@order		排序，例如“id DESC”
     * 		@where		sql语句的where部分 例如：thumb`!='' AND `status`=99（当有该参数时，thumb，catid参数失效）
     * 		@thumb		是否仅必须缩略图，1为调用带缩略图的
     * 		@moreinfo	是否调用副表数据 1为是
     * 	#当action为hits时，调用排行榜
     * 	#用法示例：<content action="hits" catid="$catid"  order="weekviews DESC" num="10"> .. HTML ..</content>
     * 	独有参数：
     * 		@order		排序，例如“weekviews DESC”
     * 		@day		调用多少天内的排行
     * 	#当action为relation时，调用相关文章
     * 	#用法示例：<content action="relation" relation="$relation" catid="$catid"  order="id DESC" num="5" keywords="$keywords"> .. HTML ..</content>
     * 	独有参数：
     * 		@nid		排除id 一般是 $id，排除当前文章
     * 		@keywords	内容页面取值：$keywords，也就是关键字
     * 		@relation		内容页取值$relation，当有$relation时keywords参数失效
     * 	#当action为category时，调用栏目列表
     * 	#用法示例：<content action="category" catid="$catid"  order="listorder ASC" > .. HTML ..</content>
     * 	独有参数：
     * 		@order		排序，例如“id DESC”
     * 
     * +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
      +----------------------------------------------------------
     * @return string|void
      +----------------------------------------------------------
     */
    public function _content($attr, $content) {
        static $content_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr.$content);
        if (isset($content_iterateParseCache[$cacheIterateId])) {
            return $content_iterateParseCache[$cacheIterateId];
        }
        //分析content标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'content');
        $tag['catid'] = $catid = $tag['catid'];
        //每页显示总数
        $tag['num'] = $num = (int) $tag['num'];
        //当前分页参数
        $tag['page'] = $page = (isset($tag['page'])) ? ( (substr($tag['page'], 0, 1) == '$') ? $tag['page'] : (int) $tag['page'] ) : 0;
        //数据返回变量
        $tag['return'] = $return = empty($tag['return']) ? "data" : $tag['return'];
        //方法
        $tag['action'] = $action = trim($tag['action']);
        //sql语句的where部分
        $tag['where'] = $where = $tag['where'];
        //分页模板
        $tag['pagetp'] = $pagetp = (substr($tag['pagetp'], 0, 1) == '$') ? $tag['pagetp'] : '';
        //分页函数，默认page
        $tag['pagefun'] = $pagefun = empty($tag['pagefun']) ? "page" : trim($tag['pagefun']);

        //拼接php代码
        $parseStr = '<?php';
        $parseStr .= ' $content_tag = TagLib("Content");' . "\r\n";
        //如果有传入$page参数，则启用分页。
        if ($page && in_array($action, array('lists'))) {
            //进行信息数量统计 需要 action catid where
            $parseStr .= ' $count = $content_tag->count(' . self::arr_to_html($tag) . ');' . "\r\n";
            //分页函数
            $parseStr .= ' $_page_ = ' . $pagefun . '($count ,' . $num . ',' . $page . ',6,C("VAR_PAGE"),"",true);';
            //设置分页模板，模板必须是变量传递
            if ($pagetp) {
                $parseStr .= ' $_page_->SetPager(\'default\', ' . $pagetp . ');';
            }
            $tag['count'] = '$count';
            $tag['limit'] = '$_page_->firstRow.",".$_page_->listRows';
            //总分页数，生成静态时需要
            $parseStr .= ' $GLOBALS["Total_Pages"] = $_page_->Total_Pages;';
            //显示分页导航
            $parseStr .= ' $pages = $_page_->show("default");';
            //分页总数
            $parseStr .= ' $pagetotal = $_page_->Total_Pages;';
            //总信息数
            $parseStr .= ' $totalsize = $_page_->Total_Size;';
        }
        $parseStr .= ' if(method_exists($content_tag, "' . $action . '")){';
        $parseStr .= ' $' . $return . ' = $content_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' }';

        $parseStr .= ' ?>';
        //解析模板
        $parseStr .= $this->tpl->parse($content);
        $content_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 评论标签
     * 标签：<comment></comment>
     * 作用：评论标签
     * 用法示例：<comment action="get_comment" catid="$catid" id="$id"> .. HTML ..</comment>
     * 参数说明：
     * 	基本参数
     * 		@action		调用方法（必填）
     * 		@catid		栏目id（必填），列表页，内容页可以使用 $catid 获取当前栏目。
     * 	公用参数：
     * 		@cache		数据缓存时间，单位秒
     * 		@return		返回值变量名称，默认data
     * 	#当action为get_comment时，获取评论总数
     * 	#用法示例：<comment action="get_comment" catid="$catid" id="$id"> .. HTML ..</comment>
     * 	独有参数：
     * 		@id				信息ID
     * 	#当action为lists时，获取评论数据列表
     * 	#用法示例：<comment action="lists" catid="$catid" id="$id"> .. HTML ..</comment>
     * 	独有参数：
     * 		@id		信息ID
     * 		@hot		排序方式｛0：最新｝
     * 		@date		时间格式 Y-m-d H:i:s A
     *    #当action为bang时，获取评论排行榜
     * 	#用法示例：<comment action="bang" num="10"> .. HTML ..</comment>
     * 	独有参数：
     * 		@num		返回信息数
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     */
    public function _comment($attr, $content) {
        static $_comment_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr.$content);
        if (isset($_comment_iterateParseCache[$cacheIterateId])) {
            return $_comment_iterateParseCache[$cacheIterateId];
        }
        $tag = $this->parseXmlAttr($attr, 'comment');
        /* 属性列表 */
        $num = (int) $tag['num']; //每页显示总数
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $action = $tag['action']; //方法

        $parseStr = '<?php';
        $parseStr .= ' $comment_tag = TagLib("Comment");';
        $parseStr .= ' if(method_exists($comment_tag, "' . $action . '")){';
        $parseStr .= ' $' . $return . ' = $comment_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' }';
        $parseStr .= ' ?>';
        $parseStr .= $this->tpl->parse($content);
        $_comment_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * Tags标签
     * 标签：<tags></tags>
     * 作用：Tags标签
     * 用法示例：<tags action="lists" tag="$tag" num="4" page="$page" order="updatetime DESC"> .. HTML ..</tags>
     * 参数说明：
     * 	基本参数
     * 		@action		调用方法（必填）
     * 		@page		当前分页号，默认$page，当传入该参数表示启用分页，一个页面只允许有一个page，多个标签使用多个page会造成不可预知的问题。
     * 		@num		每次返回数据量
     * 	公用参数：
     * 		@cache		数据缓存时间，单位秒
     * 		@return		返回值变量名称，默认data
     * 		@pagefun                      分页函数，默认page()
     * 		@pagetp		分页模板
     * 	#当action为lists时，获取tag标签列表
     * 	#用法示例：<tags action="lists" tag="$tag" num="4" page="$page" order="updatetime DESC"> .. HTML ..</tags>
     * 	独有参数：
     * 		@tag	标签名，例如：厦门 支持多个，多个用空格或者英文逗号
     * 		@tagid	标签id 多个使用英文逗号隔开
     * 		@order	排序
     * 		@num	每次返回数据量
     * 	#当action为top时，获取tag点击排行榜
     * 	#用法示例：<tags action="top"  num="4"  order="tagid DESC"> .. HTML ..</tags>
     * 	独有参数：
     * 		@num	每次返回数据量
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     */
    public function _tags($attr, $content) {
        static $_tags_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr.$content);
        if (isset($_tags_iterateParseCache[$cacheIterateId])) {
            return $_tags_iterateParseCache[$cacheIterateId];
        }
        $tag = $this->parseXmlAttr($attr, 'tags');
        /* 属性列表 */
        //每页显示总数
        $tag['num'] = $num = (int) $tag['num'];
        //当前分页参数
        $tag['page'] = $page = (isset($tag['page'])) ? ( (substr($tag['page'], 0, 1) == '$') ? $tag['page'] : (int) $tag['page'] ) : 0;
        //数据返回变量
        $tag['return'] = $return = empty($tag['return']) ? "data" : $tag['return'];
        //方法
        $tag['action'] = $action = trim($tag['action']);
        //sql语句的where部分
        $tag['where'] = $where = $tag['where'];
        //分页模板
        $tag['pagetp'] = $pagetp = (substr($tag['pagetp'], 0, 1) == '$') ? $tag['pagetp'] : '';
        //分页函数，默认page
        $tag['pagefun'] = $pagefun = empty($tag['pagefun']) ? "page" : trim($tag['pagefun']);

        $parseStr = '<?php';
        $parseStr .= ' $Tags_tag = TagLib("Tags");';
        //如果有传入$page参数，则启用分页。
        if ($page && in_array($action, array('lists'))) {
            $parseStr .= ' $count = $Tags_tag->count(' . self::arr_to_html($tag) . ');';
            $parseStr .= ' $_page_ = ' . $pagefun . '($count ,' . $num . ',' . $page . ',6,C("VAR_PAGE"),"",true);';
            //设置分页模板，模板必须是变量传递
            if ($pagetp) {
                $parseStr .= ' $_page_->SetPager(\'default\', ' . $pagetp . ');';
            }
            $tag['count'] = '$count';
            $tag['limit'] = '$_page_->firstRow.",".$_page_->listRows';
            //总分页数，生成静态时需要
            $parseStr .= ' $GLOBALS["Total_Pages"] = $_page_->Total_Pages;';
            //显示分页导航
            $parseStr .= ' $pages = $_page_->show("default");';
            //分页总数
            $parseStr .= ' $pagetotal = $_page_->Total_Pages;';
            //总信息数
            $parseStr .= ' $totalsize = $_page_->Total_Size;';
        }
        $parseStr .= ' if(method_exists($Tags_tag, "' . $action . '")){';
        $parseStr .= '     $' . $return . ' = $Tags_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' };';

        $parseStr .= ' ?>';
        //解析模板
        $parseStr .= $this->tpl->parse($content);
        $_tags_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 友情链接标签
     * 标签：<links></links>
     * 作用：友情链接标签
     * 用法示例：<links action="type_list" termsid="1" id="1"> .. HTML ..</links>
     * 参数说明：
     * 	公用参数：
     * 		@cache		数据缓存时间，单位秒
     * 		@return		返回值变量名称，默认data
     * 	#当action为type_list时，获取tag标签列表
     * 	#用法示例：<links action="type_list" termsid="1" id="1"> .. HTML ..</links>
     * 	独有参数：
     * 		@order		排序方式
     * 		@termsid		分类ID
     * 		@id		链接ID
     * 
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     */
    public function _links($attr, $content) {
        static $_links_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr . $content);
        if (isset($_links_iterateParseCache[$cacheIterateId])) {
            return $_links_iterateParseCache[$cacheIterateId];
        }
        $tag = $this->parseXmlAttr($attr, 'links');
        /* 属性列表 */
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $action = $tag['action']; //方法

        $parseStr = '<?php';
        $parseStr .= ' $links_tag = TagLib("Links");';
        $parseStr .= ' if(method_exists($links_tag, "' . $action . '")){';
        $parseStr .= '     $' . $return . ' = $links_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' };';
        $parseStr .= ' ?>';
        $parseStr .= $this->tpl->parse($content);
        $_links_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 推荐位标签
     * 标签：<position></position>
     * 作用：推荐位标签
     * 用法示例：<position action="position" posid="1"> .. HTML ..</position>
     * 参数说明：
     * 	公用参数：
     * 		@cache		数据缓存时间，单位秒
     * 		@return		返回值变量名称，默认data
     * 	#当action为position时，获取推荐位
     * 	独有参数：
     * 		@posid		推荐位ID(必填)
     * 		@catid		调用栏目ID
     * 		@thumb		是否仅必须缩略图
     * 		@order		排序例如
     * 		@num		每次返回数据量
     * 
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     */
    public function _position($attr, $content) {
        static $_position_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr.$content);
        if (isset($_position_iterateParseCache[$cacheIterateId])) {
            return $_position_iterateParseCache[$cacheIterateId];
        }
        $tag = $this->parseXmlAttr($attr, 'position');
        /* 属性列表 */
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $action = $tag['action']; //方法

        $parseStr = '<?php';
        $parseStr .= ' $Position_tag = TagLib("Position");';
        $parseStr .= ' if(method_exists($Position_tag, "' . $action . '")){';
        $parseStr .= '     $' . $return . ' = $Position_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' };';
        $parseStr .= ' ?>';
        $parseStr .= $this->tpl->parse($content);
        $_position_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 标签：<get></get>
     * 作用：特殊标签，SQL查询标签
     * 用法示例：<get sql="SELECT * FROM shuipfcms_article  WHERE status=99 ORDER BY inputtime DESC" page="$page" num="5"> .. HTML ..</get>
     * 参数说明：
     * 	@sql		SQL语句，强烈建议只用于select类型语句，其他SQL有严重安全威胁，同时不建议直接在SQL语句中使用外部变量，如:$_GET,$_POST等。
     * 	@page		当前分页号，默认$page，当传入该参数表示启用分页，一个页面只允许有一个page，多个标签使用多个page会造成不可预知的问题。
     * 	@num		每次返回数据量
     * 	@cache		数据缓存时间，单位秒
     * 	@return		返回值变量名称，默认data
     * 	@pagefun	                    分页函数，默认page()
     * 	@pagetp		分页模板
     * 
     * +----------------------------------------------------------
     * @param type $attr
     * @param type $content 
     */
    public function _get($attr, $content) {
        static $_get_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr.$content);
        if (isset($_get_iterateParseCache[$cacheIterateId])) {
            return $_get_iterateParseCache[$cacheIterateId];
        }
        $tag = $this->parseXmlAttr($attr, 'get');
        //当前分页参数
        $tag['page'] = $page = (isset($tag['page'])) ? ( (substr($tag['page'], 0, 1) == '$') ? $tag['page'] : (int) $tag['page'] ) : 0;
        //当前分页参数
        $tag['page'] = $page = (isset($tag['page'])) ? ( (substr($tag['page'], 0, 1) == '$') ? $tag['page'] : (int) $tag['page'] ) : 0;
        //数据返回变量
        $tag['return'] = $return = empty($tag['return']) ? "data" : $tag['return'];
        //方法
        $tag['action'] = $action = trim($tag['action']);
        //分页模板
        $tag['pagetp'] = $pagetp = (substr($tag['pagetp'], 0, 1) == '$') ? $tag['pagetp'] : '';
        //分页函数，默认page
        $tag['pagefun'] = $pagefun = empty($tag['pagefun']) ? "page" : trim($tag['pagefun']);
        //缓存时间
        $tag['cache'] = $cache = (int) $tag['cache'];
        //每页显示总数
        $tag['num'] = $num = isset($tag['num']) && intval($tag['num']) > 0 ? intval($tag['num']) : 20;
        //SQL语句
        $tag['sql'] = $sql = str_replace(array("think_", "shuipfcms_"), C("DB_PREFIX"), strtolower($tag['sql']));
        //数据源
        $tag['dbsource'] = $dbsource = $tag['dbsource'];
        if (!$sql) {
            return false;
        }
        //删除，插入不执行！这样处理感觉有点鲁莽了，，，-__,-!
        if (strpos($tag['sql'], "delete") || strpos($tag['sql'], "insert")) {
            return false;
        }

        $parseStr = ' <?php ';

        //有启用分页
        if ($page) {
            //分析SQL语句
            if ($_sql = preg_replace('/select([^from].*)from/i', "SELECT COUNT(*) as count FROM ", $tag['sql'])) {
                //判断是否变量传递
                if (substr(trim($sql), 0, 1) == '$') {
                    $parseStr .= ' $sql = str_replace(array("think_", "shuipfcms_"), C("DB_PREFIX"),' . $sql . ');';
                    $parseStr .= ' $_count_sql = preg_replace("/select([^from].*)from/i", "SELECT COUNT(*) as count FROM ", $sql);';
                    $parseStr .= ' $_sql = $sql;';
                } else {
                    //统计SQL
                    $parseStr .= ' $_count_sql = "' . str_replace('"', '\"', $_sql) . '";';
                    $parseStr .= ' $_sql = "' . str_replace('"', '\"', $sql) . '";';
                }
                $parseStr .= ' $cache = ' . $cache . ';';
                $parseStr .= ' $cacheID = to_guid_string(array($_sql,' . $page . '));';
                //缓存处理
                $parseStr .= ' if($cache && $_return = S($cacheID)){ ';
                $parseStr .= ' $count = $_return["count"];';
                $parseStr .= ' }else{ ';
                $parseStr .= ' $get_db = M(); ';
                //如果定义了数据源 
                if ($dbsource) {
                    $dbSource = F('dbSource');
                    $dbConfig = $dbSource[$dbsource];
                    if ($dbConfig) {
                        $db = 'mysql://' . $dbConfig['username'] . ':' . $dbConfig['password'] . '@' . $dbConfig['host'] . ':' . $dbConfig['port'] . '/' . $dbConfig['dbname'];
                    }
                    $parseStr .= ' $get_db->db(1,"' . $db . '"); ';
                }
                //取得信息总数
                $parseStr .= ' $count = $get_db->query($_count_sql);';
                $parseStr .= ' $count = $count[0]["count"]; ';
                $parseStr .= ' } ';
                $parseStr .= ' $_page_ = ' . $pagefun . '($count ,' . $num . ',' . $page . ',6,C("VAR_PAGE"),"",true);';
                //设置分页模板，模板必须是变量传递
                if ($pagetp) {
                    $parseStr .= ' $_page_->SetPager(\'default\', ' . $pagetp . ');';
                }
                //显示分页导航
                $parseStr .= ' $pages = $_page_->show("default");';
                //总分页数
                $parseStr .= ' $GLOBALS["Total_Pages"] = $_page_->Total_Pages;';
                //分页总数
                $parseStr .= ' $pagetotal = $_page_->Total_Pages;';
                //总信息数
                $parseStr .= ' $totalsize = $_page_->Total_Size;';
                //缓存判断
                $parseStr .= ' if($cache && $_return){ ';
                $parseStr .= '      $' . $return . ' = $_return["data"]; ';
                $parseStr .= ' }else{ ';
                $parseStr .= '      $' . $return . ' = $get_db->query($_sql." LIMIT ".$_page_->firstRow.",".$_page_->listRows." ");';
                //缓存处理
                $parseStr .= '      if($cache){ S($cacheID ,array("count"=>$count,"data"=>$' . $return . '),$cache); }; ';
                $parseStr .= ' } ';
            } else {
                return false;
            }
        } else {
            //判断是否变量传递
            if (substr(trim($sql), 0, 1) == '$') {
                $parseStr .= ' $_sql = str_replace(array("think_", "shuipfcms_"), C("DB_PREFIX"),' . $sql . ');';
            } else {
                $parseStr .= ' $_sql = "' . str_replace('"', '\"', $sql) . '";';
            }
            $parseStr .= ' $cache = ' . $cache . ';';
            $parseStr .= ' $cacheID = to_guid_string($_sql);';
            $parseStr .= ' if(' . $cache . ' && $_return = S( $cacheID ) ){ ';
            $parseStr .= '      $' . $return . '=$_return;';
            $parseStr .= ' }else{ ';
            $parseStr .= ' $get_db = M();';
            $parseStr .= '      $' . $return . '=$get_db->query($_sql." LIMIT ' . $num . ' ");';
            $parseStr .= '      if(' . $cache . '){ S( $cacheID  ,$' . $return . ',$cache); }; ';
            $parseStr .= ' } ';
        }
        $parseStr .= '  ?>';
        $parseStr .= $this->tpl->parse($content);
        $_get_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 转换数据为HTML代码
     * @param array $data 数组
     */
    private static function arr_to_html($data) {
        if (is_array($data)) {
            $str = 'array(';
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $str .= "'$key'=>" . self::arr_to_html($val) . ",";
                } else {
                    if (strpos($val, '$') === 0) {
                        $str .= "'$key'=>$val,";
                    } else {
                        $str .= "'$key'=>'" . new_addslashes($val) . "',";
                    }
                }
            }
            return $str . ')';
        }
        return false;
    }
    
    /**
     * 检查是否变量
     * @param type $variable
     * @return type
     */
    private function variable($variable){
        return substr(trim($variable), 0, 1) == '$';
    }

}

?>