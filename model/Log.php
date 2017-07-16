<?php
/**
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Log_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_log','log_id');
	}
	
	public $join = '';
	
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'log_id DESC'){
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
					->field("count(log_id) as counter")
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
				->where("log_id='".$id."'")
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
		$this->db->table($this->table)->where($where)->update($data);
	}
	
	public function addLog($data)
	{
		$this->user = load_model('User')->get();
		$data['use_id'] = $this->user['use_id'];
		$data['log_time'] = date('Y-m-d H:i:s');
		$data['log_ip'] = Ext_Network::getClientIp();
		return $this->add($data);
	}
}