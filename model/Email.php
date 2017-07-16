<?php
/**
 * 
 * 短信发送模型
 *
 */
class Email_Model extends Model{
	
    public function __construct(){
		parent::__construct();
		$this->setTable('tra_email','em_id');
	}
	
	/**
	 * 查找
	 * */
	public function search($where=array(),$field="*", $limit = '0, 10', $order = 'em_id', $by = 'DESC'){
		$rs = $this->db->table($this->table)
					->where($where)
					->field($field)
					->limit($limit)
					->order($order.' '.$by)
					->getAll();
		return $rs;
	}
	
	/**
	 * 获取总数
	 * */
	public function getTotal($where = array()){
		$rs = $this->db->table($this->table)
					->field("COUNT(em_id) AS num")
					->where($where)
					->getOne();
		return $rs['num'];
	}
	
	/*
	 * 根据ID获取
	 * */
	public function getById($em_id)
	{
		$rs = $this->db->table($this->table)->where("em_id=$em_id")->getOne();
		return $rs;
	}
	
	/**
	 * 添加
	 * @param $data:数据
	 */
	public function add($data){
		$this->db->table($this->table)->insert($data);
		return $this->db->insertId();
	}
	
	/*发送邮件
	 *to:接收人，格式：（字符串：多个用英文逗号隔开）；（数组：array('111@qq.com','222@qq.com')或者array(array('111@qq.com','路人甲'),array('222@qq.com','路人乙'))）。
	 *subject:标题
	 *body:内容
	 *param(数组)
	 *case 1 : array("host"=>"","user"=>"","pwd"=>"","is_smtp"=>1,"is_html"=>1,"from"=>"","from_name"=>"","attachment"=>array(),"cc"=>array("收件人Email","收件人姓名"),"bcc"=>array("收件人Email","收件人姓名"),"replyto"=>array("收件人Email","收件人姓名"),"alt_body"=>"");
	 *case 2 : array("host"=>"","user"=>"","pwd"=>"","from_name"=>"","attachment"=>"f:\gg.rar,e:\test.rar","cc"=>"a@163.com,b@huyi.cn","bcc"=>"a@163.com,b@huyi.cn","replyto"=>"a@163.com,b@huyi.cn","alt_body"=>"");
	*/
	public function send($to,$subject,$body,$param=array("is_smtp"=>true,"is_html"=>true))
	{
		if(!$to){return false;}
		if(!is_array($to))
		{
			$to = str_replace(';', ',', $to);
			$to = explode(",",$to);
		}

		if(!isset($param['host'])){$param['host'] = Fn::$config['smtp_server'];} // smtp服务器
		if(!isset($param['port'])){$param['port'] = (isset(Fn::$config['smtp_port'])?Fn::$config['smtp_port']:25);} // stmp端口

		if(!isset($param['user']) || !isset($param['pwd']) || !$param['user'] || !$param['pwd'])
		{
			$param['user'] = Fn::$config['smtp_username']; // smtp用户名
			$param['pwd'] = Fn::$config['smtp_password']; // stmp密码
		}
		$Ext_mail = new Ext_Mailer();
		$param['is_smtp'] = isset($param['is_smtp'])?$param['is_smtp']:true;
		if($param['is_smtp'])
		{
			$Ext_mail->IsSMTP(); // 使用SMTP方式发送
			$Ext_mail->SMTPAuth = true; // 启用SMTP验证功能
			$Ext_mail->Host = $param['host'];
			if($param['port'])$Ext_mail->Port = $param['port'];
			$Ext_mail->Username = $param['user'];
			$Ext_mail->Password = $param['pwd'];
		}
		$Ext_mail->From = isset($param['from'])?$param['from']:$param['user']; //邮件发送者email地址
		$from_name = isset($param['from_name'])?$param['from_name']:Fn::$config['web_name'];	//发送者姓名
		$from_name = $from_name?$from_name:$param['user'];	//发送者姓名
		$Ext_mail->FromName = $from_name;

		//收件人
		$db_to = array();

		foreach($to as $v)
		{
			if(is_array($v))
			{
				if(!trim($v[0]))continue;
				$Ext_mail->AddAddress(trim($v[0]),$v[1]);	//收件人地址,姓名
				$db_to[] = trim($v[0]);
			}
			else
			{
				if(!trim($v))continue;
				$Ext_mail->AddAddress(trim($v),trim($v));	//收件人地址
				$db_to[] = trim($v);
			}
		}

		//抄送
		$db_cc = array();
		if(isset($param['cc']) && $param['cc'])
		{
			if(!is_array($param['cc']))
			{
				$param['cc'] = str_replace(';', ',', $param['cc']);
				$param['cc'] = explode(",",$param['cc']);
			}
			foreach($param['cc'] as $v)
			{
				if(!$v)continue;
				if(is_array($v))
				{
					if(!trim($v[0]))continue;
					$Ext_mail->AddCC(trim($v[0]),$v[1]);//抄送地址，姓名
					$db_cc[] = trim($v[0]);
				}
				else
				{
					if(!trim($v))continue;
					$Ext_mail->AddCC(trim($v),trim($v));//抄送地址
					$db_cc[] = trim($v);
				}
			}
		}
		//密抄
		$db_bcc = array();
		if(isset($param['bcc']) && $param['bcc'])
		{
			if(!is_array($param['bcc']))
			{
				$param['bcc'] = str_replace(';', ',', $param['bcc']);
				$param['bcc'] = explode(",",$param['bcc']);
			}
			foreach($param['bcc'] as $v)
			{
				if(is_array($v))
				{
					if(!trim($v[0]))continue;
					$Ext_mail->AddBCC(trim($v[0]),$v[1]);	//密抄人地址，姓名
					$db_bcc[] = trim($v[0]);
				}
				else
				{
					if(!trim($v))continue;
					$Ext_mail->AddBCC(trim($v),trim($v));	//密送地址
					$db_bcc[] = trim($v);
				}
			}
		}
		//回复
		if(isset($param['replyto']) && $param['replyto'])
		{
			if(!is_array($param['replyto']))
			{
				$param['replyto'] = str_replace(';', ',', $param['replyto']);
				$param['replyto'] = explode(",",$param['replyto']);
			}
			foreach($param['replyto'] as $v)
			{
				if(is_array($v))
				{
					if(!trim($v[0]))continue;
					$Ext_mail->AddReplyTo(trim($v[0]), $v[1]);	//回复人地址，姓名
				}
				else
				{
					if(!trim($v))continue;
					$Ext_mail->AddReplyTo(trim($v),trim($v));	//回复地址
				}
			}
		}
		//附件
		$db_attach = array();
		//读取邮件内容的附件
		$db_body = $body;
		$pms = $this->replceBody($body);
		if($pms['em_body'])$body = $pms['em_body'];
		if(isset($pms['attachment']))
		{
			//$zip_obj = new Ext_PhpZip();
			foreach($pms['attachment'] as $v)
			{
				$Ext_mail->AddAttachment($v['path'],$v['name']);	// 添加附件
				//$Ext_mail->AddAttachment($v['path'],iconv('UTF-8', 'gbk', $v['name']));	// 添加附件
				$db_attach[] = $v['path'];
			}
		}
		if(isset($param['attachment']))
		{
			if(!is_array($param['attachment']))
			{
				$param['attachment'] = explode(",",$param['attachment']);
			}
			foreach($param['attachment'] as $v)
			{
				if(is_array($v))
				{
					$Ext_mail->AddAttachment($v['path'],$v['name']);	// 添加附件
					$db_attach[] = $v['path'];
				}
				else
				{
					$Ext_mail->AddAttachment($v);	// 添加附件
					$db_attach[] = $v;
				}
			}
		}
		//html附件
		if(isset($pms['html_attachment']) && $pms['html_attachment'])$param['html_attachment'] = $pms['html_attachment'];
		if(isset($param['html_attachment']) && is_array($param['html_attachment']))
		{
			foreach($param['html_attachment'] as $v)
			{
				$Ext_mail->AddEmbeddedImage($v['path'],$v['cid'],$v['name']);	// 添加附件
				$db_attach[] = $v['path'];
			}
		}
		$param['is_html'] = isset($param['is_html'])?$param['is_html']:true;
		$Ext_mail->IsHTML($param['is_html']); //是否使用HTML格式
		$Ext_mail->Subject = $subject; //邮件标题
		
		$Ext_mail->Body = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$body; //邮件内容
		if(isset($param['alt_body']))
		{
			$Ext_mail->AltBody = $param['alt_body']; //附加信息，可以省略
		}
		$rs = $Ext_mail->Send();
		if(!$rs && trim($Ext_mail->ErrorInfo) == 'SMTP Error: Data not accepted.')
		{
			$rs = $Ext_mail->Send();
		}
		//echo $Ext_mail->ErrorInfo."<br/>";
		if(!$param['db_not'])
		{
			$data = array();
			$data['em_to'] = implode(',',$db_to);
			$data['em_from'] = $Ext_mail->From;
			$data['em_cc'] = implode(',',$db_cc);
			$data['em_bcc'] = implode(',',$db_bcc);
			$data['em_subject'] = $subject;
			$data['em_body'] = addslashes($db_body);
			$data['em_attach'] = implode(',', $db_attach);
			$data['em_time'] = date('Y-m-d H:i:s');
			$data['em_result'] = addslashes($Ext_mail->ErrorInfo);
			$data['em_status'] = $rs?1:0;
			$data['use_id'] = $param['use_id'];
			$data['key_id'] = $param['key_id'];
			$data['key_table'] = $param['key_table'];
			$this->add($data);
		}
		return $rs?true:false;
	}
	private function replceBody($body)
	{
		$param = array();
		//替换邮件内容
		$body = str_replace('<?xml:namespace prefix="o"><o:p></o:p></?xml:namespace>', '', $body);
		$body = str_replace('[自行輸入]', '', $body);
		$body = str_replace('(DATE)', '', $body);
		$body = str_ireplace('< table', '<table', $body);
		$body = str_ireplace('< tr', '<tr', $body);
		$body = str_ireplace('< th', '<th', $body);
		$body = str_ireplace('< td', '<td', $body);
		preg_match_all('/<p style=".+?">[\w\W]*?<img src="http:\/\/'.$_SERVER['HTTP_HOST'].'\/js\/ueditor\/dialogs\/attachment\/fileTypeImages\/icon_.+?\..{3,4}"\/><a href="(.+?)">(.*?)<\/a>[\w\W]*?<\/p>/i', $body,$rs);
		if($rs[1])
		{
			foreach ($rs[1] as $k=>$v)
			{
				$v = str_replace('http://'.$_SERVER['HTTP_HOST'].'/', "", $v);
				$param['attachment'][] = array('path'=>$v,'name'=>$rs[2][$k]);
			}
		}
		$body = preg_replace('/<p style=".+?">[\w\W]*?<img src="http:\/\/'.$_SERVER['HTTP_HOST'].'\/js\/ueditor\/dialogs\/attachment\/fileTypeImages\/icon_.+?\..{3,4}"\/><a href="(.+?)">(.*?)<\/a>[\w\W]*?<\/p>/i', '',$body);
		preg_match_all('/<img .*?>/i', $body,$rs);
		if($rs[0])
		{
			$html_attachment_list = array();
			$i = 0;
			foreach ($rs[0] as $v)
			{
				preg_match_all('/src="(.+?)"/i', $v,$img);
				$img[1][0] = str_replace('http://'.$_SERVER['HTTP_HOST'].'/js/ueditor/php/../../../attach', "attach", $img[1][0]);
				$img[1][0] = str_replace('http://'.$_SERVER['HTTP_HOST'].'/', "", $img[1][0]);
				if(strtolower(substr($img[1][0],0,7)) == 'http://')continue;
				if(substr($img[1][0],0,1) == '/')
				{
					if(substr($img[1][0],0,5) != '/data')
					{
						$img[1][0] = '..'.$img[1][0];
					}
				}
				$img[1][0] = str_replace('//', '/', $img[1][0]);
				if(!file_exists($img[1][0]))continue;
				$old_img = $v;
				if($html_attachment_list[$img[1][0]])
				{
					$cid = $html_attachment_list[$img[1][0]];
				}
				else
				{
					$i ++;
					$cid = 'img_'.$i;
					$param['html_attachment'][] = array('path'=>$img[1][0],'cid'=>$cid);
					$html_attachment_list[$img[1][0]] = $cid;
				}
				$v = preg_replace('/src="(.+?)"/i', "src=\"cid:".$cid."\" osrc=\"\\1\"", $v);
				$body = str_replace($old_img, $v, $body);
			}
		}
		$param['em_body'] = $body;
		return $param;
	}
}