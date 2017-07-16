function checkFloat(str)
{
	return /^[\-]{0,1}\d+[\.]{0,1}\d+|\d$/g.test(str);
}
function checkInt(str)
{
	return /^\d+$/g.test(str);
}
function trim(str)
{
	return str.replace(/(^\s*)|(\s*$)/g,"");
}
function Lrim(str)
{
	return str.replace(/(^\s*)/g,"");
}
function Rrim(str)
{
	return str.replace(/(\s*$)/g,"");
}
function checkDate(str)
{
	return /^[1-2]\d{3}-(0[1-9]||1[0-2])-([0-2]\d||3[0-1])$/g.test(str);
}
function checkTime(str)
{
	return /^[1-2]\d{3}-(0[1-9]||1[0-2])-([0-2]\d||3[0-1]) ([0-1][0-9]||2[0-3]):([0-5][0-9]):([0-5][0-9])$/g.test(str);
}
function checkC(str)
{
	return /^[a-zA-Z_]+[a-zA-Z0-9_]*$/g.test(str);
}
function checkPhone(str)
{
	if(!str)return false;
	return /^(([0\+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/.test(str);
}
function checkDomainTel(str)
{
	if(!str)return false;
	return /^\+\d{1,3}\.\d{7,14}$/.test(str);
}
function checkMobile(str)
{
	if(!str)return false;
	return /^1[3|4|5|8][0-9]\d{4,8}$/.test(str);
}
function checkEmail(str)
{
	if(!str)return false;
	return /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/.test(str);
}
function checkNumber(str)
{
	if(!str)return false;
	return /^\d+$/.test(str);
}
function checkUserName(str)
{
	if(!str)return false;
	return /^[a-zA-Z0-9][a-zA-Z0-9_]{3,18}[a-zA-Z0-9]$/.test(str);
}
function checkPassword(str)
{
	if(!str)return false;
	return /^.{6,20}$/.test(str);
}
function checkDomainName(str)
{
	if(!str)return false;
	if(/[\u4e00-\u9fa5]+/.test(str) && str.length > 20)return false;
	return /^[A-Za-z0-9_\-\u4E00-\u9FA5]{1,255}$/.test(str);
}