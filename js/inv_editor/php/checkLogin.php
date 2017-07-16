<?php
session_start();
include('../../../data/db.inc.php');
if(!function_exists('passHash'))
{
	function passHash($str)
	{
		return md5(md5($str).ENCRYPT_KEY);
	}
}

$dblink = mysql_connect(DB_HOST.':'.DB_PORT,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$dblink);
mysql_query("SET NAMES 'utf8'", $dblink);
$sql = "select * from hyd_user where md5(md5(use_id))='".$_SESSION['auth_id']."' and use_status=1 and use_type>0";
$res = mysql_query($sql,$dblink);
$rs = mysql_fetch_array($res);
if(!$rs || passHash($rs['use_id'].$rs['use_name'].$rs['use_pwd']) != $_SESSION['auth_key'])
{
	exit();
}
?>