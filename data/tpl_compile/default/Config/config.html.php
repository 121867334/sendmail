<?php
 /* compiled by (FnPHP) at (2017-06-21 17:44:00) */
?>
﻿<?php $this->display('header.html');?>
<table width="98%" border="0" cellpadding="4" cellspacing="1" class="table">
	<form action="?c=Config&a=update"  method="post" id="myform">
		<tr>
			<td height="30" colspan="2"><strong>邮件服务器</strong></td>
		</tr>
		<tr>
			<td width="10%">服务器地址</td>
			<td><input type="text" name="data[em_host]" value="<?php echo $this->data['config']['em_host'];?>" ></td>
		</tr>
		<tr>
			<td>端口</td>
			<td><input type="text" name="data[em_port]" value="<?php echo $this->data['config']['em_port'];?>" ></td>
		</tr>
		<tr>
			<td>账号</td>
			<td><input type="text" name="data[em_user]" value="<?php echo $this->data['config']['em_user'];?>" ></td>
		</tr>
		<tr>
			<td>密码</td>
			<td><input type="password" name="data[em_pwd]" value="<?php echo $this->data['config']['em_pwd'];?>" ></td>
		</tr>
		<tr>
			<td>发送人名称</td>
			<td><input type="text" name="data[from_name]" value="<?php echo $this->data['config']['from_name'];?>" ></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="act" value="save" />
				<input type="submit" value="提交" />
			</td>
		</tr>
	</form>
</table>
<?php $this->display('footer.html');?>