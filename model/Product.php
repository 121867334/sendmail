<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Product_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_product','pro_id');
	}
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'pro_id DESC'){
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
					->field("count(pro_id) as counter")
					->where($where)
					->getOne();
		return intval($rs['counter']);
	}
	public function getJoin($where=array(),$field='*',$limit='0, 10', $order = 'pro.pro_id DESC'){
		$rs = $this->db->table($this->table.' pro left join '.Fn::$config['db_table_prefix'].'product_cate cat on cat.cat_id=pro.cat_id 
								left join '.Fn::$config['db_table_prefix'].'supplier sup on sup.sup_id=pro.sup_id 
								left join '.Fn::$config['db_table_prefix'].'storage sto on sto.sto_id=pro.sto_id')
					->field($field)
					->where($where)
					->limit($limit)
					->order($order)
					->getAll();
		return $rs;
	}
	public function getJoinTotalNum($where=array()){
		$rs = $this->db->table($this->table.' pro left join '.Fn::$config['db_table_prefix'].'product_cate cat on cat.cat_id=pro.cat_id 
								left join '.Fn::$config['db_table_prefix'].'supplier sup on sup.sup_id=pro.sup_id 
								left join '.Fn::$config['db_table_prefix'].'storage sto on sto.sto_id=pro.sto_id')
					->field("count(pro.pro_id) as counter")
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
				->where("pro_id='".$id."'")
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
	
}