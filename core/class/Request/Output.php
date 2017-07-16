<?php
/**
 * 传出数据对象
 */
class Request_Output {
	/**
	 * @var string 控制器名
	 */ 
	public $controllerName = null;
	
	/**
	 * @var string 方法名
	 */ 
	public $actionName = null;
		
	/**
	 * @var integer 传出状态
	 */
	public $state = 0;
	
	/**
	 * @var mixed 数据模式
	 */
	public $dataMode = false;
	
	/**
	 * @var array 传出数据参数
	 */
	public $data = array();
	

	/**
	 * 构造方法
	 * 
	 * @return void 
	 */
	public function __construct() {
	}
	
	
	/**
	 * 获取模板引擎对象 
	 * @return object 缓存对象
	 */
	public function getCompiler() {
		if (!isset(Fn::$box['CompilerInstance'])) {
			Fn::$box['CompilerInstance'] = new Template_Compiler();
		}
		return Fn::$box['CompilerInstance'];
	}
	
	/**
	 * 注册标签
	 * @param mixed $name 标签名
	 * @param string $callback 标签对应的解析方法 
	 * @return void
	 */
	public function registerTag($name, $callback = null) {
		$this->getCompiler()->registerTag($name, $callback);
	}
	
	/**
	 * 显示模板数据
	 * @param string $tplFile 模板文件 
	 * @return void
	 */
	public function display($tplFile, $absPath = false) {
		if (Fn::ENTRANCE_INDEX == Fn::$config['entrance']) {
			if (is_file(Fn::$config['view_path'] . Fn::$config['template_skin'] . '/' . $tplFile)) {
				$skin = Fn::$config['template_skin'];
			} else {
				$skin = 'default';	
			}
		} elseif (Fn::ENTRANCE_ADMIN == Fn::$config['entrance']) {
			$skin = 'admin';	
		} elseif (Fn::ENTRANCE_INSTALL == Fn::$config['entrance']) {
			$skin = 'install';	
		}
		$realTplFile = Fn::$config['view_path'] . $skin . '/' . $tplFile;
		$compileFile = Fn::$config['data_path'] . 'tpl_compile/' . $skin. '/' . $tplFile . '.php';
		if ($this->getCompiler()->getCompileFile($realTplFile, $compileFile)) {
			error_reporting(E_ALL & ~E_NOTICE);
			include $compileFile;	
		}
	}
	
	/**
	 * 返回显示数据
	 * @param mixed 
	 * @return void
	 */
	public function makeHtml($tplFile = null, $htmlFile = null, $absPath = false) {
		$this->display($tplFile, $absPath);
		$content = ob_get_contents();
		ob_end_clean();
		if ($htmlFile) {
			Ext_File::write($htmlFile, $content);
		}
		return $content;
	}
	
	/**
	 * 设置方法名
	 * 
	 * @param string $actionName 方法名 
	 * @return void 
	 */
	public function setActionName ($actionName) {
		$this->actionName = $actionName;
	}
	

	/**
	 * 设置控制器名
	 * 
	 * @param string $actionName 方法名 
	 * @return void 
	 */
	public function setControllerName($controllerName) {
		$this->controllerName = $controllerName;
	}
	
	/**
	 * 设置全部数据值
	 * 
	 * @param array $data 
	 * @return void 
	 */
	public function setData ($data) {
		$this->data = $data;
	}
	
	
	/**
	 * 设置传出状态
	 * 
	 * @param integer $state 
	 * @return void 
	 */
	public function setState($state = 1) {
		$this->state = $state;
	}
	
	/**
	 * 设置输出模式
	 * @param string 模板文件 
	 * @return void
	 */
	public function setDataMode($mode = true) {
		$this->dataMode = $mode;	
	}
	
	/**
	 * 获取数据值
	 * 
	 * @param string $name 参数键名
	 * @return mixed 参数值 
	 */
	public function get($name) {
		return isset($this->data[$name]) ? $this->data[$name] : null;	
	}
	
	/**
	 * 设置数据值
	 * 
	 * @param string $name 参数键名
	 * @param mixed $value 参数值 
	 * @return void 
	 */
	public function set($name, $value = null) {
		if (is_object($name)) {
			$name = get_object_vars($name);	
		}
		if (is_array($name)) {
			$this->data = array_merge((array) $this->data, $name);
		} else {
			$this->data[$name] = $value;
		} 
	}
	
	
	public function __get($name) {
		return $this->get($name);
	}
	
	public function __set($name, $value) {
		$this->set($name, $value);	
	}
	
	public function __isset($name) {
		return isset($this->data[$name]);
	}
	
	/**
	 * 模板解析 html_options
	 * @param  $name 选项框名称 
	 * @param  $options 数据
	 * @param  $type 类型 select radio checkbox
	 * @param  $checked 选中的值 Sting ,间隔
	 * @param  $isval 判断是key(默认)或val 
	 * @return output
	 */
	private function html_options($name, $options = array(), $checked='', $type='', $isval=0)
	{			
		$checked =explode(',',$checked);
		if ($type !='checkbox' && $type !='radio'){
				$type ='select';
		}		
        $out = '';
		$id = str_replace(array('[',']','\''),array('-','',''),$name);
        if($type=='select'){
        	$out .= '<select name="'.$name.'" id="'.$id.'">';
        	foreach ($options AS $key => $val)
        	{   
        		$check_bool = $isval ? in_array($val,$checked): in_array($key,$checked);
        		$out_sel = $check_bool ? 'selected="selected"' : '';
        		$out_val = $isval ? $val : $key;		
        		$out .=  '<option value="'.$out_val.'" '.$out_sel.'>'.$val.'</option>';
        	}
        	$out .= '</select>';
        }else{
			if($type =='checkbox'){
				$name.='[]';				
			}
	        foreach ($options AS $key => $val)
	        {
	        	$check_bool = $isval ? in_array($val,$checked): in_array($key,$checked);
	        	$out_sel = $check_bool ? 'checked="checked"' : '';
	        	$out_val = $isval ? $val : $key;
				
	            $out .= '<label><input type="'.$type.'" name="'.$name.'" id="'.$id.'-'.$key.'" value="'.$out_val.'" '.$out_sel.'>'.$val.'</label> ';
	        }
	    }	
		echo $out;
	}
		
	/**
	 * 模板解析insert_scripts
	 * @param $args js路径
	 * @return none
	 */		
	function insert_scripts($args)
	{
		$scripts = array();
		$arr = explode(',', str_replace(' ', '', $args));
	
		$str = '';
		foreach ($arr AS $val)
		{
			if (in_array($val, $scripts) == false)
			{
				$scripts[] = $val;
				if ($val{0} == '/')
				{
					$str .= "<script type=\"text/javascript\" src=\"".$val."\"></script>";
				}
				else
				{
					$str .= "<script type=\"text/javascript\" src=\"".Fn::$config['web_url']."js/$val\"></script>";
				}
			}
		}	
		echo $str;
	}	
}