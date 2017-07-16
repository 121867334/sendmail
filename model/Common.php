<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Common_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
	public function getCheckCode(){
		$code = Ext_String::getSalt(5,1);
		Session::set("check_code", Ext_String::passHash(strtolower($code)));
		return Ext_Image::vcode($code,80,25);
	}
	public function checkCode($check_code='')
	{
		if(!$check_code)$check_code = $this->input->getTrim('check_code');
		$bln = true;
		if(!$check_code)$bln = false;
		if(Session::get('check_code') != Ext_String::passHash(strtolower($check_code)))$bln = false;
		Session::delete('check_code');
		return $bln;
	}
	public function checkToken()
	{
		$use_id = $this->input->getIntval('use_id');
		$time = $this->input->getIntval('time');
		$token = $this->input->getTrim('token');
		$tb = $this->input->getTrim('tb');
		if(Ext_String::passHash(Fn::$config['encrypt_key'].$time.$use_id.$tb) != $token)
		{
			return false;
		}
		return true;
	}
	private function checkUserIP($auth_ip)
	{
		if(!$auth_ip)return false;
		if(!is_array($auth_ip))$auth_ip = explode(',',$auth_ip);
		return in_array(Ext_Network::getClientIp(),$auth_ip);
	}
	public function download($file)
	{
		$fileinfo = pathinfo($file);
		header('Content-type: application/x-'.$fileinfo['extension']);
		header('Content-Disposition: attachment; filename='.$fileinfo['basename']);
		header('Content-Length: '.filesize($file));
		readfile($file);
		exit();
	}
	public function getWhere($where_data)
	{
		$where = array();
		if($where_data)
		{
			foreach ($where_data as $v)
			{
				switch($v['symbol'])
				{
					case 1:
						$where[] = '`'.$v['field'].'`'." = '".$v['value']."'";
						break;
					case 2:
						$where[] = '`'.$v['field'].'`'." != '".$v['value']."'";
						break;
					case 3:
						$where[] = '`'.$v['field'].'`'." > '".$v['value']."'";
						break;
					case 4:
						$where[] = '`'.$v['field'].'`'." >= '".$v['value']."'";
						break;
					case 5:
						$where[] = '`'.$v['field'].'`'." < '".$v['value']."'";
						break;
					case 6:
						$where[] = '`'.$v['field'].'`'." <= '".$v['value']."'";
						break;
					case 7:
						$where[] = '`'.$v['field'].'`'." like '".$v['value']."%'";
						break;
					case 8:
						$where[] = '`'.$v['field'].'`'." like '%".$v['value']."'";
						break;
					case 9:
						$where[] = '`'.$v['field'].'`'." like '%".$v['value']."%'";
						break;
					case 10:
						$v['value'] = "'".str_replace(',', "','", $v['value'])."'";
						$where[] = '`'.$v['field'].'`'." in (".$v['value'].")";
						break;
				}
			}
		}
		return $where;
	}
}