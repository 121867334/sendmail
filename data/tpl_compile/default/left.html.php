<?php
 /* compiled by (FnPHP) at (2017-07-16 18:54:28) */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $this->data['web_name'];?></title>
<link href="<?php echo $this->data['web_path'];?>style/default/style_2.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this->data['web_path'];?>js/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$(".nav_left_a").click(function(){
		$('.nav_left_ab').hide();
		var src = '<?php echo $this->data['web_path'];?>style/default/images/menu_icon_r.png?id='+Math.random();
		$('.nav_left_a img').attr('src',src);
		$(this).parent().find('.nav_left_ab').show();
		src = '<?php echo $this->data['web_path'];?>style/default/images/menu_icon_d.png?id='+Math.random();
		$(this).find('img').attr('src',src);
	});
	$('.nav_left_a:first').click();
	$('.left_nav').height($(document).height());
});
</script>
<style type="text/css">
body{background-color:#CDDCEF;}
</style>
</head>
<body style="overflow-x:hidden;" onselect="return false;">
<div class="left_nav">
	<div style="height:30px;"><img src="<?php echo $this->data['web_path'];?>style/default/images/left_tips.gif" /></div>
	<div class="menu_list">
		<p class="nav_left_a"><img src="<?php echo $this->data['web_path'];?>style/default/images/menu_icon_r.png" />资料管理</p>
		<div class="nav_left_ab">
			<ul>
				<li><a href="index.php?c=Travel&a=add" target="fn_mainFrame">• 添加资料</a></li>
				<li><a href="index.php?c=Travel&a=index" target="fn_mainFrame">• 资料管理</a></li>
			</ul>
		</div>
	</div>
	<div class="menu_list">
		<p class="nav_left_a"><img src="<?php echo $this->data['web_path'];?>style/default/images/menu_icon_r.png" />系统设置</p>
		<div class="nav_left_ab">
			<ul>
				<li><a href="index.php?c=Config&a=update" target="fn_mainFrame">• 系统配置</a></li>
				<li><a href="index.php?c=Main&a=logout" target="_top">• 退出登录</a></li>
			</ul>
		</div>
	</div>
</div>
</body>
</html>