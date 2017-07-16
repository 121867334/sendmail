<?php
/**
 * 数据分页类管理
 */
class Ext_Page {
	/**
	 * @var mixed URL地址
	 */
	private $_url;	
	/**
	 * @var mixed 总记录数
	 */
	private $_totalNum;	
	/**
	 * @var mixed 当前页码
	 */
	private $_curPage = 1;	
	/**
	 * @var mixed 每页显示多少条记录
	 */
	private $_sizeNum = 10;	
	/**
	 * @var mixed 显示多少页
	 */
	private $_lineNum = 9;	
	/**
	 * @var mixed 上一页 &lt; Prev
	 */
	public static $prevText = '上一页';	
	/**
	 * @var mixed 下一页 Next &gt;
	 */
	public static $nextText = '下一页';	
	/**
	 * @var mixed 开始记录
	 */
	private $_start;	
	/**
	 * @var mixed 结束记录
	 */
	private $_end;	
	/**
	 * @var mixed 总页数
	 */
	private $_totalPage;	
	/**
	 * @var mixed LIMIT参数
	 */
	private $_limit;	
	/**
	 * @var mixed 分页HTML
	 */
	private $_html;	
	/**
	 * @var mixed 地址生成器
	 */
	private $_makeUrlFun = null;
	/**
	 * @var mixed 回调参数
	 */
	private $_callArgs = array();
	private $ajax = 0;
	private $ajaxObj = '';
	
	/**
	 * 构造方法, 初始化分页管理
	 * @param mixed 
	 * @return void
	 */
	public function __construct($url, $totalNum, $curPage = 1, $sizeNum = 10, $ajax=0, $ajaxObj='', $lineNum = 9) {
		
		$this->_url = $url;
		if (is_array($url)) {
			$this->_makeUrlFun = array_splice($url, 0, 2);
			$this->_callArgs = $url;
		} else {
			$this->_url = str_replace('%40', '@', $this->_url);	
		}
		$this->_totalNum = $totalNum;
		$this->_curPage = $curPage;
		$this->_sizeNum = $sizeNum;
		$this->_lineNum = $lineNum;
		if ($this->_totalNum < 1) {
			$this->_totalNum = 1;
		}
		if ($this->_sizeNum < 1) {
			$this->_sizeNum = 1;	
		}
		if ($this->_curPage < 1) {
			$this->_curPage = 1;	
		}
		$this->ajax = $ajax;
		$this->ajaxObj = $ajaxObj;
		$this->make();
	}
	
	/**
	 * 生成分页数据
	 * @param mixed 
	 * @return void
	 */
	public function make() {
		$ajax = $this->ajax;
		$ajaxObj = $this->ajaxObj;
		$this->_totalPage = ceil($this->_totalNum / $this->_sizeNum);	
		if ($this->_curPage > $this->_totalPage) {
			$this->_curPage = $this->_totalPage;
		}				
		$this->_start = ($this->_curPage - 1) * $this->_sizeNum;	
		$tmpArr = array();
		$tmpArr[] = "<span>共&nbsp;".$this->_totalNum."&nbsp;条记录，当前&nbsp;".$this->_curPage."/".$this->_totalPage."</span>";
		
		$prevPage = $this->_curPage - 1;
		if ($prevPage < 1) {
			$tmpArr[] = "<span class=\"prev\">".self::$prevText."</span>";
		} else {
			$tmpArr[] = $this->_makeUrl($prevPage, self::$prevText, $ajax, $ajaxObj, 'prev');
		}
		$k = floor( $this->_lineNum / 2 );
		$i = $this->_curPage - $k;
		$j = $this->_curPage + $k;
		if ($i < 3) {
			$i = 1;
			if ($i  + $this->_lineNum > $this->_totalPage) {
				$j = $this->_totalPage;
			} else {
				$j = $i - 1 + $this->_lineNum;	
			}
		}
		if ($j > $this->_totalPage-2) {
			$j = $this->_totalPage;
			if ($j - $this->_lineNum < 1) {
				$i = 1;
			} else {
				$i = $j - $this->_lineNum + 1;
			}
		}

		if ($i > 2) {
			$tmpArr[] = $this->_makeUrl(1, 1, $ajax, $ajaxObj) . '<font>...</font>';
		}else if($i==2){
			$tmpArr[] = $this->_makeUrl(1, 1, $ajax, $ajaxObj);
		}
		for (; $i <= $j; $i++) {
			if ($i == $this->_curPage) {
				$tmpArr[] = "<strong>$i</strong>";
			} else {
				$tmpArr[] = $this->_makeUrl($i, $i, $ajax, $ajaxObj);
			}
		}	
		if ($j < $this->_totalPage-1) {
			$tmpArr[] =  '<font>...</font>' . $this->_makeUrl($this->_totalPage, $this->_totalPage, $ajax, $ajaxObj);	
		}
		$nextPage = $this->_curPage + 1;
		if ($nextPage > $this->_totalPage) {
			$tmpArr[] = "<span class=\"next\">".self::$nextText."</span>";	
		} else {
			$tmpArr[] = $this->_makeUrl($nextPage, self::$nextText, $ajax, $ajaxObj, 'next');	
		}
		$this->_limit = $this->_start . ', ' . $this->_sizeNum;
		$this->_end = $this->_start + $this->_sizeNum;
		if ($this->_end > $this->_totalNum) {
			$this->_end = $this->_totalNum;	
		}
		
		$tmpArr[] = "<span style=\"padding:0px 10px;display:inline-block\">跳到<input type=\"text\" name=\"jump_page\" id=\"jump_page\" style=\"width:40px;\" href=\"".$this->_getUrl(1)."\">页";
		$tmpArr[] = "<input type=\"button\" value=\"Go\" onclick=\"jumpPage('".$ajaxObj."')\"></span>";
		$this->_html = implode("", $tmpArr);
	}
	
	/**
	 * 生成URL地址
	 * @param mixed 
	 * @return void
	 */
	private function _makeUrl($p, $text, $ajax = 0, $ajaxObj = '', $class = '') {
		if ($this->_makeUrlFun) {
			$args = $this->_callArgs;
			$args[] = $p;
			$url = call_user_func_array($this->_makeUrlFun, $args);
		} else {
			if(1==$p){
				$url = str_replace(Fn::$config['url_delimiter'].'@', '', $this->_url);
			}else{
				$url = str_replace('@', $p, $this->_url);
			}
		}
		if(!$ajax)
		{
			$str = "<a href=\"$url\"".($class?" class=\"$class\"":"")." title=\"第".$p."页\">$text</a>";
		}else{
			$str = "<a href=\"javascript:ajaxPage('$url','$ajaxObj');\"".($class?" class=\"$class\"":"")." title=\"第".$p."页\">$text</a>";
		}
		return $str;
	}
	private function _getUrl($p) {
		if ($this->_makeUrlFun) {
			$args = $this->_callArgs;
			$args[] = $p;
			$url = call_user_func_array($this->_makeUrlFun, $args);
		} else {
			if(1==$p){
				$url = str_replace(Fn::$config['url_delimiter'].'@', '', $this->_url);
			}else{
				$url = str_replace('@', $p, $this->_url);
			}
		}
		return $url;
	}
	/**
	 * 获取总页数
	 * @param mixed 
	 * @return void
	 */
	public function totalPage() {
		return $this->_totalPage;	
	}
	
	/**
	 * 获取分页HTML
	 * @param mixed 
	 * @return void
	 */
	public function html() {
		return $this->_html;	
	}
	
	/**
	 * 获取分页LIMIT
	 * @param mixed 
	 * @return void
	 */
	public function limit() {
		return $this->_limit;	
	}
	
	public function start() {
		return $this->_start;	
	}
	
	public function end() {
		return $this->_end;	
	}
}