<?php
/**
 * 数据验证
 */
class Ext_Valid {
    /**
     * @var array 预定义验证格式
     */
    public static $regex = array(
            'require'=> '/.+/', //匹配任意字符，除了空和断行符
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
    		// 'email' => '/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/',
            'phone' => '/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',
            'mobile' => '/^1[3|4|5|8][0-9]\d{4,8}$/',
            'url' => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
            // 图片连接 http://www.example.com/xxx.jpg
            'img' => '^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&amp;)|&)+\.(jpg|bmp|gif|png)$',
            'currency' => '/^\d+(\.\d+)?$/',
            'number' => '/\d+$/',
            'zip' => '/^[1-9]\d{5}$/',
            'qq' => '/^[1-9]\d{4,12}$/',
            'int' => '/^[-\+]?\d+$/',
            'double' => '/^[-\+]?\d+(\.\d+)?$/',
            'english' => '/^[A-Za-z]+$/',
    		'password' => '/^.{6,20}$/',
            'username' => '/^[a-zA-Z0-9][a-zA-Z0-9_-]{3,18}[a-zA-Z0-9]$/',
            'ca' => '/^[a-zA-Z_]+[a-zA-Z0-9_]*$/',
            'float' => '/^[\-]{0,1}\d+[\.]{0,1}\d|\d+$/',
            'int' => '/^\d+$/',
            'knet_tel' => '/^\+\d{1,3}\.\d{7,14}$/',
            'ip' => '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/'
   );

    /**
     * 验证数据
     * @param string $value 待验证的数据
     * @param string $checkName 验证类型 
     * @return bool
     */
    public static function check($value, $checkName) {
    	$matchRegex = self::getRegex($checkName);
        return preg_match($matchRegex, trim($value));
    }

    /**
     * 取得验证类型的正则表达式
     * @param string $name 验证类型 
     * @return string
     */
    public static function getRegex($name) {
        if (isset(self::$regex[strtolower($name)])) {
            return self::$regex[strtolower($name)];
        } else {
        	return $name;
        }
    }
    
    public static function checkDomainName($domain,$dom_type)
    {
    	$domain = str_replace($dom_type, '', $domain);
    	if(!preg_match("/^[A-Za-z0-9_\-\x7f-\xff]{1,255}$/",$domain))return false;
    	$idn_obj = new Ext_IdnaConvert();
    	$domain = $idn_obj->encode($domain.$dom_type);
    	if(strlen($domain) > 255)return false;
    	return true;
    }

    public static function checkIDCard($id)
    {
        if (preg_match("/^\d{18}$/", $id) || preg_match("/^\d{15}$/", $id)) {
            return true;
        }
        return false;
    }
    
	/**
	 * 检查非法字符
	 * 
	 * @param string $str 待检查的字符串
	 * @return string/false 返回所包含的非法字符, 不包含则返回false 
	 */
	public static function haveInvalidChars( $str ) {
		$arr = array('\\', '/', ':', '*', '?', '"', '\'', '<', '>', ',', 
					'|', '%', '&', '&', ';', '#', '　', '');
		foreach ($arr as $ch) {
			if (false !== strstr($str, $ch)) {
				if('　' == $ch || '' == $ch) {
					return '不能显示的空字符';
				} else {
					return $ch;
				}
			}
		}
		return false;
	}
}