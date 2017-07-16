<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class UserMenu_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_user_menu','um_id');
	}
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'um_id DESC'){
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
					->field("count(um_id) as counter")
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
				->where("um_id='".$id."'")
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
	
	public function setCache($use_id,$type=1)
	{
		$use_model = load_model('User');
		$rol_model = load_model('Role');
		$user = $use_model->get($use_id);
		if(!$user)return false;
		$where['m.men_status'] = 1;
		if($user['use_id'] != '1')
		{//非超级管理员
			$men_id_str = '-1';
			if($user['use_info']['auth']['men_id'])
			{//个人权限
				$men_id_str .= ','.implode(',', $user['use_info']['auth']['men_id']);
			}
			elseif($user['use_info']['rol_id'])
			{//角色权限
				foreach ($user['use_info']['rol_id'] as $v)
				{
					$role = $rol_model->get($v);
					if($role['rol_auth']['men_id'])$men_id_str .= ','.implode(',', $role['rol_auth']['men_id']);
				}
			}
			$where[] = "(um.use_id='".$use_id."' or m.men_id in(".$men_id_str."))";
		}
		if(!$user['rol_id'])$user['rol_id'] = '-1';
		$rs = $this->db->table(Fn::$config['db_table_prefix']."menu m left join ".$this->table." um on um.men_id=m.men_id")
					->field('m.*')
					->where($where)
					->order('m.men_sort asc,m.men_id asc')
					->getAll();
		$menu = array(1=>array(),2=>array());
		if($rs)
		{
			foreach ($rs as $v)
			{
				if(!$v['men_pid'])
				{
					$menu[1][$v['men_id']] = $v;
				}
				else
				{
					$menu[1][$v['men_pid']]['list'][$v['men_id']]= $v;
				}
				$menu[2][(($v['men_c']||$v['men_a'])?$v['men_c'].'_'.$v['men_a']:$v['men_id'])] = $v;
				$menu[3][$v['men_c']] = $v;
			}
		}
		$this->cache->setToFile('menu/1_'.md5($use_id), $menu[1]);
		$this->cache->setToFile('menu/2_'.md5($use_id), $menu[2]);
		$this->cache->setToFile('menu/3_'.md5($use_id), $menu[3]);
		return $menu[$type];
	}
	public function get($use_id,$type=1)
	{
		$menu = $this->cache->getFromFile('menu/'.$type.'_'.md5($use_id));
		if (!$menu)
		{
			$menu = $this->setCache($use_id,$type);
		}
		return $menu;
	}
}