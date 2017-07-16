<?php
 /* compiled by (FnPHP) at (2017-06-21 15:45:35) */

 $this->display('header.html');?>
<script type="text/javascript" src="<?php echo $this->data['web_url'];?>js/inv_editor/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo $this->data['web_url'];?>js/inv_editor/ueditor.all.js"></script>
<script type="text/javascript" src="<?php echo $this->data['web_url'];?>js/inv_editor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript">
var ue = null;
$(document).ready(function(e) {
	var ue = UE.getEditor('em_body',{initialFrameHeight:300});//ueditor
});
function checkFrm(obj)
{
	var em_subject = $.trim($('#em_subject').val());
	var em_body = $.trim($('#em_body').val());
	if(em_subject == '')
	{
		alert('请填写标题');
		$('#em_subject').focus();
		return false;
	}
	if(em_body == '')
	{
		alert('请填写内容');
		$('#em_body').focus();
		return false;
	}
	$('#email_frm').submit();
	$(obj).remove();
}
</script>
<form action="index.php?c=Travel&a=sendEmail" method="post" id="email_frm">
<?php foreach($this->data['re_id'] as $this->data['v']){?>
<input type="hidden" name="re_id[]" class="id_cls" value="<?php echo $this->data['v'];?>" />
<?php }
?>
<table width="100%" cellpadding="3" cellspacing="0" class="table">
	<tr>
		<th width="10%" nowrap="nowrap">抄送：</th>
		<td>
			<input type="text" name="em_cc" id="em_cc" style="width:90%" value="<?php echo $this->data['em_cc'];?>" /><br/>多个邮箱请用英文逗号隔开
		</td>
	</tr>
	<tr <?php if(!$this->data['is_admin']){?>style="display:none;"<?php }
?>>
		<th width="10%" nowrap="nowrap">暗抄：</th>
		<td>
			<input type="text" name="em_bcc" id="em_bcc" style="width:90%" value="<?php echo $this->data['em_bcc'];?>" /><br/>多个邮箱请用英文逗号隔开
		</td>
	</tr>
	<tr>
		<th width="10%" nowrap="nowrap">标题：</th>
		<td>
			<input type="text" name="em_subject" id="em_subject" style="width:90%" value="<?php echo addslashes($this->data['em_subject']);;?>" />
		</td>
	</tr>
	<tr>
		<th width="10%" valign="top" nowrap="nowrap">内容：</th>
		<td><textarea name="em_body" id="em_body">
		<?php echo $this->data['em_body'];?>
		</textarea></td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="hidden" name="back_url" id="back_url" value="<?php echo $this->data['back_url'];?>" />
			<input type="hidden" name="act" id="act" value="save" />
			<input type="button" value="发送" class="btn_css" onclick="checkFrm(this)" />
		</td>
	</tr>
</table>
</form>
<?php $this->display('footer.html');?>