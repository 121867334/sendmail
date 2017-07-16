<?php
/**
 * 系统配置
 * @author devil
 * @time 2013-6-21
 * @version 1.0
 */
class Config_Model extends Model {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 初始化核心配置
	 * @param mixed 
	 * @return void
	 */
	public function initConfig() {
		$config = $this->db->table('#@_config')->getAll();
		$config = Ext_Array::format($config, 'con_name', 'con_value');
		$rs = write_config(Fn::$config['data_path'] .'web-config.php', $config);
		return $rs;
	}
	

	/**
	 * 更新配置参数
	 * @param mixed 
	 * @return void
	 */
	public function setConfig($name, $value = null) {
		if (is_array($name)) {
			foreach ($name as $key => $val) {
				$this->db->table('#@_config')
					->replace(array('con_name' => $key, 'con_value' => trim($val)));
			}	
		} else {
			$this->db->table('#@_config')
				->replace(array('con_name' => $name, 'con_value' => trim($value)));
		}
		$this->initConfig();
	}
	public function getConfig($type)
	{
		import_file(Fn::$config['data_path'].'web-'.$type.'.php');
		return Fn::$config[$type];
	}
	/**
	 * 更新文件缓存
	 * @param mixed 
	 * @return void
	 */
	public function clearFileCache($cache_name = '') {
		if(empty($cache_name)){
			return Ext_Dir::del(Fn::$config['data_path'] . 'cache/');
		}else{
			$cache_name_arr = explode(',',$cache_name);
			foreach($cache_name_arr as $cache_name){
				return Ext_Dir::delFile(Fn::$config['data_path'] . 'cache/' . $cache_name . '.php');
			}
		}
	}
	
	
	public function getSkinList() {
		$source = Fn::$config['view_path'];
		$dirs = Ext_Dir::getDirList($source, Ext_Dir::TYPE_DIR, array('admin', '.svn', 'install'));
		return $dirs;	
	}
}



