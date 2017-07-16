<?php
/**
 * QQ开放平台 * @package        	Fn
 * @copyright      	Copyright (c) 2012, Fn.
 * @author         	Fn
 */
class Ext_QQOpen {
    public static function callbackUrl()
    {
        return Fn::$config['web_url']."index.php?c=User&a=openApiLogin&api=qq";
    }
	public static function getAuthorize($scope=null,$callbackUrl=null)
	{
	    $scope = $scope?$scope:'get_user_info';
	    $callbackUrl = $callbackUrl?$callbackUrl:self::callbackUrl();
	    return "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=".Fn::$config['qq_appid']."&redirect_uri=".urlencode($callbackUrl)."&scope=".$scope;
	}
    public static function logout()
	{
	    //$rs = Ext_Network::openUrl("https://api.weibo.com/2/account/end_session.json?access_token=".Session::get('open_api_open_token'));
	    Session::delete('open_api_open_token');
		Session::delete('open_api_open_id');
	    //return json_decode($rs,true);
	}
    public static function getOpenID()
	{
	    $token = self::getToken();
        $data = array(
			"access_token"    => $token
		);
		$rs = Ext_Network::openUrl("https://graph.qq.com/oauth2.0/me",$data);
		if($rs)
		{
    		preg_match_all("/{.+}/", $rs, $rs);
    		$rs = json_decode($rs[0][0],true);
    		Session::set('open_api_open_id',$rs['openid']);
    		return $rs['openid'];
		}
        return false;
	}
	public static function getToken()
	{
	    $callbackUrl = self::callbackUrl();
	    $data = array(
    		"grant_type"    =>    "authorization_code",
    		"client_id"        =>    Fn::$config['qq_appid'],
    		"client_secret"    =>    Fn::$config['qq_appkey'],
    		"code"            =>    $_REQUEST['code'],
    		"state"            =>    $_REQUEST['state'],
    		"redirect_uri"    =>    self::callbackUrl()
    	);
        $rs = Ext_Network::openUrl("https://graph.qq.com/oauth2.0/token",$data);
        if($rs)
        {
            $rs = explode('&', $rs);
            $rs = explode('=', $rs[0]);
            Session::set('open_api_open_token',$rs[1]);
            return $rs[1];
        }
        return false;
	}
    public static function openUser($scope=null)
	{
	    $scope = $scope?$scope:'get_user_info';
	    self::getOpenID();
	    $data = array(
	        "access_token" => Session::get("open_api_open_token"),
			"oauth_consumer_key"    =>    Fn::$config["qq_appid"],
			"openid"                =>    Session::get('open_api_open_id'),
			"format"                =>    "json"
	    );
	    $rs = Ext_Network::openUrl("https://graph.qq.com/user/".$scope,$data);
	    return json_decode($rs,true);
	}
}
