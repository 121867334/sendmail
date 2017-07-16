<?php
if (!defined('APP_PATH')) {
	exit("APP_PATH undefined");	
}

// 定义框架路径
define('CORE_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);


// 加载核心类库
require_once 'class/Fn.php';

// 加载核心函数库
require_once 'common/funs.php';


// 初始化运行时信息
Fn::$box['_runStartTime'] = microtime(true);

// 自动加载对象
spl_autoload_register('__autoload_class');

// 注册shutdow事件
register_shutdown_function('__shutdown');

// 载入系统配置文件
require CORE_PATH . 'common/config.php';

// 错误和异常处理
if (Fn::$config['debug_mode']) {
	//Fn::$config['error_types'] = E_ALL & ~E_NOTICE;
} else {
	Fn::$config['error_types'] = 0;
}
error_reporting(Fn::$config['error_types']);

if (Fn::$config['error_exception']) {
	set_error_handler('__error_handler', Fn::$config['error_types']);
}
set_exception_handler('catch_error');

// 设置系统时间
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set(Fn::$config['default_timezone']);
}

// 表单自动保存
if (Fn::$config['form_auto_cache']) {
	header('Cache-Control: private,must-revalidate');
	session_cache_limiter('private,must-revalidate');
}

// 自动启用Session
if (Fn::$config['session_auto_start']) {
	Session::start();	
}

// 默认编码
header("Content-type: text/html; charset=" . Fn::$config['charset']);

// 开启输出缓冲
ob_start();

