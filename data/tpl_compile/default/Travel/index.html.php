<?php
 /* compiled by (FnPHP) at (2017-07-16 18:58:36) */

 $this->display('header.html');?>
<script type="text/javascript" src="<?php echo $this->data['web_path'];?>js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
function sendEmail(obj)
{
	if($('.id_cls:checked').length <= 0)
	{
		alert('请选择要操作的记录');
		return false;
	}
	$('#opt_frm').submit();
	$(':button').attr('disabled',true);
}
</script>
<form action="" method="get" id="search_frm">
	<fieldset class="search">
		<legend>搜索</legend>
		<input type="hidden" name="c" id="c" value="Travel" />
		<input type="hidden" name="a" id="a" value="index" />
		名称：<input type="text" name="re_name" id="re_name" class="txt_1" value="<?php echo $this->data['re_name'];?>" />&nbsp;
		<input type="submit" value="搜索" />&nbsp;
		<input type="button" value="发邮件" onclick="sendEmail(this)" />
	</fieldset>
</form>
<form action="index.php?c=Travel&a=sendEmail" method="post" id="opt_frm">
<table width="100%" cellpadding="3" cellspacing="0" class="table">
	<tr>
		<th width="10" nowrap="nowrap"><input type="checkbox" onclick="chooseAll(this,'id_cls')" /></th>
		<th align="left">名称</th>
		<th align="left">日期</th>
		<th align="left">邮箱</th>
		<th align="left">状态</th>
		<th align="left">操作</th>
	</tr>
	<?php if($this->data['list']){
 foreach($this->data['list'] as $this->data['val']){?>
	<tr id="tr_<?php echo $this->data['val']['re_id'];?>">
		<td><input type="checkbox" name="re_id[]" value="<?php echo $this->data['val']['re_id'];?>" class="id_cls" /></td>
		<td align="left"><?php echo $this->data['val']['re_name'];?></td>
		<td align="left"><?php echo $this->data['val']['re_date'];?></td>
		<td align="left"><?php echo $this->data['val']['re_email'];?></td>
		<td align="left"><?php if($this->data['val']['re_status']){?>有效<?php } else{?>无效<?php }
?></td>
		<td align="left"><a href="index.php?c=Travel&a=add&re_id=<?php echo $this->data['val']['re_id'];?>">编辑</a></td>
	</tr>
	<?php }

 }
?>
</table>
</form>
<div class="page"><span>共&nbsp;<?php echo $this->data['totalNum'];?>&nbsp;条记录，当前&nbsp;<?php echo $this->data['page'];?>/<?php echo $this->data['totalPage'];?></span><?php echo $this->data['pageHtml'];?></div>
<?php $this->display('footer.html');?>