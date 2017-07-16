<?php
/**
 * 控制器
 * @author devil
 * @time 2013-6-21
 * @version 1.0
 */
class User_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
		$this->setMenuName();
	}
	
	public function index()
	{
		$use_mod = load_model('User');
		$dep_mod = load_model('Department');
		//搜索条件
		$params = $where = array();
		$where[] = 'use_id !=1';
		$params['use_key'] = $this->input->getTrim('use_key');
		$params['dep_id'] = $this->input->getIntval('dep_id');
		$params['use_entry_date1'] = $this->input->getTrim('use_entry_date1');
		$params['use_entry_date1'] = $this->input->getTrim('use_entry_date1');
		$params['use_status'] = $this->input->getTrim('use_status');
		if($params['use_key'] != '')$where[] = "(us.use_no like '%".$params['use_key']."%' or us.use_name like '%".$params['use_key']."%')";
		if($params['use_status'] != '')$where['us.use_status'] = $params['use_status'];
		if($params['dep_id'] != '')
		{
			$sub_id = $dep_mod->getArrayByParentID($params['dep_id']);
			if($sub_id)$sub_id = implode(',', $sub_id);
			if(!$sub_id)$sub_id = '-1';
			$where[] = "us.dep_id in(".$sub_id.")";
		}
		if($params['use_entry_date1'] != '')$where[] = "us.use_entry_date>='".$params['use_entry_date1']."'";
		if($params['use_entry_date2'] != '')$where[] = "us.use_entry_date<='".$params['use_entry_date2']."'";
		//排序
		$order = $this->input->getTrim('order');
		if(!$order)$order = 'us.use_id';
		$by = $this->input->getTrim('by');
		if(!$by)$by = 'desc';
		$params['order'] = $order;
		$params['by'] = $by;
		//分页
		$p = $this->input->getIntval('p');
		$p = $p>0?$p:1;
		$params['page_size'] = $this->input->getIntval('page_size');
		if($params['page_size'] <= 0)$params['page_size'] = 20;
		$params['p'] = '@';
		$url = url('User','index',$params);
		
		$totalNum = $use_mod->getJoinTotalNum($where);
		$page = new Ext_Page($url, $totalNum, $p, $params['page_size']);
		$rs = $use_mod->getJoin($where,"*",$page->limit(),$params['order'].' '.$params['by']);
		
		$dep_select = $dep_mod->getTreeSelect(0,$params['dep_id'],'dep_id','dep_id');
		
		$this->output->set($params);
		$this->output->set(array(
				'pageHtml'         => $page->html(),
				'totalPage'        => $page->totalPage(),
				'page'             => $p,
				'totalNum'         => $totalNum,
				'list'             => $rs,
				'dep_select' => $dep_select,
		));
		$this->output->display('User/index.html');
	}
	public function add()
	{
		$dep_model = load_model('Department');
		$use_model = load_model('User');
		$rol_model = load_model('Role');
		$lang_model = load_model('Lang');
		
		$use_id = $this->input->getIntval('use_id');
		$dep_id = $this->input->getIntval('dep_id');
		if($use_id)
		{
			$user = $use_model->get($use_id);
			if(!$user)show_msg($lang_model->get('action_illegal'),'',5);
			$dep_id = $user['dep_id'];
		}
		elseif($dep_id > 0)
		{
			$dep = $dep_model->getById($dep_id);
			if(!$dep)show_msg($lang_model->get('action_illegal'),'',5);
		}
		$act = $this->input->getTrim('act');
		if($act == 'save')
		{
			$date = date('Y-m-d');
			$data = $this->input->get('data');
			if(trim($data['use_name']) == '')show_msg($lang_model->get('enter_use_name'),'',5);
			if($data['use_pwd'])
			{
				$data['use_pwd'] = Ext_String::passHash($data['use_pwd']);
			}
			else
			{
				unset($data['use_pwd']);
			}
			if($data['use_status'] == 2 && !$data['use_leave_date'])$data['use_leave_date'] = $date;	//离职日期
			if($data['use_permanent'] == 1 && !$data['use_permanent_date'])$data['use_permanent_date'] = $date;	//转正日期
			if($data['use_info'])$data['use_info'] = addslashes(serialize($data['use_info']));
			$this->db->begin();	//Mysql开始事务
			$commit_status = true;
			if($use_id)
			{
				$rs = $use_model->getOne("use_no='".$data['use_no']."' and use_id!='".$use_id."'");
				if($rs)show_msg($lang_model->get('exists_use_no'),'',5);
				if(!$use_model->set($use_id,$data))
				{
					$this->db->rollBack();	//回滚
					$commit_status = false;
				}
			}
			else
			{
				$data['use_time'] = date('Y-m-d H:i:s');
				$use_id = $use_model->add($data);
				if(!$use_id)
				{
					$this->db->rollBack();	//回滚
					$commit_status = false;
				}
				if(!$data['use_no'])
				{
					$dep = $dep_model->getById($data['dep_id']);
					$update_data = array();
					$update_data['use_no'] = Fn::$config['use_no_prefix'].substr(preg_replace('/[^a-zA-Z]+/', '', $dep['dep_no']),0,3).date('y').str_pad($use_id, 4,'0',STR_PAD_LEFT);
					if(!$data['use_pwd'])$update_data['use_pwd'] = Ext_String::passHash($update_data['use_no']);
					if(!$data['use_entry_date'])$update_data['use_entry_date'] = $date;
					if(!$use_model->set($use_id,$update_data))
					{
						$this->db->rollBack();	//回滚
						$commit_status = false;
					}
				}
			}
			if($commit_status)
			{
				$this->db->commit();	//Mysql提交事务
				
				//日志
				$log = array();
				$log['log_table'] = Fn::$config['db_table_prefix'].'user';
				$log['log_key'] = $user['use_id'];
				if($user['use_id'])
				{
					$log['log_bak'] = serialize($user);
					$log['log_content'] = $lang_model->get('edit_info');
				}
				else
				{
					$log['log_content'] = $lang_model->get('add_info');
				}
				load_model('Log')->addLog($log);
				
				show_msg($lang_model->get('action_success'),'index.php?c=User&a=index',5);
			}
			else
			{
				show_msg($lang_model->get('action_failure'),'',5);
			}
		}
		
		$role_list = $rol_model->get();
		$dep_select = $dep_model->getTreeSelect(0,$dep_id,'data[dep_id]','dep_id');
		
		if(!isset($user['use_info']['rol_id']))$user['use_info']['rol_id'] = array();
		$this->output->set(array(
				'user' => $user,
				'dep_select' => $dep_select,
				'role_list' => $role_list,
		));
		
		$this->output->display('User/add.html');
	}
	public function pwd()
	{
		$user_mod = load_model('User');
		$lang_model = load_model('Lang');
		$act = $this->input->get('act');
		if($act == 'save')
		{
			$old_pwd = $this->input->getTrim('old_pwd');
			$new_pwd_2 = $this->input->getTrim('new_pwd_2');
			$new_pwd = $this->input->getTrim('new_pwd');
			if($old_pwd == '')
			{
				show_msg($lang_model->get('enter_old_pwd'),'',3);
			}
			if(!Ext_Valid::check($new_pwd, 'password'))
			{
				show_msg($lang_model->get('enter_old_pwd'),'',3);
			}
			if($new_pwd != $new_pwd_2)
			{
				show_msg($lang_model->get('enter_two_pwd_not'),'',3);
			}
			if($this->user['use_pwd'] != Ext_String::passHash($old_pwd))
			{
				show_msg($lang_model->get('enter_old_pwd_bad'),'',3);
			}
			$user_mod->set($this->user['use_id'],array('use_pwd'=>Ext_String::passHash($new_pwd)));
			//日志
			$log = array();
			$log['log_table'] = Fn::$config['db_table_prefix'].'user';
			$log['log_key'] = $this->user['use_id'];
			$log['log_bak'] = serialize($this->user);
			$log['log_content'] = $lang_model->get('edit_use_pwd');
			load_model('Log')->addLog($log);
			
			//更新缓存
			$this->user['use_pwd'] = Ext_String::passHash($new_pwd);
			$user_mod->setCache($this->user['use_id']);
			$user_mod->setLoginSession($this->user);
			
			show_msg($lang_model->get('action_success'),'',3);
		}
		
		$this->output->display('User/pwd.html');
	}
	public function chooseUser()
	{
		$user_model = load_model('User');
		//条件
		$where = array();
		$where[] = 'use_id !=1';
		$params['use_status'] = $this->input->getTrim('use_status');
		if($params['use_status'] == '')$params['use_status'] = '1';
		if($params['use_status'] != 'all')$where['use_status'] = $params['use_status'];
		$params['use_key'] = $this->input->getTrim('use_key');
		if($params['use_key'] != '')$where[] = "(use_name like '%".$params['use_key']."%' or use_no like '%".$params['use_key']."%')";
	
		$rs = $user_model->search($where,"*",null,'use_id desc');
	
		$fun = $this->input->getTrim('fun');
		$alone = $this->input->getTrim('alone');
		$this->output->set($params);
		$this->output->set(array(
				'list' => $rs,
				'fun' => $fun,
				'alone' => $alone,
				'use_status_list'  =>load_model('Config')->use_status_list,
		));
		if(is_ajax())
		{
			$this->output->display('User/userList.html');
		}
		else
		{
			$this->output->display('User/chooseUser.html');
		}
	}
}