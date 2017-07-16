<?php
 /* compiled by (FnPHP) at (2017-07-16 18:54:28) */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $this->data['web_name'];?></title>
<style type="text/css">
body {margin: 0;padding: 0;cursor: E-resize; background:url('<?php echo $this->data['web_path'];?>style/default/images/splitter_bg.gif');}
</style>
<script type="text/javascript" src="<?php echo $this->data['web_path'];?>js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" language="JavaScript">
<!--
var pic = new Image();
pic.src="<?php echo $this->data['web_path'];?>style/default/images/splitter_r.gif";

function toggleMenu()
{
  frmBody = parent.document.getElementById('fn_bodyFrame');
  imgArrow = document.getElementById('img');

  if (frmBody.cols == "0, 7, *")
  {
    frmBody.cols="180, 7, *";
    imgArrow.src = "<?php echo $this->data['web_path'];?>style/default/images/splitter_l.gif";
  }
  else
  {
    frmBody.cols="0, 7, *";
    imgArrow.src = "<?php echo $this->data['web_path'];?>style/default/images/splitter_r.gif";
  }
}

var orgX = 0;
document.onmousedown = function(e)
{
  var evt = Utils.fixEvent(e);
  orgX = evt.clientX;

  if (Browser.isIE) document.getElementById('tbl').setCapture();
}

document.onmouseup = function(e)
{
  var evt = Utils.fixEvent(e);

  frmBody = parent.document.getElementById('fn_bodyFrame');
  frmWidth = frmBody.cols.substr(0, frmBody.cols.indexOf(','));
  frmWidth = (parseInt(frmWidth) + (evt.clientX - orgX));

  frmBody.cols = frmWidth + ", 7, *";

  if (Browser.isIE) document.releaseCapture();
}

var Browser = new Object();

Browser.isMozilla = (typeof document.implementation != 'undefined') && (typeof document.implementation.createDocument != 'undefined') && (typeof HTMLDocument != 'undefined');
Browser.isIE = window.ActiveXObject ? true : false;
Browser.isFirefox = (navigator.userAgent.toLowerCase().indexOf("firefox") != - 1);
Browser.isSafari = (navigator.userAgent.toLowerCase().indexOf("safari") != - 1);
Browser.isOpera = (navigator.userAgent.toLowerCase().indexOf("opera") != - 1);

var Utils = new Object();

Utils.fixEvent = function(e)
{
  var evt = (typeof e == "undefined") ? window.event : e;
  return evt;
}

$(document).ready(function(e) {
	$('#tbl').height($(window).height());
});
//-->
</script>
</head>
<body onselect="return false;">
<table width="7" cellspacing="0" cellpadding="0" id="tbl">
  <tr><td align="center" width="7"><a href="javascript:toggleMenu();"><img src="<?php echo $this->data['web_path'];?>style/default/images/splitter_l.gif" id="img" border="0" /></a></td></tr>
</table>
</body>
</html>