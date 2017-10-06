<?php
/**
 * 控制器
 * @author devil
 * @time 2013-6-21
 * @version 1.0
 */
class Travel_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function index()
	{
		//搜索条件
		$params = $where = array();
		$params['re_confNumber'] = $this->input->getTrim('re_confNumber');
		$params['re_customer'] = $this->input->getTrim('re_customer');
		$params['date1'] = $this->input->getTrim('date1');
		$params['date2'] = $this->input->getTrim('date2');
		if($params['re_confNumber'] != '')$where[] = "re_confNumber like '%".$params['re_confNumber']."%'";
		if($params['re_customer'] != '')$where[] = "re_customer like '%".$params['re_customer']."%'";
		if($params['date1'] != '')$where[] = "re_arrivalTime >= '".$params['date1']."'";
		if($params['date2'] != '')$where[] = "re_arrivalTime <= '".$params['date2']." 23:59:59'";
		if($params['re_status'] != '')$where['re_status'] = $params['re_status'];
		
		//分页
		$p = $this->input->getIntval('p');
		$p = $p>0?$p:1;
		$params['p'] = '@';
		$url = url('Travel','index',$params);
		
		$rs = $this->db->table("tra_record")->field("count(re_id) as counter")->where($where)->getOne();
		$totalNum = $rs['counter'];
		$page = new Ext_Page($url, $totalNum, $p, 20);
		$rs = $this->db->table("tra_record")->field("*")->where($where)->order("re_id desc")->getAll();
		
		$this->output->set($params);
		$this->output->set(array(
				'pageHtml'         => $page->html(),
				'totalPage'        => $page->totalPage(),
				'page'             => $p,
				'totalNum'         => $totalNum,
				'list'             => $rs,
		));
		$this->output->display('Travel/index.html');
	}
	public function add()
	{
		$re_id = $this->input->getIntval('re_id');
		if($re_id)
		{
			$record = $this->db->table("tra_record")->field("*")->where("re_id='".$re_id."'")->getOne();
			if(!$record)show_msg('非法操作','',5);
		}
		$act = $this->input->getTrim('act');
		if($act == 'save')
		{
			$data = $this->input->get('data');
			if($re_id)
			{
				$this->db->table("tra_record")->where("re_id='".$re_id."'")->update($data);
			}
			else
			{
				$data['re_time'] = date('Y-m-d H:i:s');
				$cus_id = $this->db->table("tra_record")->insert($data);
			}
			
			show_msg('操作成功','index.php?c=Travel&a=index',5);
		}
		
		$this->output->set(array(
				'record' => $record,
		));
		
		$this->output->display('Travel/add.html');
	}
	
	public function view()
	{
		$re_id = $this->input->getIntval('re_id');
		if($re_id)
		{
			$record = $this->db->table("tra_record")->field("*")->where("re_id='".$re_id."'")->getOne();
			if(!$record)show_msg('非法操作','',5);
		}
		
		$this->output->set(array(
				'record' => $record,
		));
		
		$this->output->display('Travel/view.html');
	}
	
	public function sendEmail()
	{
		$re_id = $this->input->get('re_id');
		if(!$re_id)show_msg('非法操作','',5);
		if(!is_array($re_id))$re_id = explode(',', $re_id);
		
		$act = $this->input->getTrim('act');
		if($act == 'save')
		{
			$em_model = load_model('Email');
			$param=array("is_smtp"=>true,"is_html"=>true);
			$param['use_id'] = $this->user['use_id'];
			$param['key_table'] = 'tra_record';
			
			$config = $this->db->table("tra_config")->field("*")->getOne();
			if(!$config['em_port'] || !$config['em_host'] || !$config['em_user'] || !$config['em_pwd'])show_msg('请先配置邮件服务器信息','',5);
			$param['port'] = $config['em_port'];
			$param['host'] = $config['em_host'];
			$param['user'] = $config['em_user'];
			$param['pwd'] = $config['em_pwd'];
			$param['from_name'] = $config['from_name'];
			
			$subject = $this->input->get('em_subject');
			$body = $this->input->get('em_body');
			if(!$subject)show_msg('请填写邮件标题','',5);
			if(!$body)show_msg('请填写邮件内容','',5);
			$param['cc'] = $this->input->get('em_cc');
			$param['bcc'] = $this->input->get('em_bcc');
			
			$not_list = array();
			foreach($re_id as $id)
			{
				$record = $this->db->table("tra_record")->field("*")->where("re_id='".$id."'")->getOne();
				if(!$record['re_email'])continue;
				$param['key_id'] = $id;
				$rs = $em_model->send($record['re_email'],$subject,$body,$param);
				if(!$rs)$not_list[] = $record['re_email'];
			}
			$msg = '';
			if($not_list)
			{
				$msg = '<div>以下邮件发送失败：</div>';
				foreach ($not_list as $em)
				{
					$msg .= '<div>'.$em.'</div>';
				}
				show_msg($msg,'',20);
			}
			show_msg('发送成功','',5);
		}
		
		$subject = '';
		$body = $this->getEmailContent(1);
		$this->output->set(array(
				'em_subject' => $subject,
				'em_body' => $body,
				're_id' => $re_id,
		));
		$this->output->display('Travel/sendEmail.html');
	}
	private function getEmailContent($tpl=1){
		ob_start();
		$this->output->display('EmailTpl/' . $tpl . '.html');
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}