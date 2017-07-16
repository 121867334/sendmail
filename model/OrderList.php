<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class OrderList_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_order_list','list_id');
	}
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'list_id DESC'){
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
					->field("count(list_id) as counter")
					->where($where)
					->getOne();
		return intval($rs['counter']);
	}
	public function getOrderList($where=array(),$field='*',$limit='0, 10', $order = 'ord.ord_id,list_id asc'){
		$rs = $this->db->table($this->table.' li 
										join '.Fn::$config['db_table_prefix'].'order ord on li.ord_id=ord.ord_id 
										join '.Fn::$config['db_table_prefix'].'user us on us.use_id=ord.use_id 
										join '.Fn::$config['db_table_prefix'].'customer cus on cus.cus_id=ord.cus_id 
										join '.Fn::$config['db_table_prefix'].'product pro on pro.pro_id=li.pro_id 
									')
					->field($field)
					->where($where)
					->limit($limit)
					->order($order)
					->getAll();
		return $rs;
	}
	public function getOrderListTotalNum($where=array()){
		$rs = $this->db->table($this->table.' li 
										join '.Fn::$config['db_table_prefix'].'order ord on li.ord_id=ord.ord_id 
										join '.Fn::$config['db_table_prefix'].'user us on us.use_id=ord.use_id 
										join '.Fn::$config['db_table_prefix'].'customer cus on cus.cus_id=ord.cus_id 
										join '.Fn::$config['db_table_prefix'].'product pro on pro.pro_id=li.pro_id 
									')
					->field("count(li.list_id) as counter")
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
				->where("list_id='".$id."'")
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
	public function delByWhere($where)
	{
		if(!$where)return false;
		return $this->db->table($this->table)->where($where)->delete();
	}
}