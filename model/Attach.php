<?php
/**
 * Attach附件模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Attach_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_attach','att_id');
	}	
	
	public function makeAttachName() {
		return time() . Ext_String::getSalt();	
	}
	
	public function getAttachPath($attach) {
		return APP_PATH . Fn::$config['upload_path'] .'/'.$attach;	
	}
	
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'att_id', $by = 'DESC'){
		$rs = $this->db->table($this->table)
					->field($field)
					->where($where)
					->limit($limit)
					->order($order.' '.$by)
					->getAll();
		return $rs;
	}
	public function getTotalNum($where=array()){
		$rs = $this->db->table($this->table)
					->field("count(att_id) as counter")
					->where($where)
					->getOne();
		return intval($rs['counter']);
	}
	/**
	 * 按att_id查找
	 * @param
	 * @return Array
	 */
	public function getById($att_id,$field='*'){
		$rs = $this->db->table($this->table)
				->field($field)
				->where("att_id='".$att_id."'")
				->getOne();
		return $rs;
	}
	public function getOne($where,$field='*'){
		$rs = $this->db->table($this->table)
				->field($field)
				->where($where)
				->order('add_time desc')
				->limit('0,1')
				->getOne();
		return $rs;
	}
	
	public function setByWhere($where,$data)
	{
		$this->db->table($this->table)->where($where)->update($data);
	}
}