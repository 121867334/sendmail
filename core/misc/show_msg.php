<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>提示消息</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php if($refresh >= 0):?>
<meta http-equiv='Refresh' content='<?php echo $refresh?>;URL=<?php echo $url?>'>
<?php endif?>
<style>
body {font-size:12px; font-family:'微软雅黑', '黑体', Verdana;color:#333333; background-color: transparent;}
.wrap {width:300px;height:150px;border:1px solid #ddd;margin:20px auto;}
.tit{font-size:14px;font-weight:bold;background:#f5f5f5;padding:5px 10px;height:20px;line-height:20px;border-bottom:1px solid #ddd;}
.txt {padding:10px 15px;}
.txt .msg{}
</style>
</head>
<body>

<div class="wrap error_<?php echo $error?>">
	<div class="tit">信息提示</div>
	<div class="txt">
		<p class="msg">
		<?php echo $errorMsg?>
		<p>
			<?php if($backUrl):?>
    		[ <a href="<?php echo $backUrl?>" style="color:#069;">返回</a> ]
    		<?php endif?>
    		[ <a href="<?php echo $url?>" style="color:#069;"><span id="limit_time">点此直接跳转</span></a> ]
    	</p>
    </div>
</div>

	
<?php if($refresh >= 0):?>
<script language='javascript' type='text/javascript'>
	var wait = <?php echo $refresh?>;
	var url = '<?php echo $url?>';
	function showWaitTime() {
		if (wait <= 0) {
			document.location.href = url;	
			return;
		}
		document.getElementById('limit_time').innerHTML = wait + '秒后自动跳转';
		wait -= 1;
		setTimeout(showWaitTime, 1000);
	}
	showWaitTime();
</script>
<?php endif?>	
</body>
</html>