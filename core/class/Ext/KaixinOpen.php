<?php
/**
 * 开心网开放平台 * @package        	Fn
 * @copyright      	Copyright (c) 2012, Fn.
 * @author         	Fn
 */
class Ext_KaixinOpen {
    public static function callbackUrl()
    {
        return Fn::$config['web_url']."index.php?c=User&a=openApiLogin&api=kaixin";
    }
	public static function getAuthorize($scope)
	{
	    return "http://api.kaixin001.com/oauth2/authorize?response_type=code&client_id=".Fn::$config['kaixin_appkey']."&redirect_uri=".urlencode(self::callbackUrl())."&scope=".$scope."&state=&display=popup";
	}
    public static function logout()
	{
	    //$rs = Ext_Network::openUrl("https://api.weibo.com/2/account/end_session.json?access_token=".Session::get('open_api_open_token'));
	    Session::delete('open_api_open_token');
		Session::delete('open_api_open_id');
	    //return json_decode($rs,true);
	}
    public static function getToken($scope='users_me')
	{
	    $data = array(
        	'grant_type' => "authorization_code",
        	'code' => $_REQUEST['code'],
        	'client_id' => Fn::$config['kaixin_appkey'],
        	'client_secret' => Fn::$config['kaixin_appsercet'],
        	'redirect_uri' => self::callbackUrl(),
	        'scope' => $scope
        );	
		$rs = Ext_Network::openUrl("http://api.kaixin001.com/oauth2/access_token", $data);
		$rs = json_decode($rs, true);
		if ( is_array($rs) && isset($rs['access_token']) )
		{
			Session::set('open_api_open_token', $rs['access_token']);
		    return $rs;
		}
		return false;
	}
	public static function openUser($more=0)
	{
	    self::getToken();
	    $fields = "uid,name,logo120,logo50";
	    if($more == 1)
	    {
	        $fields = "uid,name,gender,hometown,city,status,logo120,logo50,birthday,bodyform,blood,marriage,trainwith,interest,favbook,favmovie,favtv,idol,motto,wishlist,intro,education,schooltype,school,class,year,career,company,dept,beginyear,beginmonth,endyear,endmonth,isStar,pinyin,online";
	    }
	    $rs = Ext_Network::openUrl("https://api.kaixin001.com/users/me.json?access_token=".Session::get('open_api_open_token')."&fields=".$fields);
	    $rs = json_decode($rs,true);
	    if($rs['uid'])
	    {
    	    Session::set('open_api_open_id', $rs['uid']);
    	    return $rs;
	    }
	    return false;
	}
}
