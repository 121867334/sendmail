<?php
/**
 * 上传文件
 * @author devil
 * @time 2013-6-21
 * @version 1.0
 */
class Upload_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();		
	}
	
	public function index()
	{
		$rs = array('error'=>'-1','errorMsg'=>'test非法操作');
		$use_id = $this->input->getIntval('use_id');
		$time = $this->input->getIntval('time');
		$token = $this->input->getTrim('token');
		$tb = $this->input->getTrim('tb');
		$tk = $this->input->getTrim('tk');
		$knet_status = $this->input->getTrim('knet_status');
		if(Ext_String::passHash(Fn::$config['encrypt_key'].$time.$use_id.$tb) != $token)
		{
			exit(json_encode($rs));
		}
		
		$modAttach = load_model('Attach');
		$file = $modAttach->makeAttachName();
		$path = $modAttach->getAttachPath($tb.'/'.$use_id.'/'.date('Ym').'/'.$file);
		$rs = Ext_Upload::save('Filedata', $path);
		if (!$rs['error']) {
			$data = array();
			$data['use_id'] = $use_id;
			$data['att_table'] = Fn::$config['db_table_prefix'].$tb;
			$data['att_key'] = $tk;
			$data['att_path'] = Fn::$config['upload_path'].'/'.$tb.'/'.$use_id.'/'.date('Ym').'/'.$file.'.'.$rs['ext'];
			$data['att_type'] = $rs['type'];
			$data['att_ext'] = $rs['ext'];
			$data['att_size'] = $rs['size'];
			$data['att_name'] = $rs['name'];
			$data['add_time'] = date('Y-m-d H:i:s');
			$rs['att_id'] = load_model('Attach')->add($data);
			if($knet_status && $rs['att_id'])
			{//调用北龙接口提交数据
				
			}
			$rs['att_path'] = $data['att_path'];
			exit(json_encode($rs));
		} else {
			exit(json_encode($rs));
		}
	}
}