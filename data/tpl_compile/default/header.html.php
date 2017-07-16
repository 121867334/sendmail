<?php
 /* compiled by (FnPHP) at (2017-06-21 17:44:00) */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $this->data['web_name'];?></title>
<link href="<?php echo $this->data['web_path'];?>style/default/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this->data['web_path'];?>js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->data['web_path'];?>js/function_common.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	if($('.container_nav').length <= 0)
	{
		$('.container').css('padding-top','10px');
	}
});
</script>
</head>

<body>
<?php if($this->data['c_name'] && !$this->data['not_nav']){?><div class="container_nav noprint"><a href="javascript:void(0);" onClick="history.go(-1);" class="container_back">返回</a><?php echo $this->data['c_name'];?> >> <?php echo $this->data['a_name'];?></div><?php }
?>
<div class="container">