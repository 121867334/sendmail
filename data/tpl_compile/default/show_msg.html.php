<?php
 /* compiled by (FnPHP) at (2017-06-21 17:43:09) */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $this->data['web_name'];?></title>
<link href="<?php echo $this->data['web_path'];?>style/default/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this->data['web_path'];
 echo $this->data['web_path'];?>js/jquery-1.11.1.min.js"></script>
<?php if($this->data['refresh'] >= 0){?>
<meta http-equiv='Refresh' content='<?php echo $this->data['refresh'];?>;URL=<?php echo $this->data['url'];?>'>
<?php }
?>
</head>

<body>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" valign="middle"><form action="<?php echo url();?>" method="post" class="login">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td height="40" valign="top" class="tips">信息提示
							<div class="line"></div></td>
					</tr>
					<tr>
						<td height="30" align="center"><?php echo $this->data['errorMsg'];?></td>
					</tr>
					<tr>
						<td height="30" align="center">
							<!--<?php if($this->data['backUrl']){?>
							[<a href="<?php echo $this->data['backUrl'];?>" class="blue">返回</a>] &nbsp; 
							<?php }
?>-->
							[<a href="<?php echo $this->data['url'];?>" class="blue"><span id="limit_time">点此直接跳转</span></a>] </td>
					</tr>
				</table>
			</form></td>
	</tr>
</table>
<?php if($this->data['refresh'] >= 0){?> 
<script language='javascript' type='text/javascript'>
	var wait = '<?php echo $this->data['refresh'];?>';
	var url = '<?php echo $this->data['url'];?>';
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
<?php }
?>
</body>
</html>
