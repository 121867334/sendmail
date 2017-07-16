function chooseAll(obj,cls)
{
	if($(obj).is(":checked"))
	{
		$('.'+cls).prop('checked',true);
	}
	else
	{
		$('.'+cls).prop('checked',false);
	}
}
function jumpPage(obj)
{
	var p = $('#jump_page').attr('href');
	var v = $('#jump_page').val();
	p += '&p='+v;
	if(obj)
	{
		ajaxPage(p,obj);
	}
	else
	{
		location.href = p;
	}
}
function showMyModalDialog(url,w,h)
{
	if(!w)w = screen.width;
	if(!h)h = screen.height;
	window.open(url,'newwindow','width='+w+',height='+h+',status=no,toolbar=no,menubar=no,location=no,scrollbars=yes,z-look=yes,location=no');
}