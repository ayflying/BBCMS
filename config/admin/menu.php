<?php
return [
	
		"系统首页" => [
            "首页" => "Index/index",
			"系统设置" => "System/index",
			"网站模板" => 'System/theme',
			"更新缓存" => 'System/cache',
			"在线升级" => "Update/index?msg=".lang('正在连接到服务器'),
			"数据库" => "sql/index",
		],
		"内容管理" => [
			"菜单管理" => 'Menu/index',
			"内容列表" => 'Portal/article_list',
			"回收站" => 'Portal/recycle',
			'功能模型' => 'Mod/mod',
		],
		"用户管理" => [
			"用户列表" => "Member/user",
			"用户组" => 'Member/group',
			"权限" => 'Member/action',
		],
		"网站运营" => [
			"广告" => 'ad/ad_list',
			"友情链接" => 'Flink/flink_list',
			"插件" => 'Addon/addon_list',
		],
		"微信" => [
			"用户回复" => "Wechat/response",
			"自定义菜单" => "Wechat/menu",
		],
		
];