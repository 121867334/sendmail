<?php
class Ext_CardNo
{
    public $card_url = 'http://www.youdao.com/smartresult-xml/search.s?jsFlag=true&type=id&q=';
    public $cardno = '';
    
    public function __construct($cardno='') {
		$this->cardno = $cardno;
	}
    
	//检查身份证是否有效
    public function check($cardno='')
    {
        if($cardno)
        {
            $this->cardno = $cardno;
        }
        if(!$this->cardno)return false;
        $rs = $this->post();
        if(strpos($rs, $this->cardno)===false)
        {
            return false;
        }
        else
        {
            return true;
        }
        //return preg_match("/".$this->cardno."/i",$rs);
    }
    
    //获取身份证信息
    public function get($cardno='')
    {
        if($cardno)
        {
            $this->cardno = $cardno;
        }
        if(!$this->cardno)return false;
        $rs = $this->post();
        preg_match_all('/\{.+}|\[.+]/i', $rs, $rs);
        if(isset($rs[0][0]) && $rs[0][0])
        {
            $rs = $rs[0][0];
            $rs = str_replace("'",'"',$rs);
            $rs = json_decode($rs,true);
        }
        return $rs;
    }
    
    protected function post()
    {
        $rs = Ext_Network::openUrl($this->card_url.$this->cardno,'',array('code'=>'gbk'));
        return $rs;
    }
}
