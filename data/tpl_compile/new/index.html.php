<?php
 /* compiled by (FnPHP) at (2017-07-16 19:01:00) */
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Login PHP</title>
	<link rel="stylesheet" href="/style/default/style(2).css" />
	<!--<link href='http://fonts.googleapis.com/css?family=Oleo+Script' rel='stylesheet' type='text/css'>-->
	<script type="text/javascript" src="/js/jquery-1.7.min.js"></script>
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
	<div class="lg-container">
		<h1><?php echo $this->data['web_name'];?></h1>
		<form action="index.php?c=Main&a=login" id="lg-form" name="lg-form" method="post">
			
			<div>
				<label for="username">Username:</label>
				<input type="text" name="username" id="username" placeholder="username"/>
			</div>
			
			<div>
				<label for="password">Password:</label>
				<input type="password" name="pwd" id="pwd" placeholder="password" />
			</div>
			
			<div>
				<label for="check_code">VerificationCode:</div>
				<input type="text" class="check_code" name="check_code" id="check_code" style="width:80px;" />
				<img src="index.php?c=Main&a=getCheckCode" alt="Verification Code" title="看不清楚？点击更改验证码。" id="check_code_img" onclick="login_cls.changeCheckCode($('#check_code_img'))" style="cursor:pointer;" />
				<button type="submit" id="login">Login</button>
			</div>
			<input type="hidden" name="act" id="act" value="login" />
			<input type="hidden" name="tk" id="tk" value="<?php echo $this->data['tk'];?>" />
		</form>
		<div id="message"></div>
	</div>
</body>
</html>