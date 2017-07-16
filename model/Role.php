<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Role_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_role','rol_id');
	}
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'rol_id DESC'){
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
					->field("count(rol_id) as counter")
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
				->where("rol_id='".$id."'")
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
	
	public function setCache($rol_id)
	{
		$rs = $this->db->table($this->table)
					->field('*')
					->where("rol_status=1")
					->order('rol_id asc')
					->getAll('rol_id');
		if($rs)
		{
			foreach($rs as &$v)
			{
				$v['rol_auth'] = unserialize($v['rol_auth']);
			}
			unset($v);
		}
		$this->cache->setToFile('role', $rs);
		return $rs;
	}
	public function get($rol_id=null)
	{
		$this->role = $this->cache->getFromFile('role');
		if (!$this->role)
		{
			$this->role = $this->setCache();
		}
		return $rol_id?$this->role[$rol_id]:$this->role;
	}
}