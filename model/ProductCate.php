<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class ProductCate_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_product_cate','cat_id');
	}
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'cat_id DESC'){
		$rs = $this->db->table($this->table)
					->field($field)
					->where($where)
					->limit($limit)
					->order($order)
					->getAll();
		return $rs;
	}
	public function getTotalNum($where=array()){
		$rs = $this->db->table($this->table)
					->field("count(cat_id) as counter")
					->where($where)
					->getOne();
		return intval($rs['counter']);
	}
	
	/**
	 * 按att_id查找
	 * @param
	 * @return Array
	 */
	public function getById($id,$field='*'){
		$rs = $this->db->table($this->table)
				->field($field)
				->where("cat_id='".$id."'")
				->getOne();
		return $rs;
	}
	public function getOne($where){
		if(!$where)return false;
		$rs = $this->db->table($this->table)
				->field("*")
				->where($where)
				->getOne();
		return $rs;
	}
	public function setByWhere($where,$data)
	{
		if(!$where)return false;
		return $this->db->table($this->table)->where($where)->update($data);
	}
	
	public function getCateID($where,$type="str")
	{
		$rs = $this->db->table($this->table)
				->field('cat_id')
				->where($where)
				->getAll();
		$cat_id = '';
		if($rs){
			foreach ($rs as $v)
			{
				$cat_id .= $cat_id?','.$v['cat_id']:$v['cat_id'];
			}
		}
		if($type == 'arr')$cat_id = explode(',', $cat_id);
		return $cat_id;
	}
	
	public function setCache($type=1)
	{
		$rs = $this->db->table($this->table)
					->field("*")
					->where("cat_status=1")
					->getAll();
		$list = array(1=>array(),2=>array());
		if($rs)
		{
			foreach ($rs as $v)
			{
				if(!$v['cat_pid'])
				{
					$list[1][0][$v['cat_id']] = $v;
				}
				else
				{
					$list[1][$v['cat_pid']][$v['cat_id']]= $v;
				}
				$list[2][$v['cat_pid']] = $v;
			}
		}
		$this->cache->setToFile('product/cate/1', $list[1]);
		$this->cache->setToFile('product/cate/2', $list[2]);
		return $list[$type];
	}
	public function get($type=1)
	{
		$list = $this->cache->getFromFile('product/cate/'.$type);
		if (!$list)
		{
			$list = $this->setCache($type);
		}
		return $list;
	}
	
	public function getArrayByParentID($parent_id=0)
	{
		$rs = $this->get();
		if(!$this->id_arr)$this->id_arr[] = $parent_id;
		if($rs[$parent_id])
		{
			foreach ($rs[$parent_id] as $v)
			{
				$this->id_arr[] = $v['cat_id'];
				$this->getArrayByParentID($v['cat_id']);
			}
		}
		return $this->id_arr;
	}
	public function getTreeSelect($parent_id=0,$selected_id=0,$name='data[cat_id]',$id='cat_id')
	{
		$select = '<select name="'.$name.'" id="'.$id.'">';
		$select .= '<option value="">产品分类</option>';
		$select .= $this->getOptionByParentID($parent_id,$selected_id,$name,$id);
		$select .= '</select>';
		return $select;
	}
	public function getOptionByParentID($parent_id=0,$selected_id=0,$name='data[cat_id]',$id='cat_id')
	{
		$rs = $this->get();
		if($rs[$parent_id])
		{
			foreach ($rs[$parent_id] as $v)
			{
				$selected = '';
				if($selected_id == $v['cat_id'])$selected = " selected ";
				$str = "";
				for($i=0;$i<$v['cat_level'];$i++)
				{
					$str .= "　";
				}
				$str .= "├─";
				$select .= '<option value="'.$v['cat_id'].'" '.$selected.'>'.$str.$v['cat_name'].'</option>';
				$select .= $this->getOptionByParentID($v['cat_id'],$selected_id,$name,$id);
			}
		}
		return $select;
	}
	public function getTreeTable($where=array(),$class="table")
	{
		if($where)$this->cat_id = $this->getDepID($where,'arr');
		$tb = '<table width="100%" cellpadding="3" cellspacing="0" class="'.$class.'">'."\n";
		$tb .= '<tr>';
		$tb .= '<th>分类名称</th>';
		$tb .= '<th>编号</th>';
		$tb .= '<th>状态</th>';
		$tb .= '<th>操作</th>';
		$tb .= '</tr>'."\n";
		$tb .= $this->getTrByParentID(0,($where?true:false));
		$tb .= '</table>';
		return $tb;
	}
	public function getTrByParentID($parent_id=0,$cat_id=false)
	{
		$where['cat_pid'] = $parent_id;
		$rs = $this->db->table($this->table)
					->field("*")
					->where($where)
					->order("cat_id asc")
					->getAll();
		if($rs)
		{
			$conf_model = load_model('Config');
			foreach ($rs as $v)
			{
				$bln = true;
				if($cat_id)
				{
					if(!in_array($v['cat_id'],$this->cat_id))$bln = false;
				}
				if($bln)
				{
					$str = "";
					if($v['cat_level'] > 1)
					{
						for($i=1;$i<$v['cat_level'];$i++)
						{
						$str .= "　　";
						}
						$str .= "├─";
					}
					$tb .= '<tr class="list_cls list_cls_'.$v['cat_level'].'">';
					$tb .= '<td>'.$str.$v['cat_name'].'</td>';
					$tb .= '<td>'.$v['cat_no'].'</td>';
					$tb .= '<td><img src="'.Fn::$config['web_path'].'style/'.Fn::$config['template_skin'].'/images/'.$v['cat_status'].'.gif" /></td>';
					$tb .= '<td>
								<a href="index.php?c=Product&a=cateAdd&cat_id='.$v['cat_id'].'">修改</a> | 
								<a href="index.php?c=Product&a=cateAdd&cat_pid='.$v['cat_id'].'">添加下级分类</a>
							</td>';
					$tb .= '</tr>'."\n";
				}
				$tb .= $this->getTrByParentID($v['cat_id'],$cat_id);
			}
		}
		return $tb;
	}
}