/**
 * @example
 * LS.item("key","value");//设置key字段为value
 * LS.item("key");//设置key字段的值
 */
var LS = {
    /**
     * 获取/设置存储字段
     * @param {String} name 字段名称
     * @param {String} value 值
     * @return {String}
     */
    item : function(name,value){
        var val = null;
        if(LS.isSupportLocalStorage()){
            if(value){
                localStorage.setItem(name,value);
                val = value;
            }else{
                val = localStorage.getItem(name);
            }
        }else{
            //不支持HTML5
            return;
        }
        return val;
    },
    /**
     * 移除指定name的存储
     * @param {String} name 字段名称
     * @return {Boolean}
     */
    removeItem : function(name){
        if(LS.isSupportLocalStorage()){
            localStorage.removeItem(name);
        }else{
            //不支持HTML5
            return false;
        }
        return true;
    },
    /**
     * 判断浏览器是否直接html5本地存储
     */
    isSupportLocalStorage : function(){
        var ls = LS,is = ls.IS_HAS_LOCAL_STORAGE;
        if(is == null){
            if(window.localStorage){
                is = ls.IS_HAS_LOCAL_STORAGE = true;
            }
        }
        return is;
    },
    IS_HAS_LOCAL_STORAGE : null
};

/**
 * 微型模板引擎 tmpl 0.2
 *
 * 0.2 更新:
 * 1. 修复转义字符与id判断的BUG
 * 2. 放弃低效的 with 语句从而最高提升3.5倍的执行效率
 * 3. 使用随机内部变量防止与模板变量产生冲突
 *
 * @author	John Resig, Tang Bin
 * @see		http://ejohn.org/blog/javascript-micro-templating/
 * @name	tmpl
 * @param	{String}	模板内容或者装有模板内容的元素ID
 * @param	{Object}	附加的数据
 * @return	{String}	解析好的模板
 */
var tmpl = (function (cache, $) {
    return function (str, data) {
        var fn = !/\s/.test(str)
        ? cache[str] = cache[str]
        || tmpl(document.getElementById(str).innerHTML)

        : function (data) {
            var i, variable = [$], value = [[]];
            for (i in data) {
                variable.push(i);
                value.push(data[i]);
            };
            return (new Function(variable, fn.$))
            .apply(data, value).join("");
        };

        fn.$ = fn.$ || $ + ".push('" 
        + str.replace(/\\/g, "\\\\")
        .replace(/[\r\t\n]/g, " ")
        .split("<%").join("\t")
        .replace(/((^|%>)[^\t]*)'/g, "$1\r")
        .replace(/\t=(.*?)%>/g, "',$1,'")
        .split("\t").join("');")
        .split("%>").join($ + ".push('")
        .split("\r").join("\\'")
        + "');return " + $;

        return data ? fn(data) : fn;
    }
})({}, '$' + (+ new Date));

/**
 * 显示用户的浏览记录
 * @class
 * @param {Object} config 配置
 */
function Histories(config){
    var self = this;
    self.data = [];
    //覆盖配置
    self.config = $.extend(Histories.DEFAULT_CONFIG,config);
    //初始化
    self._init();
}
//默认配置
Histories.DEFAULT_CONFIG = {
    //显示条数
    count : 10,
    //达到该数目时清理多余数据
    maxClearNumber : 100,
    //标题长度
    maxTitleLen : 24,
    //本地存储类
    ls : LS,
    //存储字段
    storeName : 'data-histories',
    //列表的li模板   
    tpl : '<li data-id="<%=id%>"><a href="<%=url%>" target="_blank"><%=title%></a></li>'
};
Histories.prototype = {
    /**
     * 初始化
     */
    _init : function(){
        var self = this,ls = self.config.ls;
        //不支持html5的离线存储，直接退出
        if(!ls.isSupportLocalStorage()) return false;
    },
    /**
     * 将数据加入dom
     * @param {String} container 容器
     */
    appendTo : function(container){
        var self = this,$container = $(container),html = '',data = self.getData(),config = self.config,maxTitleLen = config.maxTitleLen,tpl = config.tpl,count = config.count;
        if($container.length == 0 || data.length == 0) return false;
        var render= tmpl(tpl);;
        //遍历数据（数组）
        $.each(data,function(i){
            //截取标题
            this.title = this.title.substr(0,maxTitleLen);
            //转换模板
            html += render(this);
            //超过最大渲染数，直接退出循环
            if(i >= count) return false;
        });
        return $(html).appendTo($container);
    },
    /**
     * 获取本地数据
     * @return {Array} 
     */
    getData : function(){
        var self = this,config = self.config,ls = config.ls,sData = ls.item(config.storeName);
        if(sData) self.data = JSON.parse(sData);
        return self.data;
    },
    /**
     * 将值保存到本地数据
     * @param {Object} singleData 文章数据
     * @return {Boolean}
     */
    save : function(singleData){
        var self = this,config = self.config,ls = config.ls,sData;
        //保存的数据类型必须为对象
        if(typeof singleData == 'object'){
            //如果已经存在该条数据，直接退出
            if(self.isExist(singleData.id)) return false;
            //删除多余数据
            self._removeExceedPost();
            //向数据缓存追加一条数据
            self.data.unshift(singleData);
            //转化成json字符串
            sData = JSON.stringify(self.data);
            //调用本地存储类，保存数据
            ls.item(config.storeName,sData);
        }
        return true;
    },
    /**
     * 已经存在指定id的文章
     * @param id
     * @return {Boolean}
     */
    isExist : function(id){
        var self = this,data = self.data,exist = false,postId;
        if(data.length > 0){
            $.each(data,function(){
                postId = this.id;
                if(id == postId) {
                    exist = true;
                    return false;
                }
            });
        }
        return exist;
    },
    /**
     * 清理本地数据
     */
    clear : function(){
        var self = this,config = self.config,ls = config.ls;
        ls.removeItem(config.storeName);
        self.data = [];
    },
    /**
     * 删除超过count的数据
     */
    _removeExceedPost : function(){
        var self = this,config = self.config,count = config.maxClearNumber,data = self.data;
        if(data.length < count) return false;
        self.data.splice(count-1,data.length - count + 1);
    }
};

/**
 * 评论相关JS
 */
function Comment(id ,catid ,config){
    //域名
    if(config.domain == 'undefine' || config.domain == ''){
        this.domain = "/";
    }else{
        this.domain = config.domain;
    }
    //文章ID
    this.id = id;
    //栏目ID
    this.catid = catid;
    //评论编号
    this.comment_id = "c-"+this.catid+"-"+this.id;
    //请求地址
    if(config.httpurl != 'undefine'){
        this.httpurl = this.domain+"api.php?m=Getjsonp&a=comment";
    }else{
        this.httpurl = config.httpurl;
    }
    //评论表单
    this.formid = config.formid;
    //评论列表，外围容器
    if(config.container != 'undefine'){
        this.container = config.container;
    }else{
        this.container = ".comment-list";
    }
    
    this.Commentlock = false;
    //评论模板
    this.template = config.template;
    this.tmp = "";
    this.page = 1;//当前分页数
    this.TotalPages;//总分页数
    this.show(1);
}

Comment.prototype = {
    /**
     * 显示评论
     */
    show:function(page){
        var parent = this;
        if(page>parent.TotalPages){
            return;
        }
        this.page = page;
        $("#gengdload").show();
        $.ajax({
            type: "GET",
            async:false,
            url: this.httpurl,
            data: "id="+this.id+"&catid="+this.catid+"&page="+page,
            dataType:"json",
            success: function(data){
                if(data.status){
                    parent.tp(data);
                }else{
                    art.dialog({
                        id:'error',
                        icon: 'error',
                        content: data.info
                    });
                }
            }
        });
    },
    
    //评论提交
    sub:function(){
        if(this.Commentlock){
            art.dialog({
                id:'error',
                icon: 'error',
                content: "请不用重复提交！"
            });
            return;
        }else{
            this.Commentlock = true;
            var parent = this;
            this.postdata = $(this.formid).serialize();
            $(this.formid+" [type='submit']").hide();
            $(this.formid+" .load").show();
            $.ajax({
                type: "POST",
                async:false,
                url: this.domain+"index.php?a=add&m=Index&g=Comments",
                data: this.postdata,
                timeout:5000,
                dataType:"json",
                error:function(XMLHttpRequest, textStatus, errorThrown){
                    parent.Commentlock = false;
                    setTimeout(function(){
                        $(parent.formid+" [type='submit']").show();
                        $(parent.formid+" .load").hide();
                    },50);
                    art.dialog({
                        id:'error',
                        icon: 'error',
                        content: "很遗憾的告诉您，出现未知错误，操作失败！"
                    });
                },
                success: function(data){
                    parent.Commentlock = false;
                    setTimeout(function(){
                        $(parent.formid+" [type='submit']").show();
                        $(parent.formid+" .load").hide();
                    },50);
                    if(data.status == '3'){
                        art.dialog({
                            id:'succeed',
                            icon: 'succeed',
                            content: data.info
                        });
                        return;
                    }
                    if(data.status){
                        $("#ComContent").val("");
                        movecfm(null,0,1,null);
                        parent.show(1);
                    }else{
                        art.dialog({
                            id:'error',
                            icon: 'error',
                            content: data.info
                        });
                    }
                }
            });
        }
    },
    
    //模板处理
    tp:function(obj){
        var parent = this;
        // 生成模板缓存
        this.render = tmpl(parent.template);
        if(this.page==1){
            $(parent.container).html("");
        }
        if(obj.data.list == "" || obj.data.list == null){
            $(parent.container).html('<li style=" height:105px;line-height:105px;"><img style="vertical-align:middle;" src="'+parent.domain+'statics/blog/images/nocomment.png"></li>');
            return;
        }else{
            $.each(obj.data.list, function(i,v){
                if(parent.page == 1){
                    $(parent.container).append(parent.render(v));
                }else{
                    $("#commentgenduo").before(parent.render(v));
                }
            });
            parent.TotalPages = obj.data.Total_Pages;
            //更多
            if(obj.data.Total_Pages > 1 && parent.page == 1){
                $(parent.container).append('<li id="commentgenduo" style=" height:32px;line-height:32px; text-align:center;" ><a href="javascript:void(0)">还有更多评论:-O</a></li><li id="gengdload" style=" height:32px;line-height:32px; display:none;"><img style="vertical-align:middle;" src="'+parent.domain+'statics/blog/images/load.gif">正在努力的加载数据中... ...</li>');
            }else{
                setTimeout(function(){
                    $(parent.container+" #gengdload").hide();
                },200);
            }
            //如果当前分页等于最大分页数去除加载更多
            if(parent.TotalPages==parent.page){
                $("#commentgenduo").hide();
            }
        }
        
    }
};

//滚动到页面指定位置
var slide = function(id){
    if($(id).length==0){
        return false;
    }
    $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    $body.animate({
        scrollTop: $(id).offset().top
    }, 800);
}