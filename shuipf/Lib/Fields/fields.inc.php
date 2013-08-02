<?php

/* *
 *  可用字段类型列表
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
$fields = array('text' => '单行文本',
    'textarea' => '多行文本',
    'editor' => '编辑器',
    'catid' => '栏目',
    'title' => '标题',
    'box' => '选项',
    'image' => '图片',
    'images' => '多图片',
    'number' => '数字',
    'datetime' => '日期和时间',
    'keyword' => '关键词',
    'tags' => 'Tags标签',
    'author' => '作者',
    'copyfrom' => '来源',
    'islink' => '转向链接',
    'template' => '模板',
    'pages' => '分页选择',
    'typeid' => '类别',
    'posid' => '推荐位',
    'linkfield' => '关联字段',
    'downfiles' => '多文件上传',
    'map' => '地图字段',
    'omnipotent' => '万能字段',
);
//不允许删除的字段，这些字段讲不会在字段添加处显示
$not_allow_fields = array('catid','typeid','title','keyword','template','username','tags');
//允许添加但必须唯一的字段
$unique_fields = array('pages','readpoint','author','copyfrom','islink','posid');
//禁止被禁用的字段列表
$forbid_fields = array('catid','title','updatetime','inputtime','url','listorder','status','template','username','allow_comment','tags');
//禁止被删除的字段列表
$forbid_delete = array('catid','typeid','title','thumb','keywords','updatetime','inputtime','posid','url','listorder','status','template','username','allow_comment','tags');
//可以追加 JS和CSS 的字段
$att_css_js = array('text','textarea','box','number','keyword','typeid');
?>