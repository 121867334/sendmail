<?php
/**
 * 缓存管理
 */
class Cache {		
	/**
	 * @var object MEM实例
	 */
	private $_mem = null;
	
	/**
	 * @var array 缓存服务器配置
	 */
	private $_cacheConfig = array();

	/**
	 * @var Boolean 是否压缩数据
	 */
	private $_compress = false;
	
	/**
	 * 初始化Mem连接
	 * 
	 * @param string $host 服务器
	 * @param integer $port 端口 
	 * @return void 
	 */
	public function __construct() {
		$this->_cacheConfig = Fn::$config['cache_config'];
		$this->_compress = Fn::$config['cache_compress'];
	}
	
	/**
	 * 初始化Memcache缓存
	 * @param mixed 
	 * @return void
	 */
	public function initMemcache() {
		if (!is_null($this->_mem)) {
			return $this->_mem;
		}
		if (count($this->_cacheConfig) < 1) {
			return false;
			show_error('Cache configuration error');	
		}
		if (!class_exists('Memcache', false)) {
			return false;
			show_error('Memcache support is disabled');	
		}
		$this->_mem  = new Memcache();
		foreach ($this->_cacheConfig as $value) {
			list($host, $port) = explode(':', $value);
			$this->_mem->addServer($host, $port, false);	
		}
		return $this->_mem;	
	}

	
	/**
	 * 获取缓存数据
	 * 
	 * @param string $name 缓存键名 
	 * @return mixed 缓存值 
	 */
	public function get($name) {
		if(!$this->initMemcache())return false;
		return $this->_mem->get($name);
	}
	
	/**
	 * 设置缓存数据
	 * 
	 * @param string $name 缓存键名
	 * @param mixed $value 缓存值 
	 * @param integer $lifeTime 生存周期
	 * @return mixed 
	 */ 
	public function set($name, $value, $lifeTime = 0) {
		if(!$this->initMemcache())return false;
		return $this->_mem->set($name, $value, $this->_compress, $lifeTime);	
	}
	
	/**
	 * 删除缓存数据
	 * 
	 * @param string $name 缓存键名 
	 * @return void 
	 */
	public function delete($name) {
		if(!$this->initMemcache())return false;
		return $this->_mem->delete($name);
	}
	
	/**
	 * 清空缓存
	 * 
	 * @return void 
	 */
	public function clear() {
		if(!$this->initMemcache())return false;
		return $this->_mem->flush();
	}
	
	/**
	 * 关闭缓存连接
	 * 
	 * @return void 
	 */
	public function close() {
		if(!$this->initMemcache())return false;
		return $this->_mem->close();
	}
	
	/**
	 * 获取服务器状态
	 * @param mixed 
	 * @return void
	 */
	public function getStats() {
		if(!$this->initMemcache())return false;
		return $this->_mem->getExtendedStats();	
	}
	
	
	/**
	 * 设置缓存
	 * @param mixed 
	 * @return void
	 */
	public static function setToDb($cacheName, $cacheValue) {
		if (is_null($cacheValue)) {
			load_db()->table(Fn::$config['cache_table'])->where(array("name = '$cacheName'"))->delete();	
		} else {
			$cacheValue = addslashes(json_encode($cacheValue));
			$data = array('name' => $cacheName, 'value' => $cacheValue);
			load_db()->table(Fn::$config['cache_table'])->replace($data);
		}
	}
	
	/**
	 * 读取缓存
	 * @param mixed 
	 * @return void
	 */	
	public static function getFromDb($cacheName) {
		$res = load_db()->table(Fn::$config['cache_table'])->where(array("name = '$cacheName'"))->getOne();
		if ($res) {
			return json_decode($res['value'], true);
		} else {
			return null;	
		}
	}
	
	
	/**
	 * 设置缓存
	 * @param mixed 
	 * @return void
	 */
	public static function setToFile($cacheName, $cacheValue) {
		$cacheFile = Fn::$config['data_path'] . "cache/$cacheName.php";
		if (is_null($cacheValue)) {
			return @unlink($cacheFile);	
		} else {
			$content = "<?php\nif (!defined('APP_PATH')) die('error');\nreturn " 
						. var_export($cacheValue, true) . ";";
			$rs = Ext_File::write($cacheFile, $content);	
			return $rs;
		}
	}
	
	
	/**
	 * 读取缓存
	 * @param mixed 
	 * @return void
	 */	
	public static function getFromFile($cacheName, $expire = 0) {
		$cacheFile = Fn::$config['data_path'] . "cache/$cacheName.php";
		if (is_file($cacheFile) && (filemtime($cacheFile)+$expire-Ext_Date::now())*$expire>= 0 ) {						
			return require $cacheFile;
		} else {
			return null;
		}
	}
	
	/**
	 * 设置内存缓存
	 * @param mixed 
	 * @return void
	 */
	public static function setToBox($cacheKey, $cacheValue) {
		Fn::$box['Cache'][$cacheKey] = $cacheValue;
	}
	
	/**
	 * 获取内存缓存
	 * @param mixed 
	 * @return void
	 */
	public static function getFromBox($cacheKey) {
		if (isset(Fn::$box['Cache'][$cacheKey])) {
			return Fn::$box['Cache'][$cacheKey];
		} else {
			return null;	
		}
	}
	
	/**
	 * 设置HTML缓存
	 * @param mixed 
	 * @return void
	 */
	public static function setToHtml($htmlFile, $content = null, $expire = 0) {
		if (!$expire) {
			return false;
		} 
		if (is_null($content)) {
			$content = ob_get_contents();	
		}
		$htmlFile = Fn::$config['data_path'] . "html_cache/" . $htmlFile;
		return Ext_File::write($htmlFile, $content);
	}
	
	/**
	 * 读取HTML缓存
	 * @param mixed 
	 * @return void
	 */
	public static function getFromHtml($htmlFile, $expire = 0) {
		if (!$expire) {
			return false;	
		}
		$htmlFile = Fn::$config['data_path'] . "html_cache/" . $htmlFile;
		if (is_file($htmlFile) && filemtime($htmlFile) + $expire > Ext_Date::now()) {	
			return @readfile($htmlFile);
		} else{
			return false;
		}	
	}
}
