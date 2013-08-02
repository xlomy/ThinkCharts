#交流
* QQ群：49219815
* 网址：[http://www.abc3210.com](http://www.abc3210.com)

----
#ShuipFCMS简介 
* ShuipFCMS 基于[ThinlPHP](http://www.thinlphp.cn)框架开发，采用独立分组的方式开发的内容管理系统。；
* 支持模块安装/卸载，模型自定义，整合UCenter通行证等。

##根据安装程序安装好后，进入后台需要进行如下操作：
* 更新站点缓存。
* 进入 内容 -> 批量更新URL 更新地址。
* 进入 内容 -> 批量更新栏目页 进行生成栏目页。
* 进入 内容 -> 批量更新内容页 进行生成内容页。
* 进入 模块 -> 搜索配置 -> 重建索引 进行搜索数据的重建。

##主要模块介绍：
* Admin模块：后台管理模块。

* Models模块：模型管理模块。

* Search模块：搜索模块。

* Attachment模块：附件管理模块。

* Collection模块：采集模块。

* Comments模块：评论模块。

* Contents模块：内容模块。

* Cron模块：计划任务模块。

* Domains模块：域名绑定模块。

* Template模块：模板管理模块。

---
##模板标签简单介绍：
```html
标签：##<Form/>
作用：生成各种表单元素
用法示例：<Form function="date" parameter="name,$valeu"/>
参数说明：
	@function			表示所使用的方法名称，方法来源于Form.class.php这个类。
	@parameter		所需要传入的参数，支持变量！
	
标签：##<template/>
作用：引入其他模板
用法示例：<template file="Member/footer.php"/>
参数说明：
	@file					表示需要应用的模板路径。(这里需要说明的是，只能引入当前主题下的模板文件)

标签：##<content></content>
作用：内容模型相关标签，可调用栏目，列表等常用信息
用法示例：<content action="lists" catid="$catid"  order="id DESC" num="4" page="$page"> .. HTML ..</content>
参数说明：
	基本参数
		@action			调用方法（必填）
		@page			当前分页号，默认$page，当传入该参数表示启用分页，一个页面只允许有一个page，
								多个标签使用多个page会造成不可预知的问题。
		@num			每次返回数据量
		@catid			栏目id（必填），列表页，内容页可以使用 $catid 获取当前栏目。
	公用参数：
		@cache		数据缓存时间，单位秒
		@pagefun	分页函数，默认page()
		@pagetp		分页模板
		@return		返回值变量名称，默认data
	#当action为lists时，调用栏目列表标签
	#用法示例：<content action="lists" catid="$catid"  order="id DESC" num="4" page="$page"> .. HTML ..</content>
	独有参数：
		@order			排序，例如“id DESC”
		@where			sql语句的where部分 例如：thumb`!='' AND `status`=99（当有该参数时，thumb，catid参数失效）
		@thumb		是否仅必须缩略图，1为调用带缩略图的
		@moreinfo	是否调用副表数据 1为是
	#当action为hits时，调用排行榜
	#用法示例：<content action="hits" catid="$catid"  order="weekviews DESC" num="10"> .. HTML ..</content>
	独有参数：
		@order			排序，例如“weekviews DESC”
		@day				调用多少天内的排行
	#当action为relation时，调用相关文章
	#用法示例：<content action="relation" relation="$relation" catid="$catid"  order="id DESC" num="5" keywords="$keywords"> .. HTML ..</content>
	独有参数：
		@nid				排除id 一般是 $id，排除当前文章
		@keywords	内容页面取值：$keywords，也就是关键字
		@relation		内容页取值$relation，当有$relation时keywords参数失效
	#当action为category时，调用栏目列表
	#用法示例：<content action="category" catid="$catid"  order="listorder ASC" > .. HTML ..</content>
	独有参数：
		@order		排序，例如“id DESC”
	
标签：##<comment></comment>
作用：评论标签
用法示例：<comment action="get_comment" catid="$catid" id="$id"> .. HTML ..</comment>
参数说明：
	基本参数
		@action			调用方法（必填）
		@catid			栏目id（必填），列表页，内容页可以使用 $catid 获取当前栏目。
	公用参数：
		@cache			数据缓存时间，单位秒
		@return			返回值变量名称，默认data
	#当action为get_comment时，获取评论总数
	#用法示例：<comment action="get_comment" catid="$catid" id="$id"> .. HTML ..</comment>
	独有参数：
		@id					信息ID
	#当action为lists时，获取评论数据列表
	#用法示例：<comment action="lists" catid="$catid" id="$id"> .. HTML ..</comment>
	独有参数：
		@id					信息ID
		@hot				排序方式｛0：最新｝
		@date			时间格式 Y-m-d H:i:s A
		
标签：##<tags></tags>
作用：Tags标签
用法示例：<tags action="lists" tag="$tag" num="4" page="$page" order="updatetime DESC"> .. HTML ..</tags>
参数说明：
	基本参数
		@action			调用方法（必填）
		@page			当前分页号，默认$page，当传入该参数表示启用分页，一个页面只允许有一个page，
								多个标签使用多个page会造成不可预知的问题。
		@num			每次返回数据量
	公用参数：
		@cache			数据缓存时间，单位秒
		@return			返回值变量名称，默认data
		@pagefun		分页函数，默认page()
		@pagetp		分页模板
	#当action为lists时，获取tag标签列表
	#用法示例：<tags action="lists" tag="$tag" num="4" page="$page" order="updatetime DESC"> .. HTML ..</tags>
	独有参数：
		@tag				标签名，例如：厦门 支持多个，多个用空格或者英文逗号
		@tagid			标签id 多个使用英文逗号隔开
		@order			排序
		@num			每次返回数据量
	#当action为top时，获取tag标签列表
	#用法示例：<tags action="top"  num="4"  order="updatetime DESC"> .. HTML ..</tags>
	独有参数：
		@num			每次返回数据量
		@order			排序例如 hits DESC
		
标签：##<links></links>
作用：友情链接标签
用法示例：<links action="type_list" termsid="1" id="1"> .. HTML ..</links>
参数说明：
	公用参数：
		@cache			数据缓存时间，单位秒
		@return			返回值变量名称，默认data
	#当action为type_list时，获取tag标签列表
	#用法示例：<links action="type_list" termsid="1" id="1"> .. HTML ..</links>
	独有参数：
		@order			排序方式
		@termsid		分类ID
		@id					链接ID
		
标签：##<position></position>
作用：推荐位标签
用法示例：<position action="position" posid="1"> .. HTML ..</position>
参数说明：
	公用参数：
		@cache			数据缓存时间，单位秒
		@return			返回值变量名称，默认data
	#当action为position时，获取推荐位
	独有参数：
		@posid			推荐位ID(必填)
		@catid			调用栏目ID
		@thumb		是否仅必须缩略图
		@order			排序例如
		@num			每次返回数据量
		
标签：##<get></get>
作用：特殊标签，SQL查询标签
用法示例：<get sql="SELECT * FROM shuipfcms_article  WHERE status=99 ORDER BY inputtime DESC" page="$page" num="5"> .. HTML ..</get>
参数说明：
	@sql					SQL语句，强烈建议只用于select类型语句，其他SQL有严重安全威胁，
								同时不建议直接在SQL语句中使用外部变量，如:$_GET,$_POST等。
	@page				当前分页号，默认$page，当传入该参数表示启用分页，一个页面只允许有一个page，
								多个标签使用多个page会造成不可预知的问题。
	@num				每次返回数据量
	@cache				数据缓存时间，单位秒
	@return				返回值变量名称，默认data
	@pagefun			分页函数，默认page()
	@pagetp			分页模板
	
	
标签：##<navigate/>
作用：导航标签
用法示例：<navigate catid="$catid" space=" &gt; " />
参数说明：
	@catid				栏目ID，可以使用$catid
	@space				分隔符 &gt;
```

---

## 界面预览：
 ![mahua](http://file.abc3210.com/d/file/contents/2013/01/50f8dfd9cf91d.jpg)