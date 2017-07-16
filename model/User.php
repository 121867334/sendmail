<?php
/**
 * 用户模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class User_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_user','use_id');
	}
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'use_id DESC'){
		$rs = $this->db->table($this->table)
					->field($field)
					->where($where)
					->limit($limit)
					->order($order)
					->getAll();
		return $rs;
	}
	public function getTotalNum($where=array()){
		$rs = $this->db->table($this->table)
					->field("count(use_id) as counter")
					->where($where)
					->getOne();
		return intval($rs['counter']);
	}
	public function getJoin($where=array(),$field='*',$limit='0, 10', $order = 'us.use_id DESC'){
		$rs = $this->db->table($this->table.' us left join '.Fn::$config['db_table_prefix'].'department dep on dep.dep_id=us.dep_id')
					->field($field)
					->where($where)
					->limit($limit)
					->order($order)
					->getAll();
		return $rs;
	}
	public function getJoinTotalNum($where=array()){
		$rs = $this->db->table($this->table.' us left join '.Fn::$config['db_table_prefix'].'department dep on dep.dep_id=us.dep_id')
					->field("count(us.use_id) as counter")
					->where($where)
					->getOne();
		return intval($rs['counter']);
	}
	/**
	 * 按att_id查找
	 * @param
	 * @return Array
	 */
	public function getById($id,$field='*'){
		$rs = $this->db->table($this->table)
				->field($field)
				->where("use_id='".$id."'")
				->getOne();
		return $rs;
	}
	public function getOne($where){
		if(!$where)return false;
		$rs = $this->db->table($this->table)
				->field("*")
				->where($where)
				->getOne();
		return $rs;
	}
	public function setByWhere($where,$data)
	{
		if(!$where)return false;
		return $this->db->table($this->table)->where($where)->update($data);
	}
	
	public function setCache($use_id,$md=false)
	{
		if(!$use_id)return false;
		if($md)
		{
			$user = $this->getOne("md5(md5(use_id))='".$use_id."'");
		}
		else
		{
			$user = $this->getOne("use_id='".$use_id."'");
		}
		if(!$user)return false;
		$user['use_info'] = Ext_String::getStripslashes(unserialize($user['use_info']));
		$use_id = md5(md5($user['use_id']));
		$cache_time = intval(Fn::$config['cookie_time'])>0?intval(Fn::$config['cookie_time']):3600;
		$this->cache->setToFile('user/'.$use_id, $user);
		$this->cache->set($use_id, $user, $cache_time);
		return $user;
	}
	public function get($use_id='')
	{
		if(!$use_id)
		{
			$use_id = Session::get('auth_id');
		}
		else
		{
			$use_id = md5(md5($use_id));
		}
		$cache_time = intval(Fn::$config['cookie_time'])>0?intval(Fn::$config['cookie_time']):3600;
		$user = $this->cache->get($use_id);
		if (!$user)$user = $this->cache->getFromFile('user/'.$use_id,$cache_time);
		if (!$user)
		{
			$user = $this->setCache($use_id,true);
		}
		return $user;
	}
	public function setLoginSession($user)
	{
		Session::delete('auth_id');
		Session::delete('auth_key');
		$auth = $this->makeAuth($user);
		Session::set('auth_id',$auth['auth_id']);
		Session::set('auth_key',$auth['auth_key']);
	}
	public function makeAuth($user)
	{
		if(!$user)return false;
		$auth['auth_id'] = md5(md5($user['use_id']));
		$auth['auth_key'] = Ext_String::passHash($user['use_id'].$user['use_no'].$user['use_pwd']);
		return $auth;
	}
	public function checkAuth($user)
	{
		$auth_id = Session::get('auth_id');
		$auth_key = Session::get('auth_key');
		if(!$auth_id || !$auth_key)return false;
		$auth = $this->makeAuth($user);
		if($auth['auth_id'] != Session::get('auth_id'))return false;
		if($auth['auth_key'] != $auth_key)return false;
		return true;
	}
}