<?php
/**
 * 网络通信扩展
 */
class Ext_Network {	
	/**
	 * 请求URL地址
	 * 
	 * @param string $url URL地址
	 * @param mixed $data 要POST的数据
	 * @param integer $timeout 超时时间
	 * @return string 响应内容 
	 */


	public static function openUrl($url, $data = null, $param = array('keep'=>false,'header'=>false,'bool'=>false,'refresh'=>true,'cookie'=>false,'timeout'=>30,'code'=>'utf-8')) {
		$param['keep'] = isset($param['keep'])? $param['keep'] : false;
		$param['bool'] = isset($param['bool'])? $param['bool'] : false;
		$param['header'] = isset($param['header'])? $param['header'] : false;
		$param['refresh'] = isset($param['refresh'])? $param['refresh'] : true;
		$param['cookie'] = isset($param['cookie'])? $param['cookie'] : false;
		$param['code'] = isset($param['code'])? $param['code'] : 'utf-8';
		$param['timeout'] = isset($param['timeout'])? $param['timeout'] : 30;

		$urlArr = @parse_url($url);
		if (empty($urlArr['host'])) return false;
		if (empty($urlArr['query'])) $urlArr['query'] = '';
		if (empty($urlArr['port'])) $urlArr['port'] = 80;
		if (empty($urlArr['path'])) $urlArr['path'] = '/';
		if (empty($urlArr['scheme'])) $urlArr['scheme'] = 'http';
		$urlArr['referer'] = $urlArr['host'];
		
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:5.0) Gecko/20100101 Firefox/5.0');
			if($param['cacert']) {
				if($param['is_ca'])
				{
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
					curl_setopt($ch, CURLOPT_CAINFO, $param['cacert']); // CA根证书（用来验证的网站证书是否是CA颁布）
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
				}
				else
				{
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
				}
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if($data){
				if(is_array($data)){
					$data = http_build_query($data);
				}
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}
			if(!empty($param['refresh'])){
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			}
			if(!empty($param['cookie'])){
				$cookie_file =  Fn::$config['data_path'].'cookie.txt';
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
				curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
			}
			$return_data = curl_exec($ch);
			$return_info = curl_getinfo($ch);
			if(empty($param['keep'])){
				curl_close($ch);
			}
			if(!empty($param['header'])){		
				return $return_info;
			}else{
				if($return_info['http_code']==200){
					if(!empty($param['bool'])){return true;}
					if($param['code'] !='utf-8' && $return_data){
						$return_data = iconv($param['code'],'utf-8',$return_data);
					}
					return $return_data;
				}else{
					return false;
				}
			}
		}else{
			$fp = @fsockopen($urlArr['host'], $urlArr['port'], $errno, $errstr, $timeout);
			if (function_exists('file_get_contents')) {
				if(is_array($data)){
					$url .=  $urlArr['query'] ? '&'.http_build_query($data) : '?'.http_build_query($data);
				}
				$body = @file_get_contents($url);
				return $body;
			}
			if ($urlArr['query']) {
				$sendStr  = "GET {$urlArr['path']}?{$urlArr['query']} HTTP/1.1\r\n";
			} else {
				$sendStr  = "GET {$urlArr['path']} HTTP/1.1\r\n";	
			}
			$sendStr .= "Host: {$urlArr['host']}:{$urlArr['port']}\r\n";
			$sendStr .= "Accept: */*\r\n";
			$sendStr .= "Referer: {$urlArr['referer']}\r\n";
			$sendStr .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.8)\r\n";
			$sendStr .= "Cache-Control: no-cache\r\n";
			if ($data) {
				$data = is_array($data) ? http_build_query($data) : $data;
				$length = strlen($query_str);
				$sendStr  .=  "Content-Type: application/x-www-form-urlencoded\r\n";
				$sendStr  .=  "Content-Length: {$length}\r\n";	
			}
			$sendStr .= "Connection: Close\r\n\r\n";
			if ($data) {
				$sendStr .= $data;	
			}
			fwrite($fp, $sendStr);
			$header = '';
			do { 
				$header .= fgets($fp, 4096);
			} 
			while (!preg_match("/\r\n\r\n$/", $header));
			$headerArr = self::parseHeader($header);
			if (in_array($headerArr['status'], array(301, 302))) {
				if (preg_match("/Location\:\s*(.+)\r\n/i", $header, $regs)) {
					$rs = self::openUrl(trim($regs[1]), $data, $timeout);
					return $rs;
				}
			} elseif (200 != $headerArr['status']) {
				return false;
			}       
			$body = '';
			while(!feof($fp)) {
				$body .= fgets($fp, 4096);
			}	
			fclose($fp);
			if (isset($header['Transfer-Encoding']) && 'chunked' == $header['Transfer-Encoding']) {
				$body = self::parseChunked($body);
			}
			if (strlen($body) < 1) {
				return false;	
			}
			return $body;
		}
	}
	
	/**
	 * 解析chunked编码
	 * 
	 * @param string $data 待解析的正文内容
	 * @return string 解析后的正文
	 */
	public static function parseChunked($data) {
		$pos = 0;
		$temp = '';
		while($pos < strlen($data)) {
			$len = strpos($data, "\r\n", $pos) - $pos; 
			$str = substr($data, $pos, $len);
			$pos += $len + 2;
			$arr = explode(';', $str, 2);
			$len = hexdec($arr[0]);
			$temp .= substr($data, $pos, $len);
			$pos += $len + 2;
		}
		return $temp;
	}
	
	/**
	 * 分析Header参数
	 * 
	 * @param mixed $header Header头信息 
	 * @return mixed 
	 */
	public static function parseHeader($header) {
		$rs = array();
		if (preg_match_all("/(.+?):\s*(.+?)\r\n/i", $header, $regs)) {
			$rs = array_combine($regs[1], $regs[2]);
		}
		$rs['status'] = 0;
		if (preg_match("/(.+) (\d+) (.+)\r\n/i", $header, $status)) {
            $rs['status'] = $status[2];
        } 
		return $rs;
	}
	
	/**
	 * 获取客端IP
	 * 
	 * @return string 
	 */
	public static function getClientIp() {
		if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != 'unknown') {   
	        $ip = $_SERVER['HTTP_CLIENT_IP'];   
	    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] !='unknown') {   
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];   
	    } else {   
			$ip = $_SERVER['REMOTE_ADDR'];   
	    }   
	    return $ip;   	
	}
	
	public static function getIpr($ip=null)
	{
	    if(!$ip)$ip = self::getClientIp();
	    list($ip1,$ip2,$ip3,$ip4)=explode(".",$ip); 
		$ipr = $ip1*pow(256,3)+$ip2*pow(256,2)+$ip3*256+$ip4;
		return $ipr;
	}
	/**
	 * 输出下载文件
	 * @param mixed 
	 * @return void
	 */
	public static function outContent($fileName, $content) {
		header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$fileName");
		echo $content;		
	}
	
	/**
	 * 获取客户端浏览器类型
	 * */
	public static function getBrowser(){
		$info = $_SERVER['HTTP_USER_AGENT'];
		if(strstr($info,'MSIE') != false){
		   return 'Internet Explorer';
		}elseif (strstr($info,'Firefox') != false){
		   return 'Firefox';
		}elseif (strstr($info,'Chrome') != false){
		   return 'Google Chrome';
		}elseif (strstr($info,'Safari') != false){
		   return 'Safari';
		}else{
		   return 'Other';
		}
	}
	
	/**
	 * 获取客户端操作系统版本
	 * */
	public static function getSystem(){
		$agent = $_SERVER['HTTP_USER_AGENT'];
       $os = false;
       if (preg_match('/win/i', $agent) && strpos($agent, '95')){
           $os = 'Windows 95';
       }else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')){
           $os = 'Windows ME';
       }else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)){
           $os = 'Windows 98';
       }else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)){
           $os = 'Windows XP';
       }else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)){
           $os = 'Windows 2000';
       }else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)){
           $os = 'Windows NT';
       }else if (preg_match('/win/i', $agent) && preg_match('/32/', $agent)){
           $os = 'Windows 32';
       }else if (preg_match('/linux/i', $agent)){
           $os = 'Linux';
       }else if (preg_match('/unix/i', $agent)){
           $os = 'Unix';
       }else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)){
           $os = 'SunOS';
       }else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)){
           $os = 'IBM OS/2';
       }else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)){
           $os = 'Macintosh';
       }else if (preg_match('/PowerPC/i', $agent)){
           $os = 'PowerPC';
       }else if (preg_match('/AIX/i', $agent)){
           $os = 'AIX';
       }else if (preg_match('/HPUX/i', $agent)){
           $os = 'HPUX';
       }else if (preg_match('/NetBSD/i', $agent)){
           $os = 'NetBSD';
       }else if (preg_match('/BSD/i', $agent)){
           $os = 'BSD';
       }else if (preg_match('/OSF1/i', $agent)){
           $os = 'OSF1';
       }else if (preg_match('/IRIX/i', $agent)){
           $os = 'IRIX';
       }else if (preg_match('/FreeBSD/i', $agent)){
           $os = 'FreeBSD';
       }else if (preg_match('/teleport/i', $agent)){
           $os = 'teleport';
       }else if (preg_match('/flashget/i', $agent)){
           $os = 'flashget';
       }else if (preg_match('/webzip/i', $agent)){
           $os = 'webzip';
       }else if (preg_match('/offline/i', $agent)){
           $os = 'offline';
       }else {
           $os = 'Unknown';
       }
       return $os;
	}
	
}
