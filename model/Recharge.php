<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Recharge_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_recharge','rec_id');
	}
	
	public $rec_type_list = array('cn'=>array(1=>'洗车',2=>'维保'));
	
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'rec_id DESC'){
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
					->field("count(rec_id) as counter")
					->where($where)
					->getOne();
		return intval($rs['counter']);
	}
	public function getJoin($where=array(),$field='*',$limit='0, 10', $order = 'rec_id DESC'){
		$rs = $this->db->table($this->table.' rec 
										join '.Fn::$config['db_table_prefix'].'user us on us.use_id=rec.use_id 
										join '.Fn::$config['db_table_prefix'].'customer cus on cus.cus_id=rec.cus_id 
									')
					->field($field)
					->where($where)
					->limit($limit)
					->order($order)
					->getAll();
		return $rs;
	}
	public function getJoinTotalNum($where=array()){
		$rs = $this->db->table($this->table.' rec 
										join '.Fn::$config['db_table_prefix'].'user us on us.use_id=rec.use_id 
										join '.Fn::$config['db_table_prefix'].'customer cus on cus.cus_id=rec.cus_id 
									')
					->field("count(rec.rec_id) as counter")
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
				->where("rec_id='".$id."'")
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
	
	public function getInOutSum($cus_id)
	{
		$return = array('rec_sum'=>0,'rec_point'=>0);
		if(!$cus_id)return $return;
		$rs = $this->db->table($this->table)
					->field("sum(rec_sum) as rec_sum,sum(rec_point) as rec_point")
					->where("cus_id='".$cus_id."' and rec_status=1")
					->getOne();
		$return['rec_sum'] = $rs['rec_sum'];
		$return['rec_point'] = $rs['rec_point'];
		$rs = $this->db->table(Fn::$config['db_table_prefix'].'order_list ol join '.Fn::$config['db_table_prefix'].'order ord on ord.ord_id=ol.ord_id')
					->field("sum(ol.list_mustSum) as rec_sum,sum(ol.list_point) as rec_point")
					->where("ord.cus_id='".$cus_id."' and ord.ord_invalid=0")
					->getOne();
		$return['rec_sum'] -= $rs['rec_sum'];
		$return['rec_point'] -= $rs['rec_point'];
		return $return;
	}
}