<?php
/**
 * 默认控制器
 * @author devil
 * @time 2013-6-21
 * @version 1.0
 */
class Main_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function index()
	{
		$this->output->display('index.html');
	}
	public function main()
	{
		$this->output->display('main.html');
	}
	public function login()
	{
		$act = $this->input->get('act');
		if($act == 'login')
		{
			$tm = Session::get('tm');
			$tk = $this->input->get('tk');
			$lang_model = load_model('Lang');
			if($tk != Ext_String::passHash(Fn::$config['encrypt_key'].$tm))show_msg($lang_model->get('checkcode_bad'),url('Main','login'),5);
			//验证码
			$com_model = load_model('Common');
			if(!$com_model->checkCode())show_msg($lang_model->get('checkcode_bad'),url('Main','login'),5);
			
			$use_name = $this->input->get('username');
			$use_pwd = $this->input->get('pwd');
			if(!$use_name || !$use_pwd)show_msg($lang_model->get('account_or_pwd_bad'),url('Main','login'),5);
			$use_name = md5($use_name);
			$use_pwd = Ext_String::passHash($use_pwd);
			$user_mod = load_model('User');
			$this->user = $user_mod->getOne("md5(use_no)='".$use_name."' and use_pwd='".$use_pwd."' and use_status=1");
			if(!$this->user)
			{
				show_msg($lang_model->get('account_or_pwd_bad'),url('Main','login'),5);
			}
			else
			{			
				$user_mod->setLoginSession($this->user);
				$user_mod->setCache($this->user['use_id']);
				refresh(url('Main','index'));
			}
		}
		//登录验证参数
		$tm = Ext_String::passHash(Ext_String::getSalt(8));
		Session::set('tm',$tm);
		$tk = Ext_String::passHash(Fn::$config['encrypt_key'].$tm);
		$this->output->set('tk',$tk);
		$this->output->display('login.html');
	}
	public function logout()
	{
		$this->outLogin();
	}
	public function left()
	{
		$this->output->display('left.html');
	}
	public function drag()
	{
		$this->output->display('drag.html');
	}
	public function top()
	{
		$this->output->display('top.html');
	}
	public function bottom()
	{
		$this->output->display('bottom.html');
	}
	public function getCheckCode(){
		return load_model('Common')->getCheckCode();
	}
	
}