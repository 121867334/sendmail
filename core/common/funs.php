<?php
/**
 * 函数库
 */

/**
 * 自动加载类库
 * @param string $className 类名 
 * @return void 
 */
function __autoload_class($className) {
	if (class_exists($className, false)) {
		return true;	
	}
	$tmpArr = explode('_', $className);
	$suffix = null;
	if (count($tmpArr) > 1) {
		$suffix = end($tmpArr);
	}
	if ('Controller' == $suffix) { 
		array_pop($tmpArr);
		$classFile = Fn::$config['controller_path'] 
					. implode('/', $tmpArr)
					. '.php';
	} else if ('Model' == $suffix) {
		array_pop($tmpArr);
		$classFile = Fn::$config['model_path'] 
					. implode('/', $tmpArr)
					. '.php';
	} else {
		$classFile = CORE_PATH . 'class/' 
				. strtr($className, array('_' => '/')) 
				. '.php';	
	}
	import_file($classFile, false);
	return class_exists($className, false);
}

/**
 * shutdown事件回调函数
 * @return void
 */
function __shutdown() {
}


/**
 * 默认的错误处理
 * @param integer $errno 错误类型
 * @param string $errmsg 错误消息
 * @param string $errfile 错误文件
 * @param string $errline 错误行 
 * @return void 
 */
function __error_handler($errno, $errmsg, $errfile, $errline) {
	if (!(error_reporting() & $errno)) {
		return;	
	}
	if (isset(Fn::$output)) {
		Fn::$output->setState(-1);	
		Fn::$output->set('errorMsg', $errmsg);
	}
	throw new Error($errmsg, Error::PHP_ERROR) ;
}

/**
 * 处理错误信息
 * 
 * @param object $e 异常对象
 * @return void 
 */
function catch_error($e) {
	$error = $e->getError();
	if (Error::USER_MSG == $error['code']) {
		return;	
	}
	
	require CORE_PATH . '/misc/show_error.php';
	/*
	if (Fn::$config['debug_mode']) {	
		require CORE_PATH . '/misc/show_error.php';
	} else {
		show_msg("{$error['type']}: {$error['message']} [File:{$error['file']} Line:{$error['line']}]");
	}
	*/
	
	// 纪录数据库错误日志
	if (Error::DB_ERROR == $error['code'] && Fn::$config['error_db_log']) {
		$logCon = "{$error['type']}: {$error['message']}\n" . implode("\n", $error['trace']);
		Logs::errorDbLog($logCon);
	}
	
	// 纪录程序错误日志
	if ((Error::CODE_ERROR == $error['code'] || Error::PHP_ERROR == $error['code']) && Fn::$config['error_code_log']) {
		$logCon = "{$error['type']}: {$error['message']}\n" . implode("\n", $error['trace']);
		Logs::errorCodeLog($logCon);		
	}
} 


/**
 * 获取或者设定配置参数
 * @param mixed 参数名或者参数值数组
 * @param mixed 参数值
 * @return void
 */
function set_config($name, $value = null) {
	if (is_array($name)) {
		Fn::$config = array_merge(Fn::$config, $name);	
	} else {
		if (is_null($value)) {
			return Fn::$config[$name];
		} else {
			Fn::$config[$name] = $value;	
		}
	}	
}

/**
 * 获取或者设定配置参数
 * @param mixed $file 要保存的文件路径
 * @param mixed $data 参数数组
 * @return void
 */
function write_config($file, $data) {
	$arr = array();
	foreach ($data as $key => $value) {
		$arr[] = "Fn::\$config['$key'] = " . var_export($value, true) . ';';
	}	
	$content = "<?php\n" . implode("\n", $arr);
	$rs = Ext_File::write($file, $content);
}


/**
 * 调试一个变量
 * 
 * @param mixed $vars 变量
 * @param mixed .... 更多的变量
 * @param string $title 标题
 * @return void
 */
function dump($vars) {
	$varsArr = func_get_args();
	if (count($varsArr) > 1) {
		$vars = $varsArr;	
	}
	$content = (print_r($vars, true));
	$content = "<fieldset><pre>"
			 . $content
			 . "</pre></fieldset>\n";	
    echo $content;	
}

/**
 * 生成请求地址
 * 
 * @param mixed $args 
 * @return mixed 
 */
function url($module = '', $action = '', $args = array(), $mode = null, $absPath = true) {
	if (is_null($mode)) {
		$mode = Fn::$config['url_mode'];
	}
	switch ($mode)
	{
		case 0:
			$file_name = 'index.php';
			if(isset($args['run']))
			{
				$file_name = $args['run'].'.php';
				unset($args['run']);
			}
			
			$tmpArr = array();
			if ($module) {
				$tmpArr[Fn::$config['controller_var_name']] = $module;
			}
			if ($action) {
				$tmpArr[Fn::$config['action_var_name']] = $action;
			}
			$tmpArr = array_merge($tmpArr, $args);
			if ($tmpArr) {
				$url = '?' . http_build_query($tmpArr);
			} else {
				$url = '';
			}
			return  Fn::$config['web_url'] . $file_name . $url;
			break;
		case 1:
			$file_name = 'index.php';
			if(isset($args['run']))
			{
				$file_name = $args['run'].'.php';
				unset($args['run']);
			}
			
			if (!$module) {
				$module = Fn::$config['default_controller'];
			}
			if (!$action) {
				$action = Fn::$config['default_action'];
			}
			$tmpArr = array($module, $action);
			foreach ($args as $key => $value) {
				$tmpArr[] = $key;
				$tmpArr[] = urlencode($value);
			}
			$url = implode(Fn::$config['url_delimiter'], $tmpArr) . Fn::$config['url_suffix'];
			return Fn::$config['web_url'] . $file_name . '?' . $url;
			break;
		case 2:
			$module = str_replace(Fn::$config['default_controller'], '', $module);
			$action = str_replace(Fn::$config['default_action'], '', $action);
			$url = Fn::$config['html_path'];
			if(isset($args['dir']))
			{
				$url .=  urldecode($args['dir']);
				unset($args['dir']);
			}else {			
				$url .= $module ? strtolower($module) : '';
				$url .= $action ? '/'.$action : '';
			}
			
			if(isset($args['id']))
			{
				$file_name = $args['id'];
				unset($args['id']);
			}else{
				$file_name = 'index';
			}
			if(isset($args['file']))
			{
				$file_name = $args['file'];
				unset($args['file']);
			}
			if(isset($args['p']))
			{
				$file_name .= $args['p']>1 ? Fn::$config['url_delimiter'].$args['p'] : '';
				unset($args['p']);
			}
			$tmpArr = array();
			foreach ($args as $key => $value) {
				$tmpArr[] = $key;
				$tmpArr[] = urlencode($value);
			}
			if(!empty($tmpArr))	{
				$url .='/'. implode('/', $tmpArr) ;
			}
			$url .= '/' . $file_name . Fn::$config['url_suffix'];
			$url = trim(str_replace('//','/',$url),'/');
			return $absPath ? Fn::$config['web_url'].$url : $url;
			break;		
	}
}



/**
 * 检查当前请求是否为数据提交
 * @param mixed 
 * @return void
 */
function check_submit($name = 'submit') {
	return !empty($_POST[$name]);		
}

/**
 * 判断 HTTP 请求是否是通过 XMLHttp 发起的
 *
 * @return boolean
 */
function is_ajax() {
	if (!empty($_REQUEST['is_ajax'])) {
		return true;
	}
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    	return true;
    } 
	return false;
}


/**
 * 加载模型实例
 * @param string $modelName 模型名 
 * @param bool $single 是否单例模式
 * @return User_Model
 */
function load_model($modelName, $single = true) {
	$className = $modelName . '_Model';
	if (!$single) {
		return new $className();	
	}
	if (!isset(Fn::$box['ModelInstance'][$className])) { 
		Fn::$box['ModelInstance'][$className] = new $className();
	}
	return Fn::$box['ModelInstance'][$className];		
}


/**
 * 显示程序错误
 * @param string $errmsg 错误信息
 * @param integer $errcode 错误类型代码 
 * @return void
 */
function show_error($errorMsg) {
	if (Fn::$output) {
		Fn::$output->setState(-1);	
		Fn::$output->set('errorMsg', $errorMsg);
	}
	throw new Error($errorMsg, Error::CODE_ERROR);
}


/**
 * 抛出用户提示信息
 * @param string $reportMsg 用户消息 
 * @param string $url 要调转到的地址
 * @return void 
 */
function show_msg($errorMsg, $url = null, $refresh = 1, $error = 0, $backUrl = "javascript:history.go(-1)") {
	if ($url && 0 == $refresh) {
		ob_end_clean();
		header("Location: $url");
	}
	if (!$url) {
		$url = $backUrl;
	}
	
    if (isset(Fn::$output)) {
		Fn::$output->setState(-2);	
		Fn::$output->set('errorMsg', $errorMsg);
		Fn::$output->set('url', $url);
		Fn::$output->set('refresh', $refresh);
		Fn::$output->set('backUrl', $backUrl);
		Fn::$output->set('error', $error);
	}
	
	$inajax = Fn::$input->get('inajax');
	$inframe = Fn::$input->get('inframe');
	
	if (Fn::$config['show_msg_tpl']) {
		//require Fn::$config['show_msg_tpl'];
		Fn::$output->display(Fn::$config['show_msg_tpl']);
	} else {
		require CORE_PATH . '/misc/show_msg.php';
	}
	throw new Error($errorMsg, Error::USER_MSG);
	exit;
}

/**
 * 执行JS方法
 * @param mixed 
 * @return void
 */
function js_run($js) {
	echo "<script>$js</script>";
	ob_flush();
	flush();	
}


/**
 * 加载文件
 * @param string $fileName 文件名 
 * @param string $blackout 文件不存在时退出
 * @return bool 
 */
function import_file($fileName, $blackout = false) {
	if (!isset(Fn::$box['importFiles'][$fileName])) {	
		if (is_file($fileName)) {
			require $fileName;	
		} else {
			if ($blackout) {
				exit("$fileName: File not exists");	
			}
			return false;
		}
		Fn::$box['importFiles'][$fileName] = true;
	}
	return true;	
}
	
/**
 * 获取缓存对象 
 * 
 * @return object 缓存对象
 */
function load_cache() {
	if (!isset(Fn::$box['CacheInstance'])) {
		$obj = new Cache();
		Fn::$box['CacheInstance'] = $obj;
	}
	return Fn::$box['CacheInstance'];
}

	
/**
 * 初使化连接
 * 
 * @param string $tag 数据库连接标识 
 * @return Db 数据库连接对象
 */
function load_db($tag = 'main') {
	if (!isset(Fn::$box['DbInstance'][$tag])) {
		$cfgkey = 'db_config_' . $tag;
		if (isset(Fn::$config[$cfgkey])) {
			$dbCfg = Fn::$config[$cfgkey];	
		} else {
			exit("$tag: The dbtag does not exist");	
		}
		if ('Db_Mysql' == Fn::$config['db_driver']) {
			$driverName = 'Db_Mysql';
		} else if ('Db_Mysqli' == Fn::$config['db_driver']) {
			$driverName = 'Db_Mysqli';	
		} else {
			exit(Fn::$config['db_driver'] . ": The DB driver does not exist");
		}
		$db	= new $driverName( 
				$dbCfg['host'],
				$dbCfg['port'], 
				$dbCfg['user'], 
				$dbCfg['pass'], 
				$dbCfg['dbname']	
		);
		Fn::$box['DbInstance'][$tag] = $db;	
	}
	return Fn::$box['DbInstance'][$tag];
}
	
/**
 * 获取程序运行时信息
 * @param mixed 
 * @return void
 */
function get_runtime($more = true) {
	Fn::$box['_runEndTime'] = microtime(true);
	Fn::$box['_runTime'] = round(Fn::$box['_runEndTime'] - Fn::$box['_runStartTime'], 4);
	if ($more) {
		$data = array(
			'startTime' => Fn::$box['_runStartTime'],
			'endTime' => Fn::$box['_runEndTime'],
			'runTime' => Fn::$box['_runTime'],
			'sqlQueryNum' => 0,
			'sqlQueryTime' => 0
		);
		if (isset(Fn::$box['sqlQuery'])) {
			$data['sqlQueryNum'] = count(Fn::$box['sqlQuery']);
			foreach (Fn::$box['sqlQuery'] as $value) {
				$data['sqlQueryTime'] += $value['runTime'];
			}	
		}
		return $data;
	} else {
		return Fn::$box['_runTime'];
	}
}

/**
 * 创建一个JSON格式的数据
 * @access  public
 * @param   string      $content
 * @param   integer     $error
 * @param   string      $message
 * @param   array       $append
 * @return  void
 */
function make_json_response($content='1', $message='',$error="0",$append=array())
{
	$res = array('error' => $error, 'message' => $message, 'content' => $content);
	
	if (!empty($append))
	{
		foreach ($append AS $key => $val)
		{
			$res[$key] = $val;
		}
	}
	$val = json_encode($res);
	exit($val);
}

/**
 * 跳转
 * @param   string      $uri
 * @return  null
 **/

function refresh($uri='404.html'){
	header("Location: $uri");
	echo '<meta http-equiv="refresh" content="0;URL='.$uri.'" /><script>location.href="'.$uri.'";</script>';
	exit();
}
function refreshTop($uri='404.html'){
	echo '<script>top.location.href="'.$uri.'";</script>';
	exit();
}