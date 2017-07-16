<?php
 /* compiled by (FnPHP) at (2017-06-21 14:38:57) */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $this->data['web_name'];?></title>
<script type="text/javascript" src="<?php echo $this->data['web_path'];?>js/jquery-1.11.1.min.js"></script>
<style type="text/css">
body{background-color:#648ABB;margin: 0px auto;padding: 0px; font-family:"宋体", "新宋体"; font-size:12px;}
.main{width:443px; margin:0px auto; background:url(<?php echo $this->data['web_path'];?>style/default/images/login.jpg) no-repeat;width:443px; height:296px; }
#username{margin:0px 0px 0px 0px; width:196px; height:20px; line-height:20px; border:1px solid #B1C0D3; display:block;}
#pwd{margin:0px 0px 0px 0px; width:196px; height:20px; line-height:20px; border:1px solid #B1C0D3; display:block;}
#btn_submit{background:url(<?php echo $this->data['web_path'];?>style/default/images/btn_submit.gif);width:60px; height:25px; padding:0px; border:0px; margin:15px 0px 0px 0px;}
#btn_reset{background:url(<?php echo $this->data['web_path'];?>style/default/images/btn_reset.gif);width:60px; height:25px; padding:0px; border:0px; margin:0px 0px 0px 0px;}
.login{padding:0px; margin:0px; border:0px;}
.login_tb{border:0px;}
.login_tb td{border:0px; padding:0px; margin:0px;}
.login table td{word-break:break-all; word-wrap:break-word;}
.login .tips{font-size:14px; font-weight:bold;}
.login .txt{width:180px; height:18px; border:1px solid #999;}
</style>
<script type="text/javascript">
$(document).ready(function(e) {
	//login_cls.set();
	login_cls.changeCheckCode($('#check_code_img'));
	$('#username').focus();
});
var login_cls = {
	check:function(){
		var username = $.trim($('#username').val());
		var pwd = $('#pwd').val();
		var check_code = $.trim($('#check_code').val());
		if(username == '')
		{
			alert('请输入账号');
			$('#username').focus();
			return false;
		}
		if(pwd == '')
		{
			alert('请输入密码');
			$('#pwd').focus();
			return false;
		}
		if(check_code == '')
		{
			alert('请输入验证码');
			$('#check_code').focus();
			return false;
		}
		return true;
	},
	set:function()
	{
		var h = $('#login').height();
		var w = $('#login').width();
		h = ($(document).height() - h) / 2;
		w = ($(document).width() - w) / 2;
		h = Math.round(h / $(document).height() * 100) + '%';
		w = Math.round(w / $(document).width() * 100) + '%';
		document.getElementById('login').style.top = h;
		document.getElementById('login').style.left = w;
		//$('#login').css({'top':h,'left':w});
	},
	changeCheckCode:function(obj)
	{//验证码
		var url = 'index.php?c=Main&a=getCheckCode&ref='+Math.random();
		$(obj).attr('src',url);
	}
}
</script>
</head>

<body>
<div style="height:100px;"></div>
<div class="main">
<form action="index.php?c=Main&a=login" method="post" class="login" id="login">
	<table border="0" class="login_tb" cellpadding="0" cellspacing="0">
		<tr>
			<td height="100" align="left" valign="middle" colspan="3"><div style="color:#253A53; font-size:28px; font-weight:bold; padding:40px 0px 0px 30px;"><?php echo $this->data['web_name'];?></div></td>
		</tr>
		<tr>
			<td colspan="3" height="34"><div style="height:34px;"></div></td>
		</tr>
		<tr>
			<td width="185" height="34" valign="middle"><div style="width:185px; text-align:right; color:#284184;">用户名：</div></td>
			<td width="258" colspan="2">
				<div style="width:258px; overflow:hidden;">
				<input type="text" name="username" id="username" class="txt" />
				</div>
				<div style="height:10px;"></div>
			</td>
		</tr>
		<tr>
			<td width="185"><div style="width:185px; text-align:right; color:#284184;">密码：</div></td>
			<td width="258" colspan="2">
				<div style="width:258px; overflow:hidden;">
				<input type="password" name="pwd" id="pwd" class="txt" />
				</div>
				<div style="height:8px;"></div>
			</td>
		</tr>
		<tr>
			<td width="185"><div style="width:185px; text-align:right; color:#284184;">验证码：</div></td>
			<td width="100">
				<input type="text" class="check_code" name="check_code" id="check_code" style="width:80px;" />
			</td>
			<td width="158">
				<img src="index.php?c=Main&a=getCheckCode" alt="验证码" title="看不清楚？点击更改验证码。" id="check_code_img" onclick="login_cls.changeCheckCode($('#check_code_img'))" style="cursor:pointer;" />
			</td>
		</tr>
		<tr>
			<td colspan="3" align="right">
				<div style="padding-right:60px;">
				<input type="submit" value="" onClick="return login_cls.check()" class="btn" id="btn_submit" />
				<input type="reset" value="" class="btn" id="btn_reset" />
				<input type="hidden" name="act" id="act" value="login" />
				<input type="hidden" name="tk" id="tk" value="<?php echo $this->data['tk'];?>" />
				</div>
			</td>
		</tr>
	</table>
</form>
</div>
</body>
</html>