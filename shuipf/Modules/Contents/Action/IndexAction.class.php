<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class IndexAction extends BaseAction {

    private $url;

    function _initialize() {
        parent::_initialize();
        import('Url');
        $this->url = new Url();
    }

    //首页
    public function index() {
        $page = isset($_GET[C("VAR_PAGE")]) ? $_GET[C("VAR_PAGE")] : 1;
        $page = max($page, 1);
        //模板处理
        $tp = explode(".", CONFIG_INDEXTP);
        $template = parseTemplateFile("Index:" . $tp[0]);
        if ($template == false && $tp[0] != "index") {
            //模板不存在，重新使用默认模板
            $template = "index";
            $template = parseTemplateFile("Index:" . $template);
            if ($template == false) {
                $this->error("首页模板不存在！");
            }
        } else if ($template == false) {
            $this->error("首页模板不存在！");
        }

        $SEO = seo("", "", AppframeAction::$Cache['Config']['siteinfo'], AppframeAction::$Cache['Config']['sitekeywords']);

        //生成路径
        $urls = $this->url->index($page);
        $GLOBALS['URLRULE'] = $urls['page'];

        //seo分配到模板
        $this->assign("SEO", $SEO);
        //把分页分配到模板
        $this->assign(C("VAR_PAGE"), $page);
        $this->display($template);
    }

    //栏目列表 
    public function lists() {
        //栏目ID
        $catid = I('get.catid',0,'intval');
        //分页
        $page = isset($_GET[C("VAR_PAGE")]) ? $_GET[C("VAR_PAGE")] : 1;
        if (!$catid) {
            $this->error("您没有访问该信息的权限！");
        }
        $this->categorys = F("Category");
        //获取栏目数据
        $category = $this->categorys[$catid];
        if (empty($category)) {
            $this->error("该栏目不存在！");
        }
        //栏目扩展配置信息反序列化
        $setting = unserialize($category['setting']);
        //生成静态分页数
        $repagenum = (int) $setting['repagenum'];
        if ($repagenum && !$GLOBALS['dynamicRules']) {
            //设置动态访问规则给page分页使用
            $GLOBALS['Rule_Static_Size'] = $repagenum;
            $GLOBALS['dynamicRules'] = CONFIG_SITEURL_MODEL."index.php?a=lists&catid={$catid}&page=*";
        }
        //父目录
        $parentdir = $category['parentdir'];
        //目录
        $catdir = $category['catdir'];
        //生成路径
        $category_url = $this->url->category_url($catid, $page);
        //取得URL规则
        $urls = $category_url['page'];

        //生成类型为0的栏目
        if ($type == 0) {
            //栏目首页模板
            $template = $setting['category_template'] ? $setting['category_template'] : 'category';
            //栏目列表页模板
            $template_list = $setting['list_template'] ? $setting['list_template'] : 'list';
            //判断使用模板类型，如果有子栏目使用频道页模板，终极栏目使用的是列表模板
            $template = $category['child'] ? "Category:" . $template : "List:" . $template_list;
            //去除后缀开始
            $tpar = explode(".", $template, 2);
            //去除完后缀的模板
            $template = $tpar[0];
            unset($tpar);
            //模板检测
            $template = parseTemplateFile($template);
            $GLOBALS['URLRULE'] = $urls;
        }
        //把分页分配到模板
        $this->assign(C("VAR_PAGE"), $page);
        //分配变量到模板 
        $this->assign($category);
        //seo分配到模板
        $seo = seo($catid, $setting['meta_title'], $category['description'], $setting['meta_keywords']);
        $this->assign("SEO", $seo);
        $this->display($template);
    }

    /**
     * 内容页 
     */
    public function shows() {
        $catid = $this->_get("catid");
        $id = $this->_get("id");
        $page = intval($_GET[C("VAR_PAGE")]);
        $page = max($page, 1);
        if (!$id || !$catid) {
            $this->error("缺少参数！");
        }
        $this->categorys = F("Category");
        //获取当前栏目数据
        $category = $this->categorys[$catid];
        //反序列化栏目配置
        $category['setting'] = unserialize($category['setting']);
        //模型ID
        $this->modelid = $category['modelid'];
        //获取主表名
        $this->table_name = $this->Model[$this->modelid]['tablename'];

        $this->db = new ContentModel($this->table_name);
        $data = $this->db->relation(true)->where(array("id" => $id, 'status' => 99))->find();
        if (empty($data)) {
            $this->error("该信息不存在！");
        }
        $data = array_merge($data, $data[$this->table_name . "_data"]);
        unset($data[$this->table_name . "_data"]);
        //分页方式
        if (isset($data['paginationtype'])) {
            //分页方式 
            $paginationtype = $data['paginationtype'];
            //自动分页字符数
            $maxcharperpage = (int) $data['maxcharperpage'];
        } else {
            //默认不分页
            $paginationtype = 0;
        }

        //载入字段数据处理类
        if (!file_exists(RUNTIME_PATH . 'content_output.class.php')) {
            $this->error("请更新缓存后再操作！");
        }
        require_cache(RUNTIME_PATH . 'content_output.class.php');
        $content_output = new content_output($this->modelid, $catid, $this->categorys);
        //获取字段类型处理以后的数据
        $output_data = $content_output->get($data);
        $output_data['id'] = $id;
        $output_data['title'] = strip_tags($output_data['title']);
        //SEO
        $seo_keywords = '';
        if (!empty($output_data['keywords'])) {
            $seo_keywords = implode(',', $output_data['keywords']);
        }
        $seo = seo($catid, $output_data['title'], $output_data['description'], $seo_keywords);

        //内容页模板
        $template = $output_data['template'] ? $output_data['template'] : $category['setting']['show_template'];
        //去除模板文件后缀
        $newstempid = explode(".", $template);
        $template = $newstempid[0];
        unset($newstempid);
        //检测模板是否存在、不存在使用默认！
        $tempstatus = parseTemplateFile("Show:" . $template);
        if ($tempstatus == false && $template != "show") {
            //模板不存在，重新使用默认模板
            $template = "show";
            $tempstatus = parseTemplateFile("Show:" . $template);
            if ($tempstatus == false) {
                return false;
            }
        } else if ($tempstatus == false) {
            return false;
        }

        //分页处理
        $pages = $titles = '';
        //分页方式 0不分页 1自动分页 2手动分页
        if ($data['paginationtype'] == 1) {
            //自动分页
            if ($maxcharperpage < 10) {
                $maxcharperpage = 500;
            }
            //按字数分割成几页处理开始
            import('Contentpage', APP_PATH . C("APP_GROUP_PATH") . '/Contents/ORG');
            $contentpage = new Contentpage();
            $contentfy = $contentpage->get_data($output_data['content'], $maxcharperpage);
            //自动分页有时会造成返回空，如果返回空，就不分页了
            if (!empty($contentfy)) {
                $output_data['content'] = $contentfy;
            }
        }

        //分配解析后的文章数据到模板 
        $this->assign($output_data);
        //seo分配到模板
        $this->assign("SEO", $seo);
        //栏目ID
        $this->assign("catid", $catid);

        //分页生成处理
        //分页方式 0不分页 1自动分页 2手动分页
        if ($data['paginationtype'] > 0) {
            $urlrules = $this->url->show($data, $page);
            //手动分页
            $CONTENT_POS = strpos($output_data['content'], '[page]');
            if ($CONTENT_POS !== false) {
                $contents = array_filter(explode('[page]', $output_data['content']));
                $pagenumber = count($contents);
                $pages = page($pagenumber, 1, $page, 6, C("VAR_PAGE"), $urlrules['page'], true)->show("default");
                //判断[page]出现的位置是否在第一位 
                if ($CONTENT_POS < 7) {
                    $content = $contents[$page];
                } else {
                    $content = $contents[$page - 1];
                }
                //分页
                $this->assign("pages", $pages);
                $this->assign("content", $content);
            }
        } else {
            $this->assign("content", $output_data['content']);
        }
        $this->display($tempstatus);
    }

}

?>
