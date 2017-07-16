<?php
/**
 * 基础控制器
 * @author devil
 * @time 2013-6-21
 * @version 1.0
 */
class Base_Controller extends Controller {
	
	public function __construct() {
		parent::__construct();
		$this->checkLogin();
		$this->assignData();
	}
	
	protected function assignData() {
		$this->output->set(array(
			'web_name' => Fn::$config['web_name'],
			'web_url' => Fn::$config['web_url'],
			'web_path' => Fn::$config['web_path'],
			'template_skin' => Fn::$config['template_skin'],
		));
	}
	
	protected function setMenuName()
	{
		$menu_1 = load_model('UserMenu')->get($this->user['use_id']);
		$menu = $this->getMenu();
		$a_name = $menu['men_name'];
		$c_name = $menu_1[$menu['men_pid']]['men_name'];
		$this->output->set('a_name',$a_name);
		$this->output->set('c_name',$c_name);
	}
	
	protected function checkLogin()
	{
		//检查是否可以直接通过（不需要登录，权限验证）。
		if($this->checkIsAuthPass())return true;
		
		//验证是否登录或者登录超时
		$auth_id = Session::get('auth_id');
		$auth_key = Session::get('auth_key');
		if(!$auth_id || !$auth_key)
		{
			$this->outLogin();
		}
		//验证登录信息是否合法
		$use_mod = load_model('User');
		$this->user = $use_mod->get();
		if(!$this->user)$this->outLogin();
		if(!$use_mod->checkAuth($this->user))$this->outLogin();
		if(!$this->user['use_status'])$this->outLogin();
		
		if($this->user['use_type'] < 2)
		{
			//检查权限
			$this->actionAuth();
		}
		
		return true;
	}
	protected function actionAuth()
	{
		if(!$this->checkActionAuth())
		{
			if(is_ajax())
			{
				exit(json_encode(array('code'=>'1','msg'=>load_model('Lang')->get('action_illegal'))));
			}
			else
			{
				show_msg(load_model('Lang')->get('action_illegal'),'',5);
			}
		}
	}
	protected function checkActionAuth($c='',$a='')
	{
		if(!$c)$c = $this->input->getControllerName();
		if(!$a)$a = $this->input->getActionName();
		if($c == 'Main' && in_array($a, array('index','main','top','left','drag','logout','bottom')))return true;
		if($c == 'User' && in_array($a, array('chooseUser')))return true;
		
		return true;
	}
	protected function checkIsAuthPass()
	{
		$a = $this->input->getActionName();
		$c = $this->input->getControllerName();
		if($c == 'Main' && in_array($a, array('login','getCheckCode')))return true;
		if($c == 'Upload')
		{
			if(!load_model('Common')->checkToken())
			{
				exit(json_encode(array('error'=>'-1','errorMsg'=>load_model('Lang')->get('action_illegal'))));
			}
			return true;
		}
		/*if($c != 'Main' || !in_array($a, array('index')))
		{
			if(!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], Fn::$config['web_url']) === false)
			{
				$this->outLogin();
			}
		}*/
		return false;
	}
	protected function getMenu()
	{
		$c = $this->input->getControllerName();
		$a = $this->input->getActionName();
		$menu = load_model('UserMenu')->get($this->user['use_id'],2);
		return $menu[$c.'_'.$a];
	}
	protected function outLogin()
	{
		Session::clear();
		if(is_ajax())
		{
			exit(json_encode(array('code'=>'1','msg'=>load_model('Lang')->get('not_login'))));
		}
		else
		{
			refreshTop(url('Main','login'));
		}
	}
}
