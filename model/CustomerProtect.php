<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class CustomerProtect_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_customer_protect','pro_id');
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
	public function getJoin($where=array(),$field='*',$limit='0, 10', $order = 'us.use_id DESC'){
		$rs = $this->db->table($this->table.' pro join '.Fn::$config['db_table_prefix'].'customer cus on pro.cus_id=cus.cus_id')
					->field($field)
					->where($where)
					->limit($limit)
					->order($order)
					->getAll();
		return $rs;
	}
	public function getJoinTotalNum($where=array()){
		$rs = $this->db->table($this->table.' pro join '.Fn::$config['db_table_prefix'].'customer cus on pro.cus_id=cus.cus_id')
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
	public function getProtectStatus($cus_id,$type='bln')
	{
		$rs = $this->getOne("cus_id='".$cus_id."' and pro_status=1 and pro_end_date>='".date('Y-m-d')."'");
		if($type == 'bln')
		{
			return $rs;
		}
		return $rs?true:false;
	}
	public function getProtectInfo($cus_id)
	{
		$rs = $this->getOne("cus_id='".$cus_id."' and pro_status=1 and pro_end_date>='".date('Y-m-d')."'");
		if($rs)
		{
			$rs['user'] = load_model('User')->get($rs['use_id']);
		}
		return $rs;
	}
	public function setByWhere($where,$data)
	{
		if(!$where)return false;
		return $this->db->table($this->table)->where($where)->update($data);
	}
	
	public function protect($cus_id,$use_id,$pro_type=0,$end_date='',$remark='')
	{
		$conf_model = load_model('Config');
		$lang_mod = load_model('Lang');
		$use_mod = load_model('User');
		
		$this->user = $use_mod->get();
		$date = date('Y-m-d');
		$time = date('Y-m-d H:i:s');
		
		//是否已经被保护
		$rs = $this->getOne("cus_id='".$cus_id."' and pro_end_date>='".$date."' and pro_status=1");
		if($rs)
		{
			return array('code'=>1,'msg'=>$lang_mod->get('protect_bad'));
		}
		
		$config = $conf_model->getConfig('customer');	//配置信息
		$config['protect_amount'] = intval($config['protect_amount']);
		$config['protect_days_not'] = intval($config['protect_days_not']);
		$config['protect_days'] = intval($config['protect_days']);
		if($config['protect_amount'])
		{//保护配额是否足够
			$rs = $this->getJoinTotalNum("pro.use_id='".$use_id."' and pro.pro_end_date>='".$date."' and pro.pro_status=1 and cus.cus_status=1");
			if($config['protect_amount'] <= $rs)
			{
				return array('code'=>1,'msg'=>$lang_mod->get('protect_amount_bad'));
			}
		}
		if($config['protect_days_not'])
		{//多少天内保护掉线，不能再保护
			$days_ago = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-$config['protect_days_not'],date('Y')));
			$rs = $this->getOne("cus_id='".$cus_id."' and use_id='".$use_id."' and pro_cancel_time>='".$days_ago."'");
			if($rs)
			{
				return array('code'=>1,'msg'=>$config['protect_days_not'].$lang_mod->get('protect_days_not'));
			}
		}
		
		$this->db->begin();	//Mysql开始事务
		$data = array();
		$data['pro_status'] = 0;
		$data['pro_cancel_time'] = $time;
		$data['pro_cancel_uid'] = $this->user['use_id'];
		$this->setByWhere("cus_id='".$cus_id."' and pro_status=1 and pro_end_date>='".$date."'",$data);
		if(!$end_date)
		{
			if(!$config['protect_days'])
			{
				$end_date = '9999-12-31';
			}
			else
			{
				$end_date = date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$config['protect_days'],date('Y')));
			}
		}
		if(!$remark)$remark = $this->user['use_name'].'['.$this->user['use_no'].']:'.$lang_mod->get('assign_customer');
		
		$data = array();
		$data['use_id'] = $use_id;
		$data['cus_id'] = $cus_id;
		$data['pro_end_date'] = $end_date;
		$data['pro_status'] = 1;
		$data['pro_time'] = $time;
		$data['pro_uid'] = $this->user['use_id'];
		$data['pro_remark'] = $remark;
		$data['pro_type'] = $pro_type;
		$pro_id = $this->add($data);
		if(!$pro_id)
		{
			$this->db->rollBack();	//回滚
			return array('code'=>1,'msg'=>$lang_mod->get('action_failure'));
		}
		//日志
		$log = array();
		$log['log_table'] = Fn::$config['db_table_prefix'].'customer_protect';;
		$log['log_key'] = $pro_id;
		if($use_id == $this->user['use_id'])
		{
			$log['log_content'] = $lang_mod->get('protect_customer');
		}
		else
		{
			$log['log_content'] = $lang_mod->get('assign_customer');
		}
		$log_id = load_model('Log')->addLog($log);
		if(!$log_id)
		{
			$this->db->rollBack();	//回滚
			return array('code'=>1,'msg'=>$lang_mod->get('action_failure'));
		}
		$this->db->commit();	//Mysql提交事务
		
		return array('code'=>0,'msg'=>$lang_mod->get('action_success'),'pro_id'=>$pro_id);
	}
}