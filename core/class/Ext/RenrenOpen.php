<?php
/**
 * 人人网开放平台 * @package        	Fn
 * @copyright      	Copyright (c) 2012, Fn.
 * @author         	Fn
 */
class Ext_RenrenOpen {
    public static function callbackUrl()
    {
        return Fn::$config['web_url']."index.php?c=User&a=openApiLogin&api=renren";
    }
	public static function getAuthorize()
	{
	    return "https://graph.renren.com/oauth/authorize?response_type=code&client_id=".Fn::$config['renren_appid']."&redirect_uri=".urlencode(self::callbackUrl())."&scope=&state=a%3d1%26b%3d2&x_renew=true";
	}
    public static function logout()
	{
	    //$rs = Ext_Network::openUrl("https://api.weibo.com/2/account/end_session.json?access_token=".Session::get('open_api_open_token'));
	    Session::delete('open_api_open_token');
		Session::delete('open_api_open_id');
	    //return json_decode($rs,true);
	}
    public static function getToken()
	{
	    $data = array();
		$data['client_id'] = Fn::$config['renren_appkey'];
		$data['client_secret'] = Fn::$config['renren_appsercet'];
		$data['redirect_uri'] = self::callbackUrl();
		$data['grant_type'] = 'authorization_code';
		$data['code'] = $_REQUEST['code'];		
		$rs = Ext_Network::openUrl("http://graph.renren.com/oauth/token", $data);
		$rs = json_decode($rs, true);
		if ( is_array($rs) && isset($rs['access_token']) )
		{
			Session::set('open_api_open_token', $rs['access_token']);
			Session::set('open_api_open_id', $rs['user']['id']);
		    return $rs;
		}
		return false;
	}
}
