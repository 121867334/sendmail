<?php
/*
	2017-6-11
*/
Fn::$config['db_config_main'] = array(
	'host' => 'localhost',
	'port' => '3306',
	'user' => 'root',
	'pass' => 'root',
	'dbname' => 'db_travel',
);

// ----------------------------------------
Fn::$config['db_table_prefix'] = 'tra_';
// ----------------------------------------
Fn::$config['debug_mode'] = true;
Fn::$config['template_debug'] = true;
Fn::$config['session_auto_start'] = false;
Fn::$config['show_msg_tpl'] = 'show_msg.html';
// ----------------------------------------
if (is_file(Fn::$config['data_path'] . 'web-config.php')) {
	require_once Fn::$config['data_path'] . 'web-config.php';
}