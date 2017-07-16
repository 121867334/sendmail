<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Lang_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
	private $lang = array(
			'cn'=>array(
	            'action_illegal' => '非法操作',
				'not_login'=>'您未登录或者登录超时，请先登录',
				'checkcode_bad'=>'验证码错误',
				'account_or_pwd_bad'=>'账号/密码错误。',
				'action_success'=>'操作成功。',
				'action_failure'=>'操作失败，请重试。',
				'enter_use_name'=>'请填写职员姓名',
				'exists_no'=>'此工号已经存在，请填写其他工号。',
				'enter_old_pwd'=>'请输入旧密码',
				'enter_new_pwd'=>'新密码格式不正确',
				'enter_two_pwd_not'=>'两次输入新密码不一致',
				'enter_old_pwd_bad'=>'旧密码错误',
				'edit_use_pwd'=>'修改账户密码',
				'edit_info'=>'修改资料',
				'add_info'=>'添加资料',
				'enter_dep_name'=>'请填写部门名称',
				'enter_rol_name'=>'请填写角色名称',
				'enter_title'=>'请输入标题',
				'enter_name'=>'请输入名称',
				'exists_name'=>'名称已经存在',
				'protect_customer'=>'保护客户',
				'assign_customer'=>'分配客户',
				'protect_amount_bad'=>'保护配额不足',
				'protect_bad'=>'客户已经被保护',
				'protect_days_not'=>'天内不能再保护',
				'protect_cancel'=>'放弃保护客户',
				'bad_cus_name'=>'客户名称不正确',
				'bad_pro_name'=>'产品名称不正确',
				'bad_money'=>'金额不正确',
			),
			'hk'=>array(
			),
    );

    /**
     * 獲取對應的語言
     * @param string $name
     * @return string $value
     */
    public function get($name,$lang=null) {
    	if(!$lang)$lang = Cookie::get('lang');
    	if(!$lang)$lang = Fn::$config['lang'];
    	return $this->lang[$lang][$name];
    }
}