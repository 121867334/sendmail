<?php
/**
 * Sina开放平台 * @package        	Fn
 * @copyright      	Copyright (c) 2012, Fn.
 * @author         	Fn
 */
class Ext_SinaOpen {
    public static function callbackUrl()
    {
        return Fn::$config['web_url']."index.php?c=User&a=openApiLogin&api=sina";
    }
	public static function getAuthorize()
	{
	    return "https://api.weibo.com/oauth2/authorize?response_type=code&client_id=".Fn::$config['sina_appkey']."&redirect_uri=".urlencode(self::callbackUrl())."&state=&display=&forcelogin=true";
	}
	public static function logout()
	{
	    //$rs = Ext_Network::openUrl("https://api.weibo.com/2/account/end_session.json?access_token=".Session::get('open_api_open_token'));
	    Session::delete('open_api_open_token');
		Session::delete('open_api_open_id');
	    //return json_decode($rs,true);
	}
    public static function getToken($type='code',$keys=null)
	{
	    $data = array();
		$data['client_id'] = Fn::$config['sina_appkey'];
		$data['client_secret'] = Fn::$config['sina_appsercet'];
		if ( $type === 'token' ) {
			$data['grant_type'] = 'refresh_token';
			$data['refresh_token'] = null;
		} elseif ( $type === 'code' ) {
			$data['grant_type'] = 'authorization_code';
			$data['code'] = $_REQUEST['code'];
			$data['redirect_uri'] = self::callbackUrl();
		} elseif ( $type === 'password' ) {
			$data['grant_type'] = 'password';
			$data['username'] = $keys['username'];
			$data['password'] = $keys['password'];
		} else {
			return false;
		}
		$rs = Ext_Network::openUrl("https://api.weibo.com/oauth2/access_token", $data);
		$rs = json_decode($rs, true);
		if ( is_array($rs) && !isset($rs['error']) )
		{
			Session::set('open_api_open_token', $rs['access_token']);
			Session::set('open_api_open_id', $rs['uid']);
		    return $rs;
		}
		return false;
	}
	public static function openUser()
	{
	    self::getToken();
	    $rs = Ext_Network::openUrl("https://api.weibo.com/2/users/show.json?access_token=".Session::get('open_api_open_token'));
	    $rs = json_decode($rs,true);
	    return $rs;
	}
	public static function getUserById($uid)
	{
	    self::getToken();
	    $rs = Ext_Network::openUrl("https://api.weibo.com/2/users/show.json?uid=".$uid."&access_token=".Session::get('open_api_open_token'));
	    $rs = json_decode($rs,true);
	    return $rs;
	}
}
