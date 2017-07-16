// JavaScript Document
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