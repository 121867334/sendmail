<?php
/**
 * 系统配置
 * @author devil
 * @time 2013-6-21
 * @version 1.0
 */
class Config_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$lang_model = load_model('Lang');
		$type = $this->input->get('type');
		if($type && $type != 'config')
		{
			$this->set();
			exit();
		}
		
		if (!$type) $type = 'config';
		$modConfig = load_model('Config');
		if (check_submit()) {
			$data = $this->input->get('con');
			$modConfig->setConfig($data);
			$modConfig->clearFileCache();
			show_msg($lang_model->get('action_success'), "?c=Config&type=$type",3);
		}
		if ('config' == $type) {
			$skinList = $modConfig->getSkinList();
			$this->output->set('skinList', $skinList);
		}
		$this->output->set(Fn::$config);
		$this->output->set('type',$type);
		$this->output->display("Config/".$type.".html");
	}
	public function set() {
		$lang_model = load_model('Lang');
		$type = $this->input->get('type');
		if (check_submit()) {
			$data = $this->input->get('con');
			write_config(Fn::$config['data_path'].'web-'.$type.'.php', $data);
			show_msg($lang_model->get('action_success'),'?c=Config&type='.$type,3);
		}
		$config = load_model('Config')->getConfig($type);
		$this->output->set($config);
		$this->output->set('type',$type);
		$this->output->display("Config/".$type.".html");
	}
	public function update()
	{
		$act = $this->input->getTrim('act');
		if($act == 'save')
		{
			$data = $this->input->get('data');
			$this->db->table("tra_config")->where("con_id=1")->update($data);
		}
		$config = $this->db->table("tra_config")->field("*")->getOne();
		$this->output->set('config',$config);
		$this->output->display("Config/config.html");
	}
}