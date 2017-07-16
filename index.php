<?php
/**
 * @version        v1.0
 * @package        FnCMS.Site
 * @copyright      Copyright (c) 2012, FnCMS.
 */
 
// 应用程序路径
define('WEB_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);
define('APP_PATH', WEB_PATH);
// 加载框加入口
require_once APP_PATH.'core/loader.php';
// 载入项目配置
if (is_file(Fn::$config['data_path'] . 'config.php')) {
	require Fn::$config['data_path'] . 'config.php';
}
// 运行程序
Fn::run();