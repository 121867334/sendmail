<?php
 /* compiled by (FnPHP) at (2017-07-16 18:54:39) */

 $this->display('header.html');?>
<script type="text/javascript" src="<?php echo $this->data['web_path'];?>js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
function checkFrm(obj)
{
	var re_name = $('#re_name').val();
	if(re_name == '')
	{
		alert('请填写名称');
		$('#re_name').focus();
		return false;
	}
	$('#add_frm').submit();
	$(obj).attr('disabled',true);
}
</script>
<form action="index.php?c=Travel&a=add" method="post" id="add_frm">
	<table width="100%" cellpadding="3" cellspacing="0" class="table">
		<tr>
			<td width="10%" align="left" nowrap="nowrap">名称：</td>
			<td><input type="text" name="data[re_name]" id="re_name" class="txt_1" value="<?php echo $this->data['record']['re_name'];?>" />
			<span class="red">*</span></td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">联系人：</td>
			<td><input type="text" name="data[re_contact]" id="re_contact" class="txt_1" value="<?php echo $this->data['record']['re_contact'];?>" />
			</td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">电话：</td>
			<td><input type="text" name="data[re_tel]" id="re_tel" class="txt_1" value="<?php echo $this->data['record']['re_tel'];?>" />
			</td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">传真：</td>
			<td><input type="text" name="data[re_fax]" id="re_fax" class="txt_1" value="<?php echo $this->data['record']['re_fax'];?>" />
			</td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">Email：</td>
			<td><input type="text" name="data[re_email]" id="re_email" class="txt_1" value="<?php echo $this->data['record']['re_email'];?>" />
			</td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">地址：</td>
			<td><input type="text" name="data[re_addr]" id="re_addr" class="txt_1" value="<?php echo $this->data['record']['re_addr'];?>" />
			</td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">邮编：</td>
			<td><input type="text" name="data[re_postcode]" id="re_postcode" class="txt_1" value="<?php echo $this->data['record']['re_postcode'];?>" />
			</td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">日期：</td>
			<td><input type="text" name="data[re_date]" id="re_date" class="Wdate" value="<?php echo $this->data['record']['re_date'];?>" onclick="WdatePicker()" /></td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">状态：</td>
			<td><input type="radio" name="data[re_status]" value="1" class="re_status" <?php if($this->data['record']['re_status']==1 || !$this->data['record']['re_id']){?>checked<?php }
?> />有效
				<input type="radio" name="data[re_status]" value="0" class="re_status" <?php if(!$this->data['record']['re_status'] && $this->data['record']['re_id']){?>checked<?php }
?> />无效
			</td>
		</tr>
		<tr>
			<td width="10%" align="left" nowrap="nowrap">备注：</td>
			<td><textarea name="data[re_remark]" id="re_remark" class="txtarea_1"><?php echo $this->data['record']['re_remark'];?></textarea></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="re_id" id="re_id" value="<?php echo $this->data['record']['re_id'];?>" />
				<input type="hidden" name="act" id="act" value="save" />
				<input type="button" value="提交" class="btn" onclick="checkFrm(this)" />
				<input type="reset" value="重置" class="btn" />
			</td>
		</tr>
	</table>
</form>
<?php $this->display('footer.html');?>