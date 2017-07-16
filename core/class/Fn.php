<?php
/**
 * Fn核心类
 */
class Fn {
	/**
	 * @var string 框架版本
	 */
	const CORE_VER = '1.0';
	
	/**
	 * @var mixed 前台入口
	 */
	const ENTRANCE_INDEX = 0;
	
	/**
	 * @var mixed 后台入口
	 */
	const ENTRANCE_ADMIN = 1;
	
	/**
	 * @var mixed 安装包入口
	 */
	const ENTRANCE_INSTALL = 2;
	
	/**
	 * @var Request_Input 传入数据对象
	 */
	public static $input = null;
	
	/**
	 * @var Request_Output 传出数据对象 
	 */
	public static $output = null;
	
	/**
	 * @var array 系统运行时配置
	 */
	public static $config = array();
	
	/**
	 * @var array 全局变量容器
	 */
	public static $box = array();
	
	public static $sys_manager = array();
	
	public static $contro_manager = array();
	/**
	 * 运行服务
	 * @param mixed 
	 * @return void
	 */
	public static function run($controllerName = null, $actionName = null) {
		Fn::init();
		if (!$controllerName) {
			$controllerName = Fn::$input->getControllerName();
		} 
		if (!$actionName) {
			$actionName = Fn::$input->getActionName();
		}
		$controllerName .=  '_Controller';
		if (!class_exists($controllerName) || 'Base_Controller' == $controllerName) {
			//show_msg("$controllerName: The controller does not exist");
			refresh();
		}
		try {
			$handle = new $controllerName();
			$handle->$actionName();
		} catch(Error $e) {
			catch_error($e);	
		}
		Fn::$output->setControllerName(Fn::$input->getControllerName());
		Fn::$output->setActionName(Fn::$input->getActionName());
		if (true == Fn::$output->dataMode) {
			Fn::_outputData();
		}
	}
	
	/**
	 * 初始化应用
	 * @param mixed 
	 * @return void
	 */
	public static function init() {
		if (0 == get_magic_quotes_gpc()) {
			$_GET = Ext_Array::map($_GET, 'addslashes');
			$_POST = Ext_Array::map($_POST, 'addslashes');
			$_COOKIE = Ext_Array::map($_COOKIE, 'addslashes');
		}
		$args = array_merge($_GET, $_POST);
		self::_parseRequestInfo();
		if (Fn::$config['url_mode'] > 0) {
			$pattern = "/^(([^" . Fn::$config['url_delimiter'] 
								."]+" 
								. preg_quote(Fn::$config['url_delimiter']) 
								. "?)*)" 
								. preg_quote( Fn::$config['url_suffix']) 
								. "$/is";
			$query = Fn::$config['web_query'];
			if (preg_match($pattern, $query, $pregArr)) {
				if ($pregArr[1]) {
					$query = explode(Fn::$config['url_delimiter'], $pregArr[1]);
					if (isset($query[0])) {
						$args[Fn::$config['controller_var_name']] = array_shift( $query );	
					}
					if (isset($query[0])) {
						$args[Fn::$config['action_var_name']] = array_shift( $query );	
					}
					$argsNum = count($query);
					for ($i = 0 ; $i < $argsNum; $i = $i + 2) {
						$args[$query[$i]] = null;
						if (isset($query[$i+1])) {
							$args[$query[$i]] = addslashes(urldecode($query[$i+1]));
						}
					} 
				}
			}
		}
		Fn::$input  = new Request_Input($args);
		Fn::$output = new Request_Output();
	}
	
	/**
	 * 输出数据
	 * @param mixed $output 传出数据对象
	 * @return void 
	 */
	private static function _outputData() {
		ob_end_clean();
		$v = Fn::$input->get(Fn::$config['view_var_name']) ? Fn::$input->get(Fn::$config['view_var_name']) : 'json';
		switch($v) {
			case 'xml':
				echo Ext_Xml::encode(Fn::$output);
				break;
			case 'json':
				echo json_encode(Fn::$output->data);
				break;
			case 'array':
				var_export((array) Fn::$output);
				break;
			case 'dump': 
				echo '<pre>';
				var_dump(Fn::$output);
				echo '</pre>';
				break;
		}
	}
	
	/**
	 * 解析请求信息
	 * @param mixed 
	 * @return void
	 */
	private static function _parseRequestInfo() {
		if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
        	Fn::$config['web_uri'] = $_SERVER['HTTP_X_REWRITE_URL'];
	    } elseif (isset($_SERVER['REQUEST_URI'])) {
	        Fn::$config['web_uri'] = $_SERVER['REQUEST_URI'];
	    }
	    if (isset($_SERVER['SCRIPT_NAME'])) {
	    	Fn::$config['web_script'] = $_SERVER['SCRIPT_NAME'];	
	    } elseif (isset($_SERVER['PHP_SELF'])) {
	        Fn::$config['web_script'] = $_SERVER['PHP_SELF'];
	    } elseif (isset($_SERVER['ORIG_SCRIPT_NAME'])) {
	    	Fn::$config['web_script'] = $_SERVER['ORIG_SCRIPT_NAME'];	
	    }
	    if (substr(Fn::$config['web_script'], - 1, 1) == '/') {
	        Fn::$config['web_dir'] = Fn::$config['web_script'];
	    } else {
	        Fn::$config['web_dir'] = rtrim(dirname(Fn::$config['web_script']), '\\/') . '/';
	    }
	    Fn::$config['web_host'] = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 
	    	strpos($_SERVER['SERVER_PROTOCOL'], '/'))) . '://' . $_SERVER['HTTP_HOST'];
	    if (!empty($_SERVER['PATH_INFO'])) {
			Fn::$config['web_query'] = substr($_SERVER['PATH_INFO'], 1);
		} elseif (isset($_SERVER['QUERY_STRING'])) {
			Fn::$config['web_query'] = $_SERVER['QUERY_STRING'];	
		}	
	}
}